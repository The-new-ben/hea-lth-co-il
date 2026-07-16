"""Generate the layered-figure v2 manifest (skeleton + muscles, one asset).

    python generate_layered_manifest_v2.py <stats_json> <default_manifest_out> <example_manifest_out>

Reads the v1 default manifest as the base (license, review, QA blocks and the
19 skeletal structures survive verbatim), swaps the asset LODs to the combined
layered-figure GLBs, adds the muscular layer + curated muscular structures
whose mesh ids are validated against the export's real node list.
"""

import io
import json
import os
import re
import sys

MUSCLE_GROUPS = [
    ("anatomy:pectorals", "חזה", "Pectoral muscles", "movement", r"pectoralis_(major|minor)"),
    ("anatomy:abdominals", "בטן", "Abdominal muscles", "movement", r"(rectus_abdominis|external_oblique|internal_oblique|transversus_abdominis)"),
    ("anatomy:back-muscles", "גב", "Back muscles", "movement", r"(latissimus_dorsi|trapezius|iliocostalis|longissimus|spinalis_muscle|rhomboid)"),
    ("anatomy:deltoid", "כתף", "Deltoid", "movement", r"deltoid_(muscle|fascia)|_of_deltoid"),
    ("anatomy:biceps", "זרוע קדמית", "Biceps brachii", "movement", r"biceps_brachii(?!.*bursa)"),
    ("anatomy:triceps", "זרוע אחורית", "Triceps brachii", "movement", r"triceps_brachii(?!.*bursa)"),
    ("anatomy:forearm-muscles", "אמה", "Forearm muscles", "movement", r"(brachioradialis|flexor_carpi|extensor_carpi|pronator|supinator)"),
    ("anatomy:quadriceps", "ירך קדמית", "Quadriceps", "movement", r"(rectus_femoris|vastus_(lateralis|medialis|intermedius))"),
    ("anatomy:hamstrings", "ירך אחורית", "Hamstrings", "movement", r"(biceps_femoris(?!.*bursa)|semitendinosus(?!.*bursa)|semimembranosus(?!.*bursa))"),
    ("anatomy:calf", "שוק", "Calf muscles", "movement", r"(gastrocnemius(?!.*bursa)|soleus)"),
    ("anatomy:gluteals", "ישבן", "Gluteal muscles", "movement", r"gluteus_(maximus|medius|minimus)"),
    ("anatomy:neck-muscles", "צוואר", "Neck muscles", "movement", r"(sternocleidomastoid|scalenus|splenius)"),
    ("anatomy:lips", "שפתיים", "Lips (orbicularis oris)", "skin-face", r"orbicularis_oris"),
    ("anatomy:facial-muscles", "שרירי הפנים", "Facial muscles", "skin-face", r"(orbicularis|zygomaticus|frontalis_muscle|buccinator|nasalis|procerus|depressor_(anguli|labii|septi)|levator_labii)"),
    ("anatomy:jaw-muscles", "לסת", "Jaw muscles", "skin-face", r"(masseter|temporalis_muscle|pterygoid_muscle)"),
]

CONTEXT_BY_REGION = {
    "movement": {
        "id": "musculoskeletal",
        "labelHe": "שריר ותנועה",
        "resolverEntityIds": {
            "topics": ["topic:musculoskeletal-health"],
            "specialties": ["specialty:orthopedics"],
            "treatments": ["treatment:physiotherapy-consultation"],
            "equipmentCategories": ["equipment:orthopedic-support"],
        },
    },
    "skin-face": {
        "id": "facial-structure",
        "labelHe": "מבנה הפנים",
        "resolverEntityIds": {
            "topics": ["topic:facial-anatomy"],
            "specialties": ["specialty:plastic-surgery"],
            "treatments": ["treatment:aesthetic-consultation"],
            "equipmentCategories": ["equipment:aesthetic-devices"],
        },
    },
}


def main():
    stats_path, default_out, example_out = sys.argv[1], sys.argv[2], sys.argv[3]
    stats = json.load(io.open(stats_path, encoding="utf-8"))
    base = json.load(io.open(default_out, encoding="utf-8"))

    muscular = stats["muscular_meshes"]
    skeletal = stats["skeletal_meshes"]
    lower = {m.lower(): m for m in muscular}

    group_ids = {g[0] for g in MUSCLE_GROUPS}
    # Idempotent: drop previously generated muscle groups so re-runs never duplicate.
    structures = [s for s in base["structures"] if s["id"] not in group_ids]
    used = set()
    added = []
    for sid, he, en, region, pattern in MUSCLE_GROUPS:
        rx = re.compile(pattern)
        mesh_ids = sorted({orig for low, orig in lower.items() if rx.search(low)})
        mesh_ids = [m for m in mesh_ids if m not in used]
        if not mesh_ids:
            raise SystemExit("no meshes matched for %s (%s)" % (sid, pattern))
        used.update(mesh_ids)
        structures.append(
            {
                "id": sid,
                "meshIds": mesh_ids,
                "labels": {"he": he, "en": en},
                "regionId": region,
                "contexts": [CONTEXT_BY_REGION[region]],
            }
        )
        added.append((sid, len(mesh_ids)))

    base["modelId"] = "z-anatomy-layered-v2"
    base["version"] = "2.0.0"
    base["updated"] = "2026-07-13"
    root = default_out.split("plugin-src")[0]
    detail_bytes = os.path.getsize(root + "theme-src/hea-lth-portal/assets/models/layered-figure-detail.glb")
    preview_bytes = os.path.getsize(root + "theme-src/hea-lth-portal/assets/models/layered-figure-preview.glb")
    base["asset"]["lods"] = [
        {"id": "lod-0", "path": "/wp-content/themes/hea-lth-portal/assets/models/layered-figure-preview.glb", "purpose": "mobile", "triangleCount": stats["preview_tris"], "compressedBytes": preview_bytes},
        {"id": "lod-1", "path": "/wp-content/themes/hea-lth-portal/assets/models/layered-figure-detail.glb", "purpose": "desktop", "triangleCount": stats["detail_tris"], "compressedBytes": detail_bytes},
        {"id": "lod-2", "path": "/wp-content/themes/hea-lth-portal/assets/models/layered-figure-detail.glb", "purpose": "detail", "triangleCount": stats["detail_tris"], "compressedBytes": detail_bytes},
    ]
    base["layers"] = [
        {"id": "muscular-system", "label": "מערכת השרירים", "kind": "system", "defaultVisible": True, "meshIds": muscular},
        {"id": "skeletal-system", "label": "מערכת השלד", "kind": "system", "defaultVisible": True, "meshIds": skeletal},
    ]
    base["structures"] = structures

    for out in (default_out, example_out):
        with io.open(out, "w", encoding="utf-8") as fh:
            json.dump(base, fh, ensure_ascii=False, indent=1)
            fh.write("\n")

    print("structures:", len(structures), "| muscular groups:", added)
    print("layers:", [(l["id"], len(l["meshIds"])) for l in base["layers"]])


if __name__ == "__main__":
    main()
