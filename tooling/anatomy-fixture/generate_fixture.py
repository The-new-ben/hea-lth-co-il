"""Generate a non-production GLB for the Hea-lth anatomy-engine test harness.

This file exists only to prove WebGL loading, named mesh selection, layer
visibility, and the portal handoff. It is intentionally not medically accurate,
not photorealistic, not licensed for publication, and never part of the theme
package or live WordPress release.
"""

import math
import os

import bpy


OUTPUT = os.path.join(os.path.dirname(__file__), "anatomy-engine-test-fixture.glb")


def clear_scene():
    bpy.ops.object.select_all(action="SELECT")
    bpy.ops.object.delete(use_global=False)


def material(name, color, metallic=0.0, roughness=0.55, alpha=1.0):
    item = bpy.data.materials.new(name)
    item.use_nodes = True
    principled = item.node_tree.nodes.get("Principled BSDF")
    principled.inputs["Base Color"].default_value = (*color, alpha)
    principled.inputs["Roughness"].default_value = roughness
    principled.inputs["Metallic"].default_value = metallic
    if alpha < 1.0:
        principled.inputs["Alpha"].default_value = alpha
        item.surface_render_method = "DITHERED"
    return item


def sphere(name, location, scale, skin):
    bpy.ops.mesh.primitive_uv_sphere_add(segments=64, ring_count=32, location=location)
    item = bpy.context.active_object
    item.name = name
    item.scale = scale
    bpy.ops.object.transform_apply(location=False, rotation=False, scale=True)
    item.data.materials.append(skin)
    bpy.ops.object.shade_smooth()
    return item


def capsule(name, location, radius, depth, skin):
    bpy.ops.mesh.primitive_uv_sphere_add(segments=64, ring_count=32, location=location)
    item = bpy.context.active_object
    item.name = name
    item.scale = (radius, radius * 0.72, depth)
    bpy.ops.object.transform_apply(location=False, rotation=False, scale=True)
    item.data.materials.append(skin)
    bpy.ops.object.shade_smooth()
    return item


def cone(name, location, radius, depth, skin, rotation=(math.pi / 2, 0, 0)):
    bpy.ops.mesh.primitive_cone_add(vertices=48, radius1=radius, radius2=radius * 0.08, depth=depth, location=location, rotation=rotation)
    item = bpy.context.active_object
    item.name = name
    item.data.materials.append(skin)
    bpy.ops.object.shade_smooth()
    return item


def cylinder(name, location, radius, depth, skin, rotation=(0, 0, 0)):
    bpy.ops.mesh.primitive_cylinder_add(vertices=48, radius=radius, depth=depth, location=location, rotation=rotation)
    item = bpy.context.active_object
    item.name = name
    item.data.materials.append(skin)
    bpy.ops.object.shade_smooth()
    return item


def build_fixture():
    clear_scene()

    skin = material("fixture.skin", (0.38, 0.62, 0.54), roughness=0.52)
    highlight = material("fixture.nose", (0.72, 0.48, 0.24), metallic=0.08, roughness=0.42)
    organ = material("fixture.organ", (0.56, 0.14, 0.18), roughness=0.4)
    respiratory = material("fixture.respiratory", (0.18, 0.45, 0.64), metallic=0.05, roughness=0.35)
    skeletal = material("fixture.skeletal", (0.78, 0.72, 0.56), roughness=0.65)

    capsule("skin.outer", (0, 0, 0.25), 1.1, 2.25, skin)
    sphere("skin.head", (0, 0, 2.95), (0.72, 0.66, 0.85), skin)
    cone("face.nose.external", (0, -0.72, 3.02), 0.22, 0.58, highlight)
    cylinder("skin.left-arm", (-1.02, 0, 0.58), 0.18, 2.15, skin, rotation=(0, math.pi / 2.8, 0))
    cylinder("skin.right-arm", (1.02, 0, 0.58), 0.18, 2.15, skin, rotation=(0, -math.pi / 2.8, 0))
    cylinder("skin.left-leg", (-0.42, 0, -2.25), 0.26, 2.65, skin)
    cylinder("skin.right-leg", (0.42, 0, -2.25), 0.26, 2.65, skin)

    sphere("respiratory.left-lung", (-0.38, 0.06, 0.7), (0.42, 0.3, 0.68), respiratory)
    sphere("respiratory.right-lung", (0.38, 0.06, 0.7), (0.42, 0.3, 0.68), respiratory)
    sphere("respiratory.nasal-cavity", (0, -0.38, 3.0), (0.14, 0.1, 0.16), respiratory)
    cylinder("skeleton.spine", (0, 0.12, 0.1), 0.09, 3.7, skeletal)
    sphere("organ.heart", (0.16, -0.08, 0.45), (0.24, 0.2, 0.32), organ)

    bpy.ops.export_scene.gltf(
        filepath=OUTPUT,
        export_format="GLB",
        export_materials="EXPORT",
        export_apply=True,
        export_normals=True,
        export_tangents=True,
        export_yup=True,
    )


if __name__ == "__main__":
    build_fixture()
