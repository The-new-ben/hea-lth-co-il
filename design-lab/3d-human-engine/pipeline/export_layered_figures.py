"""Export + render the full layered figure set in one Blender session.

    blender --background Startup.blend --python export_layered_figures.py -- <glb_dir> <render_dir> [summary_json]

Loads the heavy Z-Anatomy atlas once, then for each system in export_web.SYSTEMS:
  1. renders a full-resolution EEVEE QA PNG to <render_dir>/<slug>-2026-07-12.png
  2. exports detail + preview Draco GLB LODs to <glb_dir>/<slug>-{detail,preview}.glb

Each system is wrapped in try/except and the running results are flushed to the summary
JSON after every system, so a crash on the heaviest system still leaves the completed
systems' assets and stats on disk. Render happens before the decimate pass so the
evidence shows full-resolution geometry.
"""

import json
import os
import sys
import traceback

sys.path.append(os.path.dirname(os.path.abspath(__file__)))
import export_web as ew  # noqa: E402
import render_web as rw  # noqa: E402

RENDER_DATE = "2026-07-12"


def main():
    argv = sys.argv[sys.argv.index("--") + 1:]
    glb_dir = argv[0]
    render_dir = argv[1]
    summary_path = argv[2] if len(argv) > 2 else os.path.join(glb_dir, "layered-export-summary.json")
    os.makedirs(glb_dir, exist_ok=True)
    os.makedirs(render_dir, exist_ok=True)

    results = []
    for collection_name, slug in ew.SYSTEMS:
        print("SYSTEM_START", slug, collection_name)
        record = {"slug": slug, "collection": collection_name, "ok": False}
        try:
            png = os.path.join(render_dir, "%s-%s.png" % (slug, RENDER_DATE))
            rw.render_system(collection_name, png)
            record["render"] = png.replace("\\", "/")

            stats = ew.export_system(collection_name, glb_dir, slug)
            record.update(stats)
            record["ok"] = True
        except Exception as exc:
            record["error"] = str(exc)
            traceback.print_exc()
        results.append(record)
        with open(summary_path, "w", encoding="utf-8") as fh:
            json.dump(results, fh, indent=2)
        print("SYSTEM_DONE", slug, "ok=%s" % record["ok"])

    print("SUMMARY_PATH", summary_path.replace("\\", "/"))
    print("ALL_DONE")


if __name__ == "__main__":
    main()
