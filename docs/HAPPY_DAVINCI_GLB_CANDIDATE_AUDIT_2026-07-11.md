# Happy Davinci GLB candidate audit

Audited on: 2026-07-11  
Candidate: `C:\Users\pro\Documents\antigravity\happy-davinci\3d-human-body-map\assets\human_anatomy.glb`  
SHA-256: `659349B0A374B73D5362A61070039BC8601401BBDEACBECE4F3680FB4BFD41B0`  
File size: 490,956 bytes  
Decision: **rejected for Hea-lth production anatomy. Do not copy, install, publish, or activate it.**

## What the binary inspection proves

`gltf-transform inspect` and `gltf-transform validate` were run directly against the GLB.

| Check | Observed evidence | Required for Hea-lth | Result |
| --- | --- | --- | --- |
| Identity | Single mesh named `Cesium_Man` | Reviewable human anatomy model with approved semantic structure IDs | Fail |
| Geometry | 4,672 triangles, 3,273 vertices | At least one approved detail LOD at or above 100,000 triangles | Fail |
| Anatomy systems | One skinned generic-person mesh, no named organs, systems, or selectable anatomy structures | Manifest-controlled skin, systems, organs, and semantic body structures | Fail |
| Material detail | One 1024 x 1024 JPEG base-color texture | Reviewed anatomy material, visual fidelity evidence, and responsive LOD plan | Fail |
| Runtime validity | GLTF validator reported no hard errors, but warned that the skinned mesh is not a root node | Validity is necessary but not sufficient | Insufficient |
| Provenance | No local license, attribution, vendor contract, clinical review, or model manifest accompanies the binary | Contract reference, web and derivative rights, attribution record, clinical owner, and dated review | Fail |

The binary therefore does not match the description of an ultra-real, modular anatomy model. A valid GLB file is not evidence of an approved medical-anatomy asset.

## Provenance note

The embedded mesh name identifies the candidate as Cesium Man. Khronos lists Cesium Man as a sample asset for demonstrating textured animation and skinning, with a CC BY 4.0 credit and trademark limitations. It is a technology sample, not a cleared Hea-lth anatomy asset. The local binary has no accompanying provenance or attribution record, so its exact origin still requires verification. [Khronos sample-assets listing](https://github.com/KhronosGroup/glTF-Sample-Assets/blob/main/Models/Models.md)

## Generator mismatch

The accompanying `generate_anatomy.py` creates a few primitive shapes such as a sphere head, cylinder torso, cylinders for arms, a heart sphere, and a spine cylinder. It does not generate a clinically detailed body, a modular organ hierarchy, or a 100,000-triangle anatomy asset. The checked GLB also does not correspond to that stated primitive-scene structure, which reinforces that no provenance chain has been established.

## Containment implemented

The Hea-lth anatomy-model registry now requires all of the following before a browser can receive a 3D model URL:

1. Approved web and derivative-use rights plus a non-placeholder contract reference.
2. An approved clinical reviewer and date.
3. GLTF, visual, and performance QA.
4. A passed anatomy-fidelity QA and semantic-mesh QA.
5. A source model and one same-origin `detail` LOD with at least 100,000 triangles.

The registry regression test now includes a 4,672-triangle candidate and proves it fails the public gate with `anatomy-quality-not-approved`.

## Required next asset package

An acceptable candidate must arrive with: source file checksum, source and derivative license, web-delivery permission, commercial attribution requirements, vendor or owner record, model version, semantic mesh manifest, named layers, source and runtime triangle counts, desktop and mobile LODs, validation report, visual and performance screenshots, clinical anatomy review, and a signed approval date. Until then the public site continues to use the accessible text discovery path rather than an imitation model.
