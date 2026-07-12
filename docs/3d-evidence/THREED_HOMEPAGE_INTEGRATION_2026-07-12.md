# 3D anatomy — homepage viewer integration (2026-07-12)

> **DEPLOYED TO PRODUCTION 2026-07-12.** Merged to `main` (`034ce22`), deployed via
> GitHub Actions run `29199564252`. Live `deployment_id` flipped to
> `gh-034ce22…29199564252` on both healthchecks. The skeletal viewer renders on
> the live homepage (`https://hea-lth.co.il`): config `approved/three-webgl`,
> model `z-anatomy-skeletal-v1 v1.1.0`, 277 meshes, 88 clickable structures,
> Draco-decoded on the live host, old teaser removed, YMYL disclaimer visible.
> Auto-activated via the shipped default manifest (owner chose "Deploy + auto-show").
> Render evidence: `docs/3d-evidence/LIVE-homepage-skeleton-render-2026-07-12.png`.

Originally built on branch `claude/3d-frontpage-integration`. This record documents
a complete integration of the licensed Z-Anatomy skeletal model into the homepage
hero, gated by the platform approval gate.

## What shipped into the branch (verified)

| Area | File | Change |
|---|---|---|
| Draco decoder | `theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/loaders/DRACOLoader.js` + `libs/draco/gltf/{draco_decoder.wasm,draco_decoder.js,draco_wasm_wrapper.js}` | Vendored same-origin, pinned to three.js 0.185.1 (ABI match). |
| Viewer | `theme-src/hea-lth-portal/assets/js/anatomy-three-viewer.js` | Attaches `DRACOLoader` (decoder path from `import.meta.url`); on-demand render loop; initial resize before framing; decoder disposed after decode. |
| Homepage | `theme-src/hea-lth-portal/front-page.php` | Anatomy section renders the real gated viewer when a model is approved; falls back to the existing teaser otherwise. |
| Enqueue/gate | `theme-src/hea-lth-portal/functions.php` | `hea_lth_portal_is_anatomy_viewer_surface()` extends the import map + viewer to `is_front_page()`; homepage gets a minimal config-only path (no resolver/map scripts). |
| Styling | `theme-src/hea-lth-portal/assets/css/portal.css` | `.hp-anatomy-live` stage (desktop + 375px), loading state, canvas fill. |
| Assets | `theme-src/hea-lth-portal/assets/models/skeletal-{preview,detail}.glb` | Clean, label-free Z-Anatomy skeletal LODs (see below). |
| Manifest | `design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json` | Corrected to pass the real gate (root-relative paths, editorial reviewer, QA flags). |
| Contract test | `tooling/tests/anatomy-zanatomy-skeletal-manifest-test.php` | Proves the shipped manifest passes the real gate; guards against drift. |
| Harness | `tooling/theme-preview/index.php` | Homepage 3D fixture path + filemtime cache-busting + host-agnostic base URL. |

## The assets (measured)

Re-exported from `tmp/z-anatomy/Z-Anatomy/Startup.blend` with the fixed
label-stripping pipeline (`design-lab/3d-human-engine/pipeline/export_web.py`,
`excluded_labels: 1941`):

| LOD | purpose | file | triangles | bytes | sha256 (first 12) |
|---|---|---|---|---|---|
| lod-0 | mobile | skeletal-preview.glb | 104,304 | 634,044 | dac034874c31 |
| lod-1 | desktop | skeletal-detail.glb | 598,529 | 1,863,280 | f4fc761e5fdc |
| lod-2 | detail (gate proof) | skeletal-detail.glb | 598,529 | 1,863,280 | f4fc761e5fdc |

(Byte/sha values are post name-normalization; see the v1.1.0 update below. Triangle counts unchanged.)

Both GLBs are Draco-compressed, `KHR_draco_mesh_compression`, real TA2 mesh names
preserved (`Incus.l`, `Femur.l`, `Mandible`, …), and contain **no** baked
`Skeletal system.g` text label (the earlier assets did; those predated the fix).

## Verification evidence (all local; live site untouched)

