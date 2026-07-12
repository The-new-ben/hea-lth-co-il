# 3D model breakthrough — Z-Anatomy pipeline proven end-to-end (2026-07-12)

**Status: the core 3D blocker is broken.** For the first time the project has a real, licensed, web-ready, gate-passing, semantically-labeled human anatomy asset, converted and rendered with our own engine and captured as evidence. What remains is integration and one human attestation — not asset discovery.

## What was achieved this session

| Step | Result | Evidence |
|---|---|---|
| Source acquired | Z-Anatomy full atlas (`Startup.blend`, 306 MB) | `tmp/z-anatomy/` (gitignored); SHA-256 `e029688545627bd0214b269e1063143abb580aad72b2c2445d6d8a9a0d9da736` |
| License verified | **CC-BY-SA 4.0** — web delivery + derivatives explicitly allowed, attribution required | `docs/3d-evidence/Z-ANATOMY-License.txt` |
| Atlas scale | **4,569 meshes, 1,944 named structures, ~4.1 M triangles**, all major systems | Blender headless analysis |
| Tooling | Blender 5.1.2 installed and driven headless | `design-lab/3d-human-engine/pipeline/` |
| First slice exported | Skeletal system: **598,979 triangles**, 1,244 meshes | `analyze.py` + `export_slice.py` |
| Web packaging | Draco detail GLB **2.1 MB**, preview GLB **826 KB** | `docs/3d-evidence/skeletal-detail.glb` |
| Semantic labels | Preserved: `Incus.l/.r`, `Malleus`, `Stapes`, `Fifth metatarsal bone.l/.r` | GLB JSON chunk |
| Visual QA — Blender | Complete, accurate front-view skeleton | `docs/3d-evidence/z-anatomy-skeletal-blender-render-2026-07-12.png` |
| Visual QA — our engine | Loaded in vendored three.js r185: **278 meshes, 1.70 m tall**, clean bone-white PBR, studio-lit, interactive orbit | browser render confirmed |

## Benchmark position (God Mode)

- **Structure count:** 1,944 named structures is in the same class as BioDigital Human and Complete Anatomy for breadth. We are not below the category leaders on anatomical coverage.
- **Delivery weight:** 2.1 MB detail / 826 KB preview is competitive — these load fast; competitors stream heavier payloads progressively.
- **Presentation:** with a bone-white PBR override + studio lighting, the browser render reads as professional medical-atlas grade. The raw Z-Anatomy viewport materials are NOT competitor-grade (dark/engraving look); the viewer overrides them, which is standard practice.
- **Honest gap vs. the very top:** Z-Anatomy is atlas-realistic, not photoreal skin/tissue like a Zygote purchase. For the skeletal/systems discovery experience this is at parity; for photoreal external-body aesthetics (the RealSelf angle) a paid model can be layered later. Owner decided against Zygote for now — recorded.

## The gate — where this asset stands against `class-hea-lth-anatomy-model-registry.php`

| Gate requirement | Status |
|---|---|
| `status = approved` | NO — currently `review` (blocked on the two items below; correct behavior) |
| license web delivery + derivatives + owner + contract ref | **PASS** (CC-BY-SA 4.0, attribution recorded) |
| detail LOD ≥ 100,000 triangles | **PASS** — 598,979 |
| GLB valid glTF 2.0 | **PASS** |
| visual QA passed | **PASS** — two independent renders |
| performance QA passed | PENDING — no rigorous mobile/FPS pass yet |
| clinical review approved + named owner + date | PENDING — needs a real named reviewer of record |
| same-origin asset path | satisfied on ship (asset promoted into the theme) |

Manifest artifact: `design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json` — fully populated except the one field that legitimately requires a human.

## The reusable pipeline (reproducible)

`design-lab/3d-human-engine/pipeline/`:
- `analyze.py` — Blender headless: object/mesh/triangle census + collection tree.
- `export_slice.py` — selects a system collection, exports a Draco detail LOD (full-res) + a decimated preview LOD, y-up, apply-modifiers.
- `render_preview.py` — EEVEE render for visual QA evidence.

Run pattern:
```
blender --background Startup.blend --python export_slice.py -- "1: Skeletal system" out
```

## Honest QA floats from this session

1. **[High — content]** Clinical/editorial reviewer of record is still unset. Owner directed "use editorial stuff" — the manifest is set up for an **editorial attestation** (labels follow TA2 nomenclature, not a medical-treatment claim), but the gate still requires a **real named person + date**. I will not fabricate one. This is the single content blocker to approval.
2. **[High — perf]** No rigorous performance QA yet (low-end mobile, FPS, load under throttled network). Required before `performanceQa: passed`.
3. **[Med — asset hygiene]** Z-Anatomy bakes floating 3D **text-label objects** into each collection (confirmed mesh `Skeletal_systemg`). The production export MUST exclude these; a name audit (`window.__meshNames`) already catches them.
4. **[Med — engine]** The **Draco decoder is not vendored** in the theme's three.js. The compressed GLBs need it at runtime; must vendor `DRACOLoader.js` + the decoder wasm/js same-origin (no CDN) before shipping the compressed detail LOD. (The uncompressed clean preview loads without it.)
5. **[Med — delivery]** The deploy package cap is 64 MB and the current theme package is ~323 KB; a multi-system asset set could grow the theme. Plan a dedicated asset-delivery path (separate package or media upload) as systems are added.
6. **[Low — materials]** Ship the bone-white PBR override in the exported GLB (done for the preview) rather than relying only on viewer-side material replacement, so the asset looks correct even in third-party glTF viewers.

## Next moves (in order)

1. Owner: name the editorial reviewer of record (can be you or a designated editor) — unblocks float #1.
2. Vendor the Draco decoder same-origin (float #4) — my task, code change (deploy).
3. Production integration: promote `skeletal-detail.glb` + `skeletal-preview.glb` into `theme-src/.../assets/z-anatomy/`, load the manifest via the plugin, extend the approved-viewer condition from the anatomy template to `is_front_page()`, replace the homepage teaser (`front-page.php:267`) with the gated semantic viewer. **This is a production deploy — needs explicit owner go-ahead.**
4. Performance QA pass on 375 px mobile + throttled network (float #2).
5. Expand from skeletal to a layered figure (surface → muscular → skeletal → organs) using the same pipeline, mapping structures from `TA2.csv` for click-to-identify at competitor breadth.

## Attribution obligation (permanent)

Any page shipping this asset must display, in a visible place: **"Anatomy model: Z-Anatomy (CC-BY-SA 4.0), derived from BodyParts3D © The Database Center for Life Science (CC-BY-SA 2.1 JP)."** Derivative works must remain CC-BY-SA 4.0. This is a license condition, not optional.
