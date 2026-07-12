# Happy Davinci full archive audit

## Executive decision

Archive examined: `C:\Users\pro\Documents\antigravity\happy-davinci.full.rar`  
SHA-256: `76867F7651A5B3434E2AA1A38B442DCADA05E697DC34EDBFF20A7850C189F973`  
Audit extraction only: `tmp/audit/happy-davinci-full-76867f76/`  
Decision: **do not install, activate, merge, or copy either bundled WordPress plugin into Hea-lth.**

The archive contains a technically valid, high-detail anatomical GLB candidate and a working prototype viewer. It does not meet the Hea-lth release gate for a public medical portal. The blockers are source provenance, clinical and semantic validation, duplicate data models, fake local provider data, unsafe browser rendering, external runtime dependencies, and a different product architecture.

No live WordPress site, provider record, lead route, map, credential, or production source file was changed by this audit.

## What passed

| Check | Result | Meaning |
| --- | --- | --- |
| PHP syntax across bundled PHP files | Pass | The PHP parses. It is not a production-readiness verdict. |
| `assets/js/app.js` syntax | Pass | The first prototype script parses. |
| `body.glb` structural validation | No validator errors | The GLB can be parsed. The validator cannot fully validate its `KHR_draco_mesh_compression` extension and reports many informational unused objects. |
| `body.glb` model structure | Candidate only | Blender export, scene named `Axial skeleton`, Draco compression, 872,658 render vertices, 176,122 upload vertices, no textures, no animations. |
| Two `body.glb` copies | Identical | Both have SHA-256 `0886B6A068E655B284903664E2178D7964F80B1C0CE798461909AC806BCEF2E3`. |

The separate `assets/human_anatomy.glb` is a different low-detail CesiumMan asset. Its SHA-256 is `659349B0A374B73D5362A61070039BC8601401BBDEACBECE4F3680FB4BFD41B0`. The first prototype still loads this low-detail asset rather than the newer `body.glb` candidate.

## Claim check

| Walkthrough claim | Evidence | Verdict |
| --- | --- | --- |
| A physical GLB has been added | `body.glb` exists and parses. | Partly true. It is a technical candidate, not a released asset. |
| The 3D engine immediately uses that GLB | `assets/js/app.js:60` loads `assets/human_anatomy.glb`. | False for the first plugin. |
| No mock data remains | The standalone viewer has a hard-coded `healthcareDB` with New York clinics and a fallback path at `viewer/index.html:288-681` and `1079-1125`. | False for the archive as delivered. |
| Paid placement priority was removed | The endpoint sorts alphabetically, but the browser still gives `premium` cards a class, star, color, and larger map marker at `assets/js/app.js:296-318`. | False at the user experience level. |
| REST API is secured with nonce and session | The first plugin sends `X-WP-Nonce`; WordPress cookie-auth middleware validates it. Its route only permits a logged-in user with `read`. | Partly true, but it is not a governed public discovery model and has no Hea-lth verification, consent, or data-minimization gate. |
| Ready to integrate beside Hea-lth core | Two independent plugins create incompatible provider types and routing contracts. | False. |

## Archive topology

The RAR contains two overlapping implementations:

1. `3d-human-body-map/` creates the `thbm_provider` custom post type and `thbm/v1` endpoint.
2. `3d-human-body-map/wordpress-plugin/` creates a separate `body_provider` custom post type and `body-atlas/v1` endpoint.

Both would create a parallel provider system beside the existing Hea-lth directory, verification, map registry, lead resolver, and provider contracts. That would split inventory and governance, so neither implementation can be installed as a plugin.

## Model release assessment

| Release requirement | Evidence in archive | Decision |
| --- | --- | --- |
| Origin and web-delivery license | No `LICENSE`, `NOTICE`, attribution, vendor order, or asset manifest. The embedded `.git/config` has no remote. | Blocked. |
| Clinical validity | No reviewer, versioned anatomical label map, or clinical QA report. | Blocked. |
| Semantic mapping | Viewer guesses layer type from mesh names and sends raw mesh names into provider lookup. | Blocked. |
| Ultra-real exterior body | Scene is labelled `Axial skeleton`; the asset has no textures. It may be useful for a skeletal or internal layer, not proof of the premium skin-level human experience required for the portal. | Blocked. |
| Mobile and performance budget | No measured WebGL, network, memory, accessibility, or failure-state evidence. Draco decoding comes from an external CDN in the standalone viewer. | Blocked. |
| Israel-ready experience | The prototype defaults its map to New York, is English LTR, and has no Hebrew or accessible text fallback. | Blocked. |

The asset must stay quarantined as a **technical candidate** until the supplier provides rights evidence, a source manifest, terms for commercial web delivery and derivatives, clinical validation, and a verified semantic region map. It must then pass the existing Hea-lth model registry, visual, performance, accessibility, and release tests before it is copied into a release package.

## Security and data-governance findings

### HD-01: Stored DOM injection risk

- Severity: High if provider editors are not fully trusted, otherwise Medium.
- Location: `3d-human-body-map/assets/js/app.js:298-304`; `wordpress-plugin/viewer/index.html:1035-1112`.
- Evidence: fields such as `provider.image`, `provider.title`, `provider.address`, `p.website`, and `p.specialization` are concatenated into `innerHTML` or `insertAdjacentHTML`.
- Impact: a malicious or compromised provider record can execute markup in a visitor's browser or inject unsafe destinations.
- Fix: render every provider card using DOM nodes and `textContent`; validate `https` URLs against an allowlist before setting `href` or `src`.
- Mitigation: keep untrusted provider content out of the browser until this is fixed; enforce CSP as defense in depth.
- False-positive note: WordPress may sanitize certain editing paths, but meta fields and titles still require an explicit trust boundary. The source does not establish one.

