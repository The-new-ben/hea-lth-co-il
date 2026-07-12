# Happy Davinci 3D archive audit

Archive examined: `C:\Users\pro\Documents\antigravity\happy-davinci.rar`  
SHA-256: `4C0E6541C4D5FAC9A44984B465A70AE3E5F2D99454FE80686446651CFF2F0A3D`  
Decision: **do not install or merge as a WordPress plugin. Use only as a prototype reference.**

## What the archive actually contains

The 12 KB RAR contains PHP, CSS, and JavaScript only. It does **not** contain a GLB, model manifest, license proof, attribution file, performance evidence, clinical review, tests, Google Maps configuration, CRM connector, or real provider inventory.

It implements a basic Three.js viewer, pointer raycasting, layer checkboxes, a primitive fallback made from a sphere, cylinders, and a heart sphere, a public `thbm_provider` post type, and a public GET endpoint keyed by a mesh name.

## Capability comparison

| Capability | Archive | Hea-lth foundation | Decision |
| --- | --- | --- | --- |
| Orbit camera, raycasting, ACES tone mapping | Present | Present with modern self-hosted Three.js, pixel-ratio cap, semantic structure events, and disposal | Keep the Hea-lth implementation. |
| Layers | Guessed from mesh-name strings such as `skin`, `muscle`, and `bone` | Explicit manifest-controlled `meshIds`, controlled layer kinds, Hebrew labels, and default visibility | Keep the Hea-lth implementation. |
| 3D asset | No asset. Generates a low-detail demo person when GLB load fails | No public substitute asset. Delivery stays closed until rights, clinical, GLB, visual, and performance gates pass | Reject archive fallback. |
| Asset licensing | Assumes a Z-Anatomy export without a manifest or attribution | Requires web-delivery and derivative rights, contract reference, owner, clinical review, and QA | Hold any Z-Anatomy candidate for rights review. |
| Provider model | New public provider post type, no verification gate | Existing controlled provider and clinic entities, verified state, body-region taxonomy, and read-only verified endpoint | Do not duplicate provider data. |
| Provider sorting | `premium` providers always return first | Relevance, verified state, capacity, and consent boundary come first. Sponsorship cannot override relevance | Reject archive monetization logic. |
| Map | Hard-coded Google Maps loader with `YOUR_API_KEY`, default New York coordinates, no service configuration | No map is activated yet. Directory and anatomy provide controlled body-region context for a future approved map adapter | Rebuild map as a governed adapter, not from this source. |
| Public safety | Mock “Elite” clinic data, raw endpoint content, and no consent model | No mock providers, consent-first lead-routing boundary, no live intake, no public route configuration | Reject archive public data flow. |
| UX | English, LTR, neon dark interface, no Hebrew or accessibility fallback | RTL Hebrew templates, accessible textual anatomy fallback, source-gated 3D | Do not use its visual system. |

## Specific blockers in the archive

1. It has no ultra-real 100k-plus-triangle human asset. The fallback is visibly a test mannequin, so the archive cannot substantiate its “ultra-realistic” claim.
2. It uses external Three.js CDNs pinned to r128. Hea-lth self-hosts a newer runtime and only sends it after the asset gate passes.
3. It creates `thbm_provider` as publicly queryable and exposes title, body content, address, coordinates, and thumbnail through a public endpoint. There is no verification, factual-review, ownership, or disclosure gate.
4. It calls `posts_per_page => -1`, making result cost unbounded, and sorts paid placements first. It does not match specialty, service, region, capacity, or consent.
5. The browser renders remote provider fields with string-built `innerHTML`, which is inappropriate for untrusted public profile data.
6. The Google Maps URL carries a literal placeholder key and lacks a settings boundary, referrer restrictions, service restriction, region, billing ownership, or failure state. Google recommends restricting web API keys to website origins. [Google Maps security guidance](https://developers.google.com/maps/api-security-best-practices)
7. It defaults the map to New York and has no Israel geospatial or accessibility configuration.
8. It has no keyboard anatomy selection, no semantic resolver, no privacy boundary, no CRM consent, no recipient SLA, and no audit trail.

## Z-Anatomy note

The archive says a final model can be exported from Z-Anatomy, but it does not include any such asset or rights record. The official Z-Anatomy repository describes the project as CC BY-SA 4.0 and lists attribution and derivative distribution obligations; it also lists multiple referenced assets with different terms. A commercial web delivery decision therefore needs an asset-by-asset rights and attribution review, not a generic “Z-Anatomy” assumption. [Z-Anatomy model repository](https://github.com/Z-Anatomy/Models-of-human-anatomy)

## Safe integration direction

The only reusable ideas are already present in the Hea-lth engine: orbit camera, raycast selection, ACES tone mapping, and explicit layers. The next addition should be a **map adapter** built into the existing directory and anatomy contracts:

1. Accept semantic anatomy context such as `body_region=scalp`, not a renderer mesh ID.
2. Read only verified public provider and clinic records.
3. Apply relevance, geography, capacity, and consent gates before any commercial decision.
4. Render provider values through DOM text nodes, never string-built HTML.
5. Configure a map provider through a server-side configuration boundary with restricted key, Israel region, accessibility fallback, and no hard-coded billing credential.
6. Keep the visual map unavailable until location quality, provider verification, privacy, and commercial disclosure gates are approved.

## Verdict

The archive is a prototype proving that a body click can change cards and a map. It is not a usable production plugin for Hea-lth. The current Hea-lth foundation is technically stronger on 3D asset governance, semantic routing, public-data control, and lead-routing boundaries. The production work still required is an approved ultra-real licensed asset, verified inventory, a governed map integration, and an approved consent/CRM service.
