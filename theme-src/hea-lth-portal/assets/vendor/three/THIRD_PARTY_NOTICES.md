# Third-party notices

## three.js

- Package: `three`
- Version: `0.185.1`
- License: MIT
- Source: https://www.npmjs.com/package/three/v/0.185.1
- Files included: the ES module runtime, `OrbitControls`, `GLTFLoader`, `DRACOLoader`, and their directly required module files.

The upstream MIT license is preserved in `LICENSE` in this directory. The runtime is self-hosted by the theme only when an approved anatomy-model configuration is available. No remote CDN is required for the renderer.

## Draco decoder (glTF)

- Component: Google Draco 3D geometry decoder, distributed with three.js under `examples/jsm/libs/draco/gltf/`.
- License: Apache-2.0 (© Google LLC).
- Files self-hosted at `examples/jsm/libs/draco/gltf/` (measured bytes):
  - `draco_wasm_wrapper.js` — 58,456 bytes
  - `draco_decoder.wasm` — 192,420 bytes
  - `draco_decoder.js` — 512,465 bytes (JS fallback when WebAssembly is unavailable)
- Purpose: `DRACOLoader` decodes the Draco-compressed anatomy GLBs
  (`theme-src/hea-lth-portal/assets/models/*.glb`). The decoder path is resolved
  same-origin from the viewer module's own URL; the `.wasm` is fetched as an
  ArrayBuffer, so no `application/wasm` MIME configuration is required on the host.
- No remote CDN (`www.gstatic.com/draco`, `cdn.jsdelivr.net`, …) is used at runtime.

This dependency notice does not grant rights to a human anatomy model, vendor content, medical data, or any third-party asset.