### HD-02: Provider response is not governed by Hea-lth publication rules

- Severity: High for medical marketplace integrity.
- Location: `includes/class-rest-api.php:29-60`; `wordpress-plugin/3d-body-atlas.php:200-236`.
- Evidence: both endpoints query their own CPT, match only raw mesh ID, and return addresses, coordinates, tier and profile fields. The first endpoint is unbounded with `posts_per_page => -1`; neither endpoint enforces the Hea-lth verified-provider, evidence, capacity, region, consent, or disclosure rules.
- Impact: records outside the approved directory and lead-routing process can appear as medical recommendations. A large match set can also degrade performance.
- Fix: use the existing Hea-lth read-only directory and anatomy map adapters. Resolve semantic body region first, then query only verified public entities with bounded pagination and documented relevance rules.
- Mitigation: do not activate either CPT or endpoint.
- False-positive note: the default WordPress query context may limit status to published records, but this is not a substitute for the required business approval and verification gates.

### HD-03: Session nonce is passed in an iframe URL

- Severity: Medium.
- Location: `wordpress-plugin/3d-body-atlas.php:247-266`; `viewer/index.html:1005-1008`.
- Evidence: the shortcode adds `nonce` and `rest_url` to the iframe query string, then the viewer reads them with `URLSearchParams`.
- Impact: query strings can be retained in browser history, logs, analytics, and referrer paths. A nonce is a CSRF protection mechanism, not a data-governance control or a secret transport channel.
- Fix: remove the iframe architecture. Pass a minimal same-origin configuration through a controlled script bootstrap, or use the existing same-origin Hea-lth component.
- Mitigation: never put session-bound values in URLs.

### HD-04: Commercial tier remains visually privileged

- Severity: High for marketplace trust and disclosure.
- Location: `assets/js/app.js:296-318`.
- Evidence: premium records get a special card class and star, blue pin, and larger marker even though server ordering is alphabetical.
- Impact: the visible ranking is still pay-tier influenced without a relevance or disclosure policy.
- Fix: separate sponsored inventory from clinical and relevance ordering. Only render an approved, labelled sponsored placement after the directory decision engine has made a compliant result set.
- Mitigation: no tier field reaches the public anatomy renderer before policy approval.

### HD-05: External runtime and map configuration are ungoverned

- Severity: Medium.
- Location: `includes/class-shortcode.php:21-26`; `viewer/index.html:270-275`, `814-815`.
- Evidence: external Three.js, Google Maps, jsDelivr module imports, and Draco decoder CDN are loaded from browser code. The first plugin has a `YOUR_API_KEY` placeholder. The viewer defaults to New York at `assets/js/app.js:231-245` and `viewer/index.html:728`.
- Impact: supply-chain and availability dependencies are outside the Hea-lth release boundary, and a production map configuration would be easy to misconfigure.
- Fix: self-host or lock reviewed runtime assets in the build. Configure the approved map provider through the existing server-side map registry with a dedicated restricted web key, Israel configuration, monitoring, and an accessible fallback.
- Mitigation: do not supply a Maps key to the archive.

### HD-06: Local fake medical content survives in the standalone viewer

- Severity: High for editorial and provider integrity.
- Location: `wordpress-plugin/viewer/index.html:288-681`, `1026-1032`, `1079-1125`.
- Evidence: the viewer ships hard-coded New York clinics, treatments, products, fallback specialist text, and fallback map markers.
- Impact: it can display invented providers and medical recommendations after a failed request or in standalone mode.
- Fix: delete all mock medical and provider data. Empty states must state that no governed service is available, with no invented treatment or provider recommendation.
- Mitigation: preserve the current Hea-lth strict empty-state contract.

## Nuance on WordPress nonces

The first prototype does not call `wp_verify_nonce()` inside its endpoint. That alone is not a defect. WordPress documents that REST cookie authentication verifies the `wp_rest` nonce through `rest_cookie_check_errors()`, and recommends the `X-WP-Nonce` header for manual JavaScript requests. It also states that this model depends on a logged-in user with the needed capability. [WordPress REST authentication](https://developer.wordpress.org/rest-api/using-the-rest-api/authentication/)

The problem here is architectural: a user with ordinary `read` capability is not the Hea-lth public-provider governance policy, and the archive has no evidence that its session-only access model is intended or tested for the public medical discovery experience.

## Safe path forward

1. Keep the current Hea-lth anatomy engine, semantic resolver, provider directory, map registry, and lead-routing boundary. Do not copy archive PHP, CSS, JavaScript, viewer HTML, or CPTs.
2. Request the GLB supplier's license, provenance, content map, clinical reviewer, and export parameters. Keep `body.glb` outside release assets until that proof exists.
3. If rights are approved, add the GLB to the existing model registry as an audited candidate. Use a self-hosted Draco decoder, a semantic mapping manifest, bounded verified directory data, Hebrew RTL UI, and textual accessibility fallback.
4. Keep sponsor inventory separate from medical result relevance, with legal disclosure and no privileged placement inside the raw provider endpoint.
5. Configure maps only after provider address quality and consent are approved. Google requires application and API restrictions for its keys, including website restrictions for JavaScript keys. [Google Maps security guidance](https://developers.google.com/maps/api-security-best-practices)

## Final verdict

The new archive improves over the earlier small prototype because it includes a real high-detail skeletal GLB. It is not the completed Hea-lth 3D system and it is not safe to integrate as delivered. The correct move is to preserve the model only as a quarantined evaluation candidate and continue the governed Hea-lth build, which already has the right contracts for provider verification, semantic routing, map release, lead routing, and a no-fake-data policy.
