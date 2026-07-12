"""EEVEE front-view QA render for one anatomy system (Blender 5.1.x, headless).

    blender --background Startup.blend --python render_web.py -- "1: Skeletal system" out.png

Renders the label-filtered slice with the shipped bone-white PBR material, a 3-point
studio sun rig, a transparent background, at 900x1300. Reuses export_web helpers so the
render reflects exactly the geometry+material the detail GLB carries.
"""

import bpy
import math
import os
import sys

sys.path.append(os.path.dirname(os.path.abspath(__file__)))
import export_web as ew  # noqa: E402

from mathutils import Vector  # noqa: E402

RES_X = 900
RES_Y = 1300
_TEMP_PREFIX = "HL_RIG_"


def _clear_temp_rig():
    for o in list(bpy.data.objects):
        if o.name.startswith(_TEMP_PREFIX):
            bpy.data.objects.remove(o, do_unlink=True)


def _bounds(objs):
    mn = Vector((1e9, 1e9, 1e9))
    mx = Vector((-1e9, -1e9, -1e9))
    for o in objs:
        for corner in o.bound_box:
            w = o.matrix_world @ Vector(corner)
            for i in range(3):
                mn[i] = min(mn[i], w[i])
                mx[i] = max(mx[i], w[i])
    return mn, mx


def render_system(collection_name, out_png):
    objs = ew.collect_meshes(collection_name)
    if not objs:
        raise RuntimeError("no anatomy meshes after label filter for %r" % collection_name)
    ew.apply_bone_white(objs)

    # Render only this slice.
    for o in bpy.data.objects:
        try:
            o.hide_render = True
        except Exception:
            pass
    for o in objs:
        o.hide_render = False

    _clear_temp_rig()
    mn, mx = _bounds(objs)
    center = (mn + mx) / 2
    size = mx - mn

    # Front view: camera on -Y looking toward +Y.
    cam_data = bpy.data.cameras.new(_TEMP_PREFIX + "cam")
    cam = bpy.data.objects.new(_TEMP_PREFIX + "cam", cam_data)
    bpy.context.scene.collection.objects.link(cam)
    dist = max(size.x, size.z) * 2.4 or 2.0
    cam.location = center + Vector((0, -dist, size.z * 0.05))
    cam.rotation_euler = (math.radians(90), 0, 0)
    cam_data.lens = 50
    bpy.context.scene.camera = cam

    # 3-point studio sun rig. A SUN's direction is its rotation (location is
    # irrelevant); emitting toward +Y lights the camera-facing (front, -Y) faces.
    # (rx, rz, energy): key from top-front, side fill, and a top rim.
    rig = [
        (math.radians(48), math.radians(34), 4.0),    # key: upper-left-front (form)
        (math.radians(70), math.radians(-52), 1.3),   # fill: lower-right-front
        (math.radians(14), math.radians(0), 1.6),     # top rim (separation from bg)
    ]
    for rx, rz, energy in rig:
        light = bpy.data.lights.new(_TEMP_PREFIX + "l", "SUN")
        light.energy = energy
        lo = bpy.data.objects.new(_TEMP_PREFIX + "l", light)
        bpy.context.scene.collection.objects.link(lo)
        lo.location = center + Vector((0, -dist, dist))
        lo.rotation_euler = (rx, 0, rz)

    scene = bpy.context.scene
    scene.render.engine = "BLENDER_EEVEE"
    # Z-Anatomy's scene ships a compositor node tree that flattens the render onto a
    # fixed (white) backdrop and ignores the world background. Bypass it so the raw
    # studio render (dark world + shaded bone-white) reaches the saved PNG.
    scene.use_nodes = False
    scene.render.use_compositing = False
    try:
        scene.eevee.taa_render_samples = 24
    except Exception:
        pass
    scene.render.resolution_x = RES_X
    scene.render.resolution_y = RES_Y
    # NOTE: a bone-white (~0.93) model on a transparent background is invisible on
    # white viewers, which defeats visual QA. We render on a solid mid-gray studio
    # backdrop so the true shaded material is legible (BioDigital-style). This is a
    # deliberate, documented deviation from a transparent background.
    scene.render.film_transparent = False
    scene.render.filepath = out_png

    # Z-Anatomy ships with Freestyle ink contour lines enabled (the "engraving"
    # look). Disable for a clean shaded medical-atlas surface. Add EEVEE ambient
    # occlusion for anatomical depth in crevices.
    scene.render.use_freestyle = False
    try:
        scene.eevee.use_gtao = True
    except Exception:
        pass

    # Standard view transform: the Blender default (AgX) desaturates and darkens
    # midtones, which turned the cream bone-white into a muddy brown. Standard keeps
    # the true material color. Paired with a bright neutral world for even fill.
    try:
        scene.view_settings.view_transform = "Standard"
    except Exception:
        pass
    # Rebuild the world node tree from scratch. The Z-Anatomy world has no plain
    # "Background" node, so editing nodes.get("Background") silently no-ops and the
    # bright default world washes out the bone-white model. A fresh dark studio
    # world guarantees a low-ambient backdrop the model reads against.
    world = bpy.data.worlds.new("HL_Studio")
    scene.world = world
    world.use_nodes = True
    nt = world.node_tree
    nt.nodes.clear()
    bg = nt.nodes.new("ShaderNodeBackground")
    out = nt.nodes.new("ShaderNodeOutputWorld")
    nt.links.new(bg.outputs[0], out.inputs[0])
    bg.inputs[0].default_value = (0.18, 0.185, 0.2, 1.0)
    bg.inputs[1].default_value = 1.0

    visible = sum(1 for o in bpy.data.objects if o.type == "MESH" and not o.hide_render)
    print("RENDER_CFG film_transparent=%s view=%s freestyle=%s compositing=%s world_bg=%.2f visible_meshes=%d" % (
        scene.render.film_transparent, scene.view_settings.view_transform,
        scene.render.use_freestyle, scene.render.use_compositing,
        bg.inputs[0].default_value[0], visible))

    bpy.ops.render.render(write_still=True)
    size_bytes = os.path.getsize(out_png) if os.path.exists(out_png) else 0
    print("RENDERED", out_png.replace("\\", "/"), size_bytes)
    return out_png


if __name__ == "__main__":
    argv = sys.argv[sys.argv.index("--") + 1:]
    render_system(argv[0], argv[1])
    print("DONE")
