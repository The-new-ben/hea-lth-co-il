# Happy Davinci (Antigravity) RAR audit — 2026-07-12

Source: `C:\Users\pro\Documents\antigravity\happy-davinci.rar` (317,267 bytes, modified 2026-07-11 20:50), produced by "Antigravity AI" (Gemini) per the plugin header. Extracted to an isolated scratchpad; nothing was installed, merged, or deployed.

## Verdict

**REJECT for integration. Zero progress over the 2026-07-11 full-archive audit.** The archive is a byte-identical subset of the already-audited material, and its only 3D asset is the free glTF sample mannequin, not an anatomy model.

## Contents

```
happy-davinci/
├── .git/                      empty scaffold (single "Initial commit" = the empty tree 4b825dc)
├── human-body-map/            EMPTY directories only
└── 3d-human-body-map/         WordPress plugin "3D Human Body Services Map" v1.0.0, Author: Antigravity AI
    ├── 3d-human-body-map.php          936 B
    ├── generate_anatomy.py          1,970 B
    ├── assets/human_anatomy.glb   490,956 B
    ├── assets/js/app.js            13,400 B
    ├── assets/css/style.css         4,827 B
    └── includes/ (cpt-setup, rest-api, shortcode)
```

`diff -rq` against `tmp/audit/happy-davinci-full-76867f76/happy-davinci/3d-human-body-map` shows **every common file is identical**; the full archive merely had three extra items this RAR drops (`body.glb`, `index.html`, `wordpress-plugin/`). So this is a repackaging, not an iteration — it even *removes* `body.glb`, the only technically real (if unusable) mesh from the previous drop.

## The decisive finding: `human_anatomy.glb` is CesiumMan

Binary GLB parse (glTF 2.0, header-verified):

| Property | Value |
|---|---|
| generator | `COLLADA2GLTF` |
| meshes | 1 — named **`Cesium_Man`** |
| triangles | **4,672** |
| material | `Cesium_Man-effect` |
| nodes | `Z_UP`, `Armature`, `Cesium_Man`, `Skeleton_torso_joint_1`, `leg_joint_R_*`… |
| skins / animations / textures | 1 / 1 / 1 |

This is the Cesium sample-model mannequin used in glTF tutorials, renamed to `human_anatomy.glb`. Against the anatomy registry gate (`class-hea-lth-anatomy-model-registry.php::gate_manifest`): it fails the ≥100,000-triangle detail requirement by ~21×, has no anatomical structures to semantically map, no license record positioning it as an anatomy product, and nothing a clinical reviewer could meaningfully approve. The plugin header's claim of an "ultra-realistic 3D interactive human body map" does not match the shipped asset.

## `generate_anatomy.py` is a toy and is not the source of the shipped GLB

The script builds a "human" from six trimesh primitives — icosphere head, cylinder torso, icosphere "heart", cylinder "spine", two cylinder arms — and exports `assets/human_anatomy.glb`. The shipped GLB contains one skinned `Cesium_Man` mesh, not six primitive meshes, so the script's output was never shipped (or was overwritten). Even if run, geometric primitives labeled `mesh_heart_organ` cannot pass the anatomical-fidelity or semantic-mesh QA gates.

## Carried-over code violations (unchanged from 2026-07-11 audit)

- `app.js:300` builds provider cards via `innerHTML` with interpolated provider fields (injection risk).
- `app.js:296,310,318` give `premium` tier visual priority (star, brighter marker color, scale 10 vs 7) with no disclosure — violates the provider gate.
- Google Maps + mock provider flows remain as previously documented.

## Decision

1. Keep the archive quarantined (scratchpad + `C:\Users\pro\Documents\antigravity\`); do not copy code or assets into theme/plugin sources.
2. The 3D blocker remains exactly what `docs/HEA_LTH_ULTRA_REAL_3D_ANATOMY_ACCEPTANCE_AND_VENDOR_SHORTLIST_V1_2026-07-11.md` says: obtain a licensed, high-detail human model (purchase or approved open license) plus a named clinical reviewer, then drive it through `design-lab/3d-human-engine/` manifest → plugin settings → gate.
3. Any future "Antigravity" drop should be judged first by one command: parse the GLB and read the mesh names. If it says `Cesium_Man`, stop reading.
