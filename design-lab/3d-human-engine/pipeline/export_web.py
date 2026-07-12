"""Web export pipeline for the Hea-lth 3D anatomy engine (Blender 5.1.x, headless).

Run standalone for one system:

    blender --background Startup.blend --python export_web.py -- "1: Skeletal system" out_dir

It recursively collects the MESH objects of a top-level Z-Anatomy collection, drops the
baked-in floating text/label objects, bakes a clean bone-white PBR material into the
geometry, and writes two Draco-compressed GLB LODs:

  * <slug>-detail.glb   full-resolution geometry, Draco level 6
  * <slug>-preview.glb  Decimate(ratio 0.15), Draco level 8

Both are exported y-up with modifiers applied. Mesh count, triangle count, and byte
size are printed for each LOD as a machine-readable ``RESULT {json}`` line.

The module is import-safe: the ``__main__`` guard means render_web.py and
export_layered_figures.py can reuse these helpers inside a single Blender session.
"""

import bpy
import json
import os
import re
import sys

# Canonical system -> URL slug map for the layered figure set.
SYSTEMS = [
    ("1: Skeletal system", "skeletal"),
    ("7: Nervous system & Sense organs", "nervous"),
    ("5: Cardiovascular system", "cardiovascular"),
    ("8: Visceral systems", "visceral"),
    ("4: Muscular system", "muscular"),
]
SLUG_BY_COLLECTION = {name: slug for name, slug in SYSTEMS}

# Bone-white PBR override (medical-atlas presentation, per design direction).
BONE_WHITE_RGBA = (0.93, 0.90, 0.83, 1.0)
BONE_WHITE_ROUGHNESS = 0.55

DETAIL_DRACO_LEVEL = 6
PREVIEW_DRACO_LEVEL = 8
PREVIEW_DECIMATE_RATIO = 0.15

# Z-Anatomy bakes floating 3D text-label objects into each collection. Two shapes:
#   * zero-polygon leader/guide helpers (caught by the polygon check), and
#   * solid extruded TEXT captions that DO carry geometry, named like
#     "Skeletal system.g" / "Muscular system.g" (a space + ".g" suffix). The old
#     "system[a-z]?$" pattern missed these because ".g" follows "system", so a
#     "SKELETAL SYSTEM" caption leaked into the mesh set and the GLB.
# Rules: drop non-mesh, drop zero-polygon, drop system/text/label/title/caption
# names, and drop the Z-Anatomy annotation suffixes ".g" (guide/caption geometry),
# ".j" (join/leader), ".t" (text). Real anatomy uses ".l"/".r" laterality, never
# these — verified against the exported node census.
_LABEL_NAME_RE = re.compile(r"system[a-z]?$|text|label|title|caption", re.IGNORECASE)
_ANNOTATION_SUFFIX_RE = re.compile(r"\.(?:g|j|t)$", re.IGNORECASE)

_BONE_MATERIAL = None


def slugify(name):
    """Fallback slug for a collection name not in SLUG_BY_COLLECTION."""
    base = re.sub(r"^[0-9]+\s*:\s*", "", name).lower()
    base = re.sub(r"[^a-z0-9]+", "-", base).strip("-")
    return base or "system"


def _all_objects(collection):
    objs = list(collection.objects)
    for child in collection.children:
        objs += _all_objects(child)
    return objs


def is_label_object(obj):
    """True when an object is a baked text/label artifact rather than anatomy."""
    if obj.type != "MESH":
        return True
    if obj.data is None or len(obj.data.polygons) == 0:
        return True
    if _LABEL_NAME_RE.search(obj.name):
        return True
    if _ANNOTATION_SUFFIX_RE.search(obj.name):
        return True
    return False


def collect_meshes(collection_name):
    """Return the filtered anatomy meshes of a top-level collection."""
    collection = bpy.data.collections.get(collection_name)
    if collection is None:
        raise KeyError("collection not found: %r" % collection_name)
    kept = [o for o in _all_objects(collection) if not is_label_object(o)]
    # De-duplicate while preserving order (nested collections can repeat objects).
    seen = set()
    unique = []
    for o in kept:
        if o.name not in seen:
            seen.add(o.name)
            unique.append(o)
    return unique


def excluded_label_names(collection_name):
    """The object names dropped by the label filter (for QA reporting)."""
    collection = bpy.data.collections.get(collection_name)
    if collection is None:
        return []
    return sorted({o.name for o in _all_objects(collection) if is_label_object(o)})


