# 3D anatomy — editorial review-of-record decision (2026-07-12)

**Owner decision (2026-07-12):** the reviewer of record for the public 3D anatomical model is **"צוות העריכה של Hea-lth" (Hea-lth Editorial Team)** — an **editorial** attestation, not a clinical one.

## What this attestation means (and does not)

- **Scope:** the editorial team verifies that the model's structure labels follow standard **Terminologia Anatomica (TA2)** nomenclature. This is verifiable against `design-lab/3d-human-engine/ta2-structure-map-seed.json` (7,297 TA2 terms). It is a nomenclature/reference check.
- **It is NOT** a clinical/medical sign-off, and the public UI must say so. Every page carrying the viewer shows a visible disclaimer: **"אטלס אנטומי להמחשה בלבד — אינו ייעוץ, אבחון או המלצה רפואית"** plus the source line: Z-Anatomy (CC-BY-SA 4.0), derived from BodyParts3D © The Database Center for Life Science, Japan (CC-BY-SA 2.1).
- This keeps credibility intact: honest labeling + full source transparency, which is stronger than competitors who show anatomy with no sourcing. If a licensed clinician is retained later, upgrade `clinicalReview` to a true clinical review with that person's name and date.

## Manifest fields to set (skeletal-v1)

- `clinicalReview.status = "approved"`, `clinicalReview.owner = "צוות העריכה של Hea-lth"`, `clinicalReview.reviewedAt = "2026-07-12"`, `clinicalReview.kind = "editorial-nomenclature"` (add this field to document the nature honestly).
- Keep all other gate requirements real: license web-delivery + derivative + owner "Z-Anatomy" + contract reference = the CC-BY-SA license URL; gltfValid true; visual/performance/anatomical-fidelity/semantic QA = passed (skeletal detail = 598,979 tris ≥ 100k); same-origin asset path.

## Production integration checklist (next work block — this is a production DEPLOY)

1. Promote `tmp/z-anatomy/viewer/model.glb` (non-Draco, 2.4MB, loads with the already-vendored GLTFLoader — **no Draco decoder needed**) → `theme-src/hea-lth-portal/assets/models/skeletal-detail.glb`.
2. Wire the plugin manifest loader/option to this approved manifest; add the disclaimer + attribution to the viewer shell and the front-page section.
3. Extend the approved-viewer condition from the anatomy template to `is_front_page()`; replace the teaser at `front-page.php` #body-discovery with the gated viewer.
4. Verify: 22 PHP contract tests, `.mjs` tests, `pytest`, `php -l`, PHPStan/PHPCS, `deploy-wordpress.py --dry-run`, PHP 7.4 compatibility.
5. Deploy via the GitHub Actions pipeline; confirm the public `deployment_id` flips and the viewer renders on the live homepage.

Owner go-ahead to deploy to production was given 2026-07-12 ("I want to see the website live"). Design reference: the verified Artifact homepage (claude.ai/code/artifact/321cfc1d-9c37-4e1a-ba1f-7cd631d717b4).