- **Rendered in the real theme viewer** (browser, `anatomy-three-viewer.js`,
  Draco-decoded): complete label-free skeleton, bone-white on the dark stage.
  Screenshot: `docs/3d-evidence/homepage-skeleton-viewer-render-2026-07-12.png`.
- **Gate**: `anatomy-zanatomy-skeletal-manifest-test.php` → the shipped manifest
  returns `status=approved`, `engine=three-webgl`, same-origin LODs, detail LOD
  ≥ 100k, structures resolve with real mesh names, no leaked contract metadata.
- **Desktop**: loads `lod-1` (598,529 tris, 1.86 MB), 277 meshes, `ready`, no
  console errors, 30 structure mappings active.
- **Mobile 375px**: loads `lod-0` (634 KB), **0 px horizontal overflow**, stage
  fits viewport, ~2.2 ms/render frame (≈60fps headroom).
- **Accessibility**: canvas `tabindex=0`, `role=img`, Hebrew aria-label;
  keyboard view controls (front/back/reset) + Home/End; illustration-only YMYL
  disclaimer + full CC-BY-SA source attribution visible.
- **Gates**: 21 PHP + 2 mjs contract tests pass; pytest 19 pass; render matrix
  12/12 routes 200 with one `main`/`h1` (incl. homepage with viewer); PHPCS 43/43;
  PHPStan no errors; JS module syntax OK.

## Reviewer of record

Per `docs/THREED_EDITORIAL_REVIEW_DECISION_2026-07-12.md`: **"צוות העריכה של
Hea-lth"** (editorial nomenclature attestation, TA2 labels — **not** a clinical
endorsement). The public viewer states this in Hebrew.

## Activation for production (owner-gated — do NOT run without go-ahead)

This branch ships the code and assets, but the homepage viewer stays gated until
the platform option `hea_lth_anatomy_model_manifest` holds an approved manifest.
Two ways to activate after the branch is merged and deployed:

1. **wp-admin (manual, documented in `docs/agent-sync/`):** paste the JSON from
   `design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json` into
   the anatomy model manifest setting. The gate re-validates on save.
2. **Coded default (optional, future):** the plugin could load this manifest as a
   default when the option is empty — a trust decision the owner should make
   explicitly. Not implemented here.

### Deploy sequence (per `wordpress-agent-deploy` skill)
1. Merge `claude/3d-frontpage-integration` → `main` (touches `theme-src/**` → CI deploys).
2. CI builds + deploys the parent theme (carrying the GLBs + Draco decoder) and child theme.
3. Set the manifest option (step 1 above); confirm the homepage shows the viewer.
4. Verify public `deployment_id` flipped on both healthchecks.

## Update — whole-skeleton click-to-identify (manifest v1.1.0)

The earlier semantic-coverage float is **resolved**. The gate's `sanitize_mesh_ids`
forbids spaces, and Z-Anatomy names 243 of 277 bones with spaces. A reusable
pipeline step `design-lab/3d-human-engine/pipeline/normalize_glb_names.py`
rewrites glTF node names to the gate charset (`Frontal bone.l` → `Frontal_bone.l`),
touching only the JSON chunk — Draco geometry in the BIN chunk is byte-identical
(triangle counts unchanged; file size within 4 bytes). Applied to both shipped
LODs (243 renamed, 0 collisions, 0 still-unsafe). The manifest now exposes **19
structures / 88 clickable bones** spanning the whole recognizable skeleton
(cranium, mandible, ossicles, cervical/thoracic/lumbar spine, sacrum/coccyx, 24
ribs, sternum, clavicle, scapula, humerus, radius, ulna, pelvis, femur, patella,
tibia, fibula). Browser-verified: every sampled bone resolves to its correct
Hebrew label through the real viewer lookup.

## Open floats (see session report)

- **Asset cache-busting** on the live host: the manifest LOD paths are stable
  filenames. Ship versioned filenames (`skeletal-detail-v2.glb`) on asset updates.
- **Formal performance QA**: functional perf verified (sizes, on-demand render,
  60fps headroom, 375px). Throttled-3G + low-end-device Lighthouse remains a
  post-deploy pass on the live host.
- **Systems beyond skeletal**: muscular/cardiovascular/nervous/visceral re-run
  the same pipeline; layered figure is the next expansion.
