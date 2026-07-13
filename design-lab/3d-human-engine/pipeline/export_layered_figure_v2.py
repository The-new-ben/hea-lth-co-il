"""Export the layered figure: skeleton + muscles in ONE GLB pair.

    blender --background Startup.blend --python export_layered_figure_v2.py -- <out_dir>

Produces layered-figure-detail.glb (full res, Draco 6) and
layered-figure-preview.glb (Decimate PREVIEW_RATIO, Draco 8), plus
layered-figure-stats.json carrying the per-system mesh-name lists the manifest
generator consumes. Muscles get an anatomical muscle-red material; bones keep
the bone-white material, so the figure reads as a human body (ecorche), not a
statue.
"""

import json
import os
import sys

sys.path.append(os.path.dirname(os.path.abspath(__file__)))
import export_web as ew  # noqa: E402

import bpy  # noqa: E402

SKELETAL = "1: Skeletal system"
MUSCULAR = "4: Muscular system"
PREVIEW_RATIO = 0.05
MUSCLE_RGBA = (0.42, 0.11, 0.10, 1.0)
MUSCLE_ROUGHNESS = 0.62
SKIN_RGBA = (0.80, 0.56, 0.44, 1.0)
SKIN_ROUGHNESS = 0.5
SKIN_NAME_PATTERN = ("subcutaneous", "superficial_fascia", "superficial fascia", "fatty")

_MUSCLE_MATERIAL = None
_SKIN_MATERIAL = None


def _principled(name, rgba, roughness, specular):
    mat = bpy.data.materials.new(name)
    mat.use_nodes = True
    bsdf = mat.node_tree.nodes.get("Principled BSDF")
    bsdf.inputs["Base Color"].default_value = rgba
    bsdf.inputs["Roughness"].default_value = roughness
    if "Specular IOR Level" in bsdf.inputs:
        bsdf.inputs["Specular IOR Level"].default_value = specular
    return mat


def muscle_material():
    global _MUSCLE_MATERIAL
    if _MUSCLE_MATERIAL is None:
        _MUSCLE_MATERIAL = _principled("HL_MuscleRed", MUSCLE_RGBA, MUSCLE_ROUGHNESS, 0.25)
    return _MUSCLE_MATERIAL


def skin_material():
    global _SKIN_MATERIAL
    if _SKIN_MATERIAL is None:
        _SKIN_MATERIAL = _principled("HL_Skin", SKIN_RGBA, SKIN_ROUGHNESS, 0.3)
    return _SKIN_MATERIAL


def is_skin_envelope(name):
    low = name.lower()
    return any(token in low for token in SKIN_NAME_PATTERN)


def apply_material(objs, mat):
    for o in objs:
        o.data.materials.clear()
        o.data.materials.append(mat)
        for slot in o.material_slots:
            slot.link = "DATA"


def main():
    argv = sys.argv[sys.argv.index("--") + 1:]
    out_dir = argv[0]
    os.makedirs(out_dir, exist_ok=True)

    skeletal_objs = ew.collect_meshes(SKELETAL)
    muscular_objs = ew.collect_meshes(MUSCULAR)

    skeletal_names = {o.name for o in skeletal_objs}
    muscular_objs = [o for o in muscular_objs if o.name not in skeletal_names]

    skin_objs = [o for o in muscular_objs if is_skin_envelope(o.name)]
    flesh_objs = [o for o in muscular_objs if not is_skin_envelope(o.name)]

    apply_material(skeletal_objs, ew.bone_white_material())
    apply_material(flesh_objs, muscle_material())
    apply_material(skin_objs, skin_material())

    combined = skeletal_objs + muscular_objs
    ew.select_only(combined)

    detail_tris = ew.count_triangles(combined, evaluated=False)
    detail_path = os.path.join(out_dir, "layered-figure-detail.glb")
    detail_bytes = ew._export_glb(detail_path, ew.DETAIL_DRACO_LEVEL)

    added = []
    for o in combined:
        m = o.modifiers.new("HL_dec", "DECIMATE")
        m.ratio = PREVIEW_RATIO
        added.append((o, m))
    preview_tris = ew.count_triangles(combined, evaluated=True)
    ew.select_only(combined)
    preview_path = os.path.join(out_dir, "layered-figure-preview.glb")
    preview_bytes = ew._export_glb(preview_path, ew.PREVIEW_DRACO_LEVEL)
    for o, m in added:
        try:
            o.modifiers.remove(m)
        except Exception:
            pass

    stats = {
        "detail_tris": detail_tris,
        "detail_bytes": detail_bytes,
        "preview_tris": preview_tris,
        "preview_bytes": preview_bytes,
        "skeletal_meshes": sorted(o.name for o in skeletal_objs),
        "muscular_meshes": sorted(o.name for o in muscular_objs),
    }
    with open(os.path.join(out_dir, "layered-figure-stats.json"), "w", encoding="utf-8") as fh:
        json.dump(stats, fh, indent=1)
    print("RESULT", json.dumps({k: v for k, v in stats.items() if not isinstance(v, list)}))
    print("DONE")


if __name__ == "__main__":
    main()