def bone_white_material():
    """Create (once) the shared bone-white Principled material."""
    global _BONE_MATERIAL
    if _BONE_MATERIAL is not None:
        return _BONE_MATERIAL
    mat = bpy.data.materials.new("HL_BoneWhite")
    mat.use_nodes = True
    bsdf = mat.node_tree.nodes.get("Principled BSDF")
    bsdf.inputs["Base Color"].default_value = BONE_WHITE_RGBA
    bsdf.inputs["Roughness"].default_value = BONE_WHITE_ROUGHNESS
    if "Specular IOR Level" in bsdf.inputs:
        bsdf.inputs["Specular IOR Level"].default_value = 0.3
    _BONE_MATERIAL = mat
    return mat


def apply_bone_white(objs):
    mat = bone_white_material()
    for o in objs:
        o.data.materials.clear()
        o.data.materials.append(mat)
        # Force DATA-linked slots so an object-level material override cannot mask
        # the bone-white base color at render time.
        for slot in o.material_slots:
            slot.link = "DATA"


def select_only(objs):
    """Deselect everything, then select and un-hide exactly ``objs``."""
    for o in bpy.data.objects:
        try:
            o.select_set(False)
        except RuntimeError:
            pass
    selected = 0
    for o in objs:
        o.hide_set(False)
        o.hide_viewport = False
        o.hide_select = False
        try:
            o.select_set(True)
            selected += 1
        except RuntimeError:
            pass
    if objs:
        bpy.context.view_layer.objects.active = objs[0]
    return selected


def count_triangles(objs, evaluated=False):
    """Triangle total. ``evaluated=True`` counts post-modifier (e.g. after decimate)."""
    if not evaluated:
        return sum(
            sum(len(p.vertices) - 2 for p in o.data.polygons) for o in objs
        )
    deps = bpy.context.evaluated_depsgraph_get()
    total = 0
    for o in objs:
        ev = o.evaluated_get(deps)
        me = ev.to_mesh()
        total += sum(len(p.vertices) - 2 for p in me.polygons)
        ev.to_mesh_clear()
    return total


def _export_glb(path, draco_level):
    bpy.ops.export_scene.gltf(
        filepath=path,
        export_format="GLB",
        use_selection=True,
        export_draco_mesh_compression_enable=True,
        export_draco_mesh_compression_level=draco_level,
        export_yup=True,
        export_apply=True,
    )
    return os.path.getsize(path)


def export_system(collection_name, out_dir, slug=None):
    """Export detail + preview LODs for one system. Returns a stats dict."""
    slug = slug or SLUG_BY_COLLECTION.get(collection_name) or slugify(collection_name)
    os.makedirs(out_dir, exist_ok=True)

    objs = collect_meshes(collection_name)
    if not objs:
        raise RuntimeError("no anatomy meshes after label filter for %r" % collection_name)
    apply_bone_white(objs)
    selected = select_only(objs)

    # --- detail LOD: full resolution, Draco 6 ---
    detail_tris = count_triangles(objs, evaluated=False)
    detail_path = os.path.join(out_dir, "%s-detail.glb" % slug)
    detail_bytes = _export_glb(detail_path, DETAIL_DRACO_LEVEL)

    # --- preview LOD: Decimate 0.15, Draco 8 ---
    added = []
    for o in objs:
        m = o.modifiers.new("HL_dec", "DECIMATE")
        m.ratio = PREVIEW_DECIMATE_RATIO
        added.append((o, m))
    try:
        preview_tris = count_triangles(objs, evaluated=True)
    except Exception as exc:  # pragma: no cover - defensive
        print("WARN preview tri count failed:", exc)
        preview_tris = None
    select_only(objs)
    preview_path = os.path.join(out_dir, "%s-preview.glb" % slug)
    preview_bytes = _export_glb(preview_path, PREVIEW_DRACO_LEVEL)
    for o, m in added:
        try:
            o.modifiers.remove(m)
        except Exception:
            pass

    stats = {
        "slug": slug,
        "collection": collection_name,
        "meshes": len(objs),
        "selected": selected,
        "excluded_labels": len(excluded_label_names(collection_name)),
        "detail_tris": detail_tris,
        "detail_bytes": detail_bytes,
        "detail_path": detail_path.replace("\\", "/"),
        "preview_tris": preview_tris,
        "preview_bytes": preview_bytes,
        "preview_path": preview_path.replace("\\", "/"),
    }
    print("RESULT", json.dumps(stats))
    return stats


if __name__ == "__main__":
    argv = sys.argv[sys.argv.index("--") + 1:]
    collection_name = argv[0]
    out_dir = argv[1]
    export_system(collection_name, out_dir)
    print("DONE")
