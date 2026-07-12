# Owner directive — shipped 3D code freeze (2026-07-12)

Owner instruction, verbatim: **"avoid changing code fable 5 wrote"** (given 2026-07-12, after the 3D homepage launch).

Binding interpretation for every agent (Codex, Claude, any other): the 3D integration code that shipped in commits `2206dd0` → `87cf42b` → `034ce22` (live release `034ce22`, run 29199564252) is **verified, live, and frozen**. Git does not record which model authored which line, so the freeze is defined by this commit series, not by model identity.

## Protected — do not refactor, rewrite, restyle, or "improve"

Whole files:

- `theme-src/hea-lth-portal/assets/js/anatomy-three-viewer.js`
- `theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/loaders/DRACOLoader.js`
- `theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/libs/draco/**` (decoder JS + WASM)
- `theme-src/hea-lth-portal/assets/models/skeletal-detail.glb`, `skeletal-preview.glb`
- `plugin-src/hea-lth-platform-core/data/default-anatomy-manifest.json`
- `design-lab/3d-human-engine/examples/z-anatomy-skeletal-v1.manifest.json`
- `design-lab/3d-human-engine/pipeline/normalize_glb_names.py`
- `tooling/tests/anatomy-zanatomy-skeletal-manifest-test.php`

Protected sections inside shared files (append new code below/around them; do not edit the existing blocks):

- `theme-src/hea-lth-portal/functions.php` — anatomy config, context helper, import-map gate, viewer enqueue paths
- `theme-src/hea-lth-portal/front-page.php` — the gated live-viewer block and teaser fallback
- `theme-src/hea-lth-portal/assets/css/portal.css` — the `hp-anatomy-live` block
- `tooling/theme-preview/index.php` — the 3D fixture / manifest-driven harness wiring
- `plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php` — the default-manifest fallback (the gate itself was already protected by AGENTS.md: never weaken it)

## Allowed without asking

- **Additive work**: new files, new anatomical systems, new manifests, new tests, docs.
- **Defect fixes**: only with a demonstrated defect (rendered evidence or failing test), touching the minimum lines, reported to the owner.

## Overridden only by

The owner's explicit instruction in the current conversation naming the change. The open perf option (serving the preview LOD on the homepage instead of the detail LOD) touches protected code and therefore **stays parked** until the owner asks for it.

Rationale: the owner trusts this code as verified and live, and wants it protected from churn by other agents or model switches. Stability of the flagship feature outweighs stylistic improvement.
