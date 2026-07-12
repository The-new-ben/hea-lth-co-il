import bpy, sys

scene = bpy.context.scene
mesh_objs = [o for o in bpy.data.objects if o.type == 'MESH']
print("=== Z-ANATOMY ANALYSIS ===")
print("total objects:", len(bpy.data.objects))
print("mesh objects:", len(mesh_objs))
print("collections:", len(bpy.data.collections))

total_tris = 0
for o in mesh_objs:
    me = o.data
    tris = sum(len(p.vertices) - 2 for p in me.polygons)
    total_tris += tris
print("TOTAL TRIANGLES (approx):", total_tris)

# top collections = anatomical systems
print("=== TOP COLLECTIONS ===")
for c in bpy.data.collections:
    n_mesh = len([o for o in c.objects if o.type == 'MESH'])
    if n_mesh:
        print(f"  {c.name}: {n_mesh} direct meshes")

# sample object names (the semantic labels)
print("=== SAMPLE MESH NAMES ===")
for o in mesh_objs[:40]:
    print("  ", o.name)
