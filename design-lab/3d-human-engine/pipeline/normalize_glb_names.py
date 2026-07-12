"""Normalize glTF/GLB node names to the platform gate's mesh-id charset.

The Hea-lth anatomy gate (`sanitize_mesh_ids` in the platform plugin) only accepts
mesh ids matching ``^[A-Za-z0-9_.:-]+$`` — spaces are rejected. Z-Anatomy names
most structures with spaces ("Frontal bone.l"), which locks them out of the
click-to-identify structure map. This step rewrites node ``name`` fields to a
gate-safe form (any run of disallowed characters -> a single underscore),
preserving laterality suffixes (".l"/".r") and dots.

It touches ONLY the JSON chunk's node names. Geometry, buffers, bufferViews,
materials, and the Draco (``KHR_draco_mesh_compression``) payload in the BIN
chunk are left byte-for-byte unchanged — so this is safe for Draco-compressed
GLBs and does not alter triangle counts.

Usage:
    python normalize_glb_names.py in.glb out.glb
"""

import json
import re
import struct
import sys

_DISALLOWED = re.compile(r"[^A-Za-z0-9_.:-]+")
_GATE_SAFE = re.compile(r"^[A-Za-z0-9_.:-]+$")


def normalize_name(name):
    """Return a gate-safe version of a node name (collapse bad runs to '_')."""
    cleaned = _DISALLOWED.sub("_", name).strip("_")
    return cleaned or "node"


def _read_glb(path):
    with open(path, "rb") as handle:
        data = handle.read()
    magic, version, _total = struct.unpack_from("<4sII", data, 0)
    if magic != b"glTF":
        raise ValueError("not a GLB: %r" % path)
    offset = 12
    chunks = []
    while offset < len(data):
        clen, ctype = struct.unpack_from("<I4s", data, offset)
        offset += 8
        chunks.append([ctype, data[offset:offset + clen]])
        offset += clen
    return version, chunks


def _write_glb(path, version, chunks):
    body = b""
    for ctype, payload in chunks:
        pad = (4 - (len(payload) % 4)) % 4
        if pad:
            payload = payload + (b" " * pad if ctype == b"JSON" else b"\x00" * pad)
        body += struct.pack("<I4s", len(payload), ctype) + payload
    header = struct.pack("<4sII", b"glTF", version, 12 + len(body))
    with open(path, "wb") as handle:
        handle.write(header + body)


def normalize_glb(in_path, out_path):
    version, chunks = _read_glb(in_path)
    if not chunks or chunks[0][0] != b"JSON":
        raise ValueError("first chunk is not JSON: %r" % in_path)

    gltf = json.loads(chunks[0][1].decode("utf-8"))
    nodes = gltf.get("nodes", [])

    renamed = 0
    seen = {}
    collisions = 0
    for node in nodes:
        name = node.get("name")
        if not name or _GATE_SAFE.match(name):
            if name:
                seen[name] = seen.get(name, 0) + 1
            continue
        new_name = normalize_name(name)
        if seen.get(new_name):
            collisions += 1
        seen[new_name] = seen.get(new_name, 0) + 1
        node["name"] = new_name
        renamed += 1

    chunks[0][1] = json.dumps(gltf, separators=(",", ":"), ensure_ascii=False).encode("utf-8")
    _write_glb(out_path, version, chunks)

    unsafe = [n.get("name", "") for n in nodes if n.get("name") and not _GATE_SAFE.match(n["name"])]
    result = {
        "in": in_path.replace("\\", "/"),
        "out": out_path.replace("\\", "/"),
        "nodes": len(nodes),
        "renamed": renamed,
        "name_collisions": collisions,
        "still_unsafe": len(unsafe),
    }
    print("RESULT", json.dumps(result))
    return result


if __name__ == "__main__":
    normalize_glb(sys.argv[1], sys.argv[2])
    print("DONE")
