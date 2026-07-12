# Draco decoder requirement for the 3D anatomy viewer (2026-07-12)

> **RESOLVED 2026-07-12 (branch `claude/3d-frontpage-integration`).** The
> `DRACOLoader.js` addon and the `libs/draco/gltf/` decoder set (matching pinned
> three.js 0.185.1) are now vendored same-origin under
> `theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/`, and the viewer
> (`anatomy-three-viewer.js`) attaches a `DRACOLoader` whose decoder path is
> resolved from the module's own URL. A Draco-compressed skeletal GLB was loaded
> and rendered in a real browser through the actual theme viewer with no console
> error (see `THREED_HOMEPAGE_INTEGRATION_2026-07-12.md`). License recorded in
> `THIRD_PARTY_NOTICES.md`. The section below is retained as the original spec.

**Status (original): BLOCKER for shipping the compressed GLB assets.** Every LOD produced by
`design-lab/3d-human-engine/pipeline/export_web.py` is Draco-compressed (detail level 6,
preview level 8). three.js cannot decode Draco geometry without the Draco decoder, and
that decoder is **not vendored** in the theme today. Until it is, the layered figure GLBs
will not load in the browser.

> This document is a specification only. Vendoring files into `theme-src/**` is a
> production-deploy change and must go through the owner-approved deploy path
> (`.claude/skills/wordpress-agent-deploy/SKILL.md`). Nothing here has been vendored.

## What exists today (verified)

- Vendored three.js **r185** ES module runtime: `theme-src/hea-lth-portal/assets/vendor/three/build/three.module.min.js` and `three.core.min.js`.
- Vendored loader: `theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/loaders/GLTFLoader.js` — **only GLTFLoader is present**.
- Import map (`theme-src/hea-lth-portal/functions.php:176-183`) aliases:
  - `three` → `assets/vendor/three/build/three.module.min.js`
  - `three/addons/` → `assets/vendor/three/examples/jsm/`
- Viewer wiring: `theme-src/hea-lth-portal/assets/js/anatomy-three-viewer.js:3` imports `GLTFLoader`; line ~233 does `const loader = new GLTFLoader();` with **no** `DRACOLoader` attached.

## What is missing (verified absent)

- `examples/jsm/loaders/DRACOLoader.js` — not present.
- Any Draco decoder payload (`draco_decoder.wasm`, `draco_wasm_wrapper.js`, `draco_decoder.js`) — not present anywhere under `theme-src/`.

Consequence: loading a Draco GLB with the current viewer throws
`THREE.GLTFLoader: No DRACOLoader instance provided.` and the mesh never appears. This
applies to **both** the detail and preview LODs — both are Draco-compressed.

## Exact files three.js r185 needs (source same-origin, no CDN)

Vendor from the **same pinned r185 release** already in the theme (matching versions —
the decoder ABI must match the loader). Two pieces:

1. **The loader module** → `assets/vendor/three/examples/jsm/loaders/DRACOLoader.js`
   (resolves via the existing import map as `three/addons/loaders/DRACOLoader.js`).

2. **The decoder payload** → a same-origin folder, recommended
   `assets/vendor/three/examples/jsm/libs/draco/gltf/` (resolves as
   `three/addons/libs/draco/gltf/`). For glTF decoding the minimal set is:
   - `draco_wasm_wrapper.js`
   - `draco_decoder.wasm`
   - `draco_decoder.js`  *(JS fallback when WebAssembly is unavailable/disabled)*

   Use the glTF-tuned `libs/draco/gltf/` variant from the three.js distribution rather
   than the general `libs/draco/` set. The `draco_encoder.*` files are not needed for
   playback. Record the exact byte sizes of the wasm/js when vendored (a few hundred KB
   total — do not assume, measure).

All URLs must be same-origin `hea-lth.co.il` assets. No `cdn.jsdelivr.net`,
`www.gstatic.com/draco`, or any third-party host — this matches the standing rule in
`AGENTS.md` ("Do not add third-party CDNs at runtime; vendor assets into the theme").

## Loader wiring (ES module, matches existing viewer style)

```js
import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';

// The decoder path MUST be same-origin and end with a trailing slash.
const dracoLoader = new DRACOLoader();
dracoLoader.setDecoderPath( new URL(
  'assets/vendor/three/examples/jsm/libs/draco/gltf/',
  themeAssetsBase            // absolute same-origin theme base printed by the theme
).href );
dracoLoader.setDecoderConfig( { type: 'wasm' } ); // auto-falls back to JS if wasm unavailable

const loader = new GLTFLoader();
loader.setDRACOLoader( dracoLoader );
// ...loader.load(gatedAssetUrl, onLoad, onProgress, onError);

// Free the decoder worker when the viewer is torn down:
// dracoLoader.dispose();
```

`themeAssetsBase` should be the absolute theme-assets URL the theme already exposes to
the module (e.g. a `data-*` attribute on the viewer root or a printed global), so the
decoder path is resolved same-origin regardless of the page URL.

## Host / server considerations

- Serve `.wasm` as `Content-Type: application/wasm` so the browser can stream-compile
  the decoder. If the host cannot, DRACOLoader still works via the non-streaming path,
  but note it. (The live host runs PHP 7.4.33 / WordPress — this is a static-asset MIME
  concern, not PHP.)
- Add the Draco decoder license (**Apache-2.0**, Google) to
  `assets/vendor/three/THIRD_PARTY_NOTICES.md` when vendoring.

## Definition of done (when this is later implemented as a deploy)

1. `DRACOLoader.js` + the `libs/draco/gltf/` decoder set vendored same-origin.
2. Viewer attaches a `DRACOLoader` to the `GLTFLoader` (snippet above).
3. A gated Draco GLB (e.g. `skeletal-preview.glb`) loads and renders in the browser with
   no console error — captured as rendered evidence.
4. Decoder byte sizes recorded; THIRD_PARTY_NOTICES updated.

Until steps 1-2 ship, treat the compressed layered GLBs as **authoring/QA artifacts only**
— they are correct assets that the runtime cannot yet decode.
