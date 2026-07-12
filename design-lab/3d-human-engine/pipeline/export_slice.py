import bpy, sys, os

argv = sys.argv[sys.argv.index("--")+1:]
collection_name = argv[0]
out_dir = argv[1]
os.makedirs(out_dir, exist_ok=True)

def all_objs(coll):
    objs = list(coll.objects)
    for c in coll.children:
        objs += all_objs(c)
    return objs

target = bpy.data.collections.get(collection_name)
if not target:
    print("COLLECTION NOT FOUND:", collection_name); sys.exit(1)

objs = [o for o in all_objs(target) if o.type == 'MESH']
print(f"slice '{collection_name}': {len(objs)} meshes")

# Make everything visible + selectable, deselect all first
bpy.ops.object.select_all(action='DESELECT')
for o in bpy.data.objects:
    o.hide_set(False); o.hide_viewport = False; o.select_set(False)

tris = 0
for o in objs:
    o.hide_set(False); o.hide_viewport = False
    o.select_set(True)
    tris += sum(len(p.vertices) - 2 for p in o.data.polygons)
bpy.context.view_layer.objects.active = objs[0]
print("slice triangles (full):", tris)

# --- DETAIL LOD: full resolution, Draco compressed ---
detail_path = os.path.join(out_dir, "skeletal-detail.glb")
bpy.ops.export_scene.gltf(
    filepath=detail_path,
    export_format='GLB',
    use_selection=True,
    export_draco_mesh_compression_enable=True,
    export_draco_mesh_compression_level=6,
    export_yup=True,
    export_apply=True,
)
print("DETAIL_BYTES", os.path.getsize(detail_path))

# --- PREVIEW LOD: decimated for homepage ---
for o in objs:
    m = o.modifiers.new(name="dec", type='DECIMATE')
    m.ratio = 0.12
preview_path = os.path.join(out_dir, "skeletal-preview.glb")
bpy.ops.export_scene.gltf(
    filepath=preview_path,
    export_format='GLB',
    use_selection=True,
    export_draco_mesh_compression_enable=True,
    export_draco_mesh_compression_level=8,
    export_yup=True,
    export_apply=True,
)
print("PREVIEW_BYTES", os.path.getsize(preview_path))
print("DONE")
