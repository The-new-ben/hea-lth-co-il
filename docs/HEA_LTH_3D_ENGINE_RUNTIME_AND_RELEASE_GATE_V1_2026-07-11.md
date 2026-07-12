# Hea-lth 3D runtime and release gate v1

## Status

**Technical runtime:** passed in a local, non-public proof.

**Production human asset:** not acquired, not approved, and not released.

This distinction is deliberate. The local proof establishes that Hea-lth can own the WebGL interaction layer without embedding an ad-supported public anatomy site or depending on a remote rendering CDN. It does not assert that the temporary test model is medically suitable, visually premium, or licensable for public use.

## What is implemented in source

| Capability | Evidence | Public state today |
| --- | --- | --- |
| Self-hosted WebGL renderer | `theme-src/hea-lth-portal/assets/js/anatomy-three-viewer.js` | Loaded only after a registry-approved configuration |
| Self-hosted dependency files | `theme-src/hea-lth-portal/assets/vendor/three/` | No renderer CDN dependency |
| Orbit, zoom, front, rear, and reset views | Three.js `OrbitControls` and accessible camera buttons | Available after a model passes the gate |
| Named-mesh selection | Raycasting maps model mesh IDs to governed anatomy region IDs | Available after a model passes the gate |
| Layer switching | Model manifest defines named layer and mesh relationships | Available after a model passes the gate |
| Text-first fallback | `assets/js/anatomy-discovery.js` and the anatomy template | Always available, including without WebGL |
| Registry and public endpoint | `Hea_Lth_Anatomy_Model_Registry` | Does not release a GLB path until every gate passes |
| Test fixture containment | `tooling/anatomy-fixture/` | Localhost only. Not packaged in the theme or plugin |

## Gate that prevents accidental publication

The WordPress plugin accepts an anatomy manifest only when all of these are true:

1. The model status is `approved`.
2. Written web delivery and derivative-use rights are true, with a non-placeholder contract reference and owner.
3. Clinical review is approved with a reviewer and review date.
4. The delivery GLB has passed validation, visual QA, and performance QA.
5. Public runtime asset paths are same-origin or root-relative and only an approved runtime LOD is exposed.

Any failed condition returns a safe `license-gated` configuration with no model URL. The theme then keeps the anatomy index and discovery routes visible without rendering a body.

The technical fixture has an additional browser-side rule: it may run only on `localhost`, `127.0.0.1`, or `::1`. It cannot activate on a public origin even if copied by mistake.

## Proof results from the local technical fixture

The fixture is a deliberately simple generated mannequin. It proves interaction plumbing only and is never a visual candidate.

| Test | Result |
| --- | --- |
| PHP syntax for the registry, theme, and local harness | Passed |
| Registry gate test | Passed for approved and clinically unapproved manifests |
| JavaScript module syntax | Passed |
| GLB structural validation | Passed with informational tangent and UV notices only |
| Renderer and GLB network requests | Returned HTTP 200 in the local browser proof |
| Named nose mesh selection | Resolved to the `nose` discovery route and highlighted the mesh |
| Layer switch | Respiratory layer made lung meshes visible and hid the skin and skeletal test meshes |
| Rear-view control | Changed the camera from front to rear orientation |
| Normal anatomy page | Remained asset-gated with no fixture model released |

Internal-only technical evidence: `output/visual-regression/2026-07-11/anatomy-three-fixture-ready-v1.png`.

## Resolver contract for the portal

When a visitor changes a body region or context, the anatomy discovery layer emits a browser event named `hea-lth:anatomy-resolution-updated`. Its safe public payload contains only:

- selected region ID and display label;
- selected context ID and display label;
- same-site treatment hub URL;
- directory, map, and catalog filter slugs.

It never contains a diagnosis, visitor data, unverified provider, stock availability, price, vendor key, or model contract data. A future map, verified directory list, and equipment catalog can subscribe to the same event without being hard-wired into the 3D renderer. The resolver data is checked by `tooling/tests/anatomy-resolver-contract-test.mjs`.

## Competitor comparison screenshot

`output/visual-regression/2026-07-11/hea-lth-3d-gate-vs-zygote-benchmark-v1.png` puts the current Hea-lth gated state next to a live Zygote Body interaction benchmark.

The image is intentionally candid about the gap. Zygote demonstrates the interaction quality, anatomy hierarchy, and presentation class to beat. It also displays third-party branding and an advertisement, so it is not the Hea-lth production route. Its public terms restrict use to the relevant written or subscription agreement and do not by themselves grant an unrestricted public Hea-lth implementation.

## Production acceptance still required

| Workstream | Evidence required before any public release |
| --- | --- |
| Visual asset | Full 360-degree visual review at body and regional close views. No stock avatar, mannequin, or generated image substitute. |
| Medical accuracy | Qualified reviewer approval for the intended body systems, labels, sections, limitations, and update cadence. |
| Rights | Executed real-time web agreement with Israel, Hebrew, commercial display, derivatives, delivery, anti-extraction, pricing, support, and exit terms. |
| Semantic data | Stable hierarchy and at least 30 reviewed mesh-to-anatomy mappings before expansion. |
| Section views | Pre-authored or clinically reviewed cut geometry. A raw clipping plane is not a clinical section. |
| Performance | Measured desktop, iPhone-class, Android-class, and constrained network results for actual licensed assets and LODs. |
| Portal resolution | Verified guides, providers, clinics, equipment, and maps, with commercial disclosure and no diagnosis or treatment promise. |
| Accessibility | Keyboard, touch, text navigation, clear fallback, and no loss of discovery functionality when WebGL fails. |

## Decision needed next

Run a vendor RFI and sample review before selecting the real model. The owned-source comparison should begin with Zygote's real-time licensing path. The platform comparison should begin with BioDigital's commercial viewer and content API terms. Primal Pictures is a second medical-education vendor to assess for commercial embedding.

## Sources

- [Zygote Body live viewer](https://www.zygotebody.com/)
- [Zygote terms](https://www.zygotebody.com/terms)
- [Zygote real-time licensing help](https://www.zygote.com/help)
- [Zygote SDK capabilities](https://go.zygote.com/sdk-1)
- [BioDigital developer tools](https://developer.biodigital.com/)
- [Primal Pictures embeddable viewer](https://primalpictures.com/video/embeddable-viewer/)
- [Three.js GLTFLoader](https://threejs.org/docs/pages/GLTFLoader.html)
