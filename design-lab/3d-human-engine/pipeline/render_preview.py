import bpy, sys, math, os
from mathutils import Vector

argv = sys.argv[sys.argv.index("--")+1:]
collection_name, out_png = argv[0], argv[1]

def all_objs(coll):
    objs = list(coll.objects)
    for c in coll.children:
        objs += all_objs(c)
    return objs

target = bpy.data.collections[collection_name]
objs = [o for o in all_objs(target) if o.type == 'MESH']

# hide everything, show only slice
for o in bpy.data.objects:
    o.hide_render = True
for o in objs:
    o.hide_render = False

# bounds
mn = Vector((1e9,1e9,1e9)); mx = Vector((-1e9,-1e9,-1e9))
for o in objs:
    for corner in o.bound_box:
        w = o.matrix_world @ Vector(corner)
        for i in range(3):
            mn[i]=min(mn[i],w[i]); mx[i]=max(mx[i],w[i])
center = (mn+mx)/2
size = (mx-mn)

# camera front view (-Y), framing height
cam_data = bpy.data.cameras.new("C"); cam = bpy.data.objects.new("C", cam_data)
bpy.context.scene.collection.objects.link(cam)
dist = max(size.x, size.z) * 2.4
cam.location = center + Vector((0, -dist, size.z*0.05))
cam.rotation_euler = (math.radians(90), 0, 0)
cam_data.lens = 50
bpy.context.scene.camera = cam

# lighting
for kx,ky,e in [(-1,-1,4.0),(1,-1,3.0),(0,1,2.0)]:
    l = bpy.data.lights.new("L","SUN"); l.energy=e
    lo = bpy.data.objects.new("L",l); bpy.context.scene.collection.objects.link(lo)
    lo.location = center+Vector((kx*dist,ky*dist,dist));
    lo.rotation_euler=(math.radians(60),0,math.radians(45*kx))

sc = bpy.context.scene
sc.render.engine = 'BLENDER_EEVEE'
sc.eevee.taa_render_samples = 24
sc.render.resolution_x = 900; sc.render.resolution_y = 1300
sc.render.film_transparent = True
sc.render.filepath = out_png
try:
    bpy.data.worlds[0].node_tree.nodes['Background'].inputs[1].default_value = 1.0
except Exception: pass
bpy.ops.render.render(write_still=True)
print("RENDERED", out_png, os.path.getsize(out_png) if os.path.exists(out_png) else "MISSING")
