# Runbook: real-body (muscular figure) swap — exact wiring, no improvisation

**Who may run this:** Claude Fable 5 preferably. If a safeguard fallback hands
the session to another model mid-task, that model may continue ONLY by
following this runbook verbatim — no refactors, no "improvements", no renamed
functions, no restyled code. Anything not written here is out of scope.
Owner authorisation for this narrow exception: conversation of 2026-07-13.

**Goal:** the homepage + /anatomy/ viewer shows the Z-Anatomy muscular figure
(reads as a real human body) instead of the bare skeleton, with click →
services intact.

## Hard boundaries (violating any of these = stop)

- Do NOT edit: `assets/js/anatomy-three-viewer.js`, `DRACOLoader.js`, the
  draco decoder files, `normalize_glb_names.py`, the skeletal GLBs, or any
  file in `docs/agent-sync/OWNER_DIRECTIVE_SHIPPED_3D_CODE_FREEZE_2026-07-12.md`
  except the two files this runbook names for edit.
- Do NOT weaken any gate in `class-hea-lth-anatomy-model-registry.php`.
- Do NOT invent anatomy, labels, or medical claims. Hebrew labels below are the
  approved set; anything missing stays English TA2 or is omitted.
- All shipped PHP must stay PHP 7.4-compatible.

## Inputs that already exist (verify, do not recreate)

- `tmp/z-anatomy/muscular-out/muscular-detail.glb` (~4.9MB, full res)
- `tmp/z-anatomy/muscular-out/muscular-preview.glb` (~1.96MB)
- Pipeline: `design-lab/3d-human-engine/pipeline/normalize_glb_names.py`
- Reference for manifest generation: the skeletal manifest
  `design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json`
  and the shipped default `plugin-src/hea-lth-platform-core/data/default-anatomy-manifest.json`.

## Steps (copy-paste, in order)

1. **Normalize mesh names** (gate charset forbids spaces):
   `python design-lab/3d-human-engine/pipeline/normalize_glb_names.py tmp/z-anatomy/muscular-out/muscular-detail.glb tmp/z-anatomy/muscular-out/muscular-detail-n.glb`
   and the same for the preview. Expect 0 collisions, 0 unsafe names; abort otherwise.
2. **Preview weight check:** if the normalized preview exceeds 1.5MB, re-export
   the preview with a stronger decimate ratio using
   `design-lab/3d-human-engine/pipeline/export_web.py` (see DECIMATE_PREVIEW in
   that file) rather than shipping a heavy mobile asset.
3. **Promote assets** to `theme-src/hea-lth-portal/assets/models/` as
   `muscular-detail.glb`, `muscular-preview.glb`. Do not delete the skeletal
   GLBs — layer toggling keeps both systems downloadable.
4. **Author manifest v2** by extending the shipped default manifest JSON:
   - Add a second layer `{ id: "muscular", kind: "system", defaultVisible: true, meshIds: [...] }`
     and set the skeletal layer `defaultVisible: false`.
   - Add LODs for the muscular files (purpose: desktop = detail, mobile+preview = preview).
   - Structures: map muscle meshes into the SAME regionIds already used by the
     resolver (`nose`, `face-skin`, `scalp-hair`, `joints`) plus torso/limb
     structures using labels from `design-lab/3d-human-engine/ta2-structure-map-seed.json`.
     Every `meshIds` entry must exist in the normalized GLB (verify by listing
     node names with a 10-line Python gltf reader; the normalize script prints them).
   - Keep license/review/QA blocks IDENTICAL to v1 except: bump `modelId` to
     `z-anatomy-layered-v2`, set `updated` to today, triangle counts from the
     export log.
5. **Update the example manifest** `design-lab/3d-human-engine/examples/…` the
   same way (contract test compares them).
6. **Gate proof:** run `php tooling/tests/anatomy-zanatomy-skeletal-manifest-test.php`
   — it validates the shipped manifest against the real registry gate. It must
   pass WITHOUT any registry edit. If it fails, the manifest is wrong — fix the
   manifest, never the gate.
7. **Full verification battery** (all must pass):
   `for t in tooling/tests/*.php; do php "$t" || break; done`,
   both `.mjs` tests, `python -m pytest tests/test_wordpress_pipeline.py -q`,
   PHPCS + PHPStan from `tooling/php-quality/`, the 12-route render matrix via
   `php -S 127.0.0.1:8806` + curl, and a real browser check of the homepage
   hero on the preview harness (`?page=home&threeFixture=1`): canvas renders,
   click on a muscle selects a structure, services panel fills.
8. **Bump versions** (theme style.css + HEA_LTH_PORTAL_VERSION, plugin header +
   constant), build all three packages, `--dry-run` all three.
9. **Deploy only with the owner's explicit go-ahead in the conversation**, then
   verify live: healthcheck flip, muscular GLB 200, hero canvas, click →
   services, mobile 390px, zero console errors.
10. **Record** results in `docs/` and update the deploy record.

## Approved Hebrew labels for major muscular structures

חזה (pectoralis), בטן (abdominals), גב (back muscles), כתף (deltoid),
זרוע קדמית (biceps), זרוע אחורית (triceps), אמה (forearm), ירך קדמית
(quadriceps), ירך אחורית (hamstrings), שוק (calf), ישבן (gluteals),
צוואר (neck), פנים (facial muscles), לסת (masseter/jaw).
Left/right suffixes follow the mesh names (`.l`/`.r`) — do not merge sides.
