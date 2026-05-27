# Hea-lth WordPress API Status

Updated: 2026-05-27

## Access

- Site URL: `https://hea-lth.co.il`
- WordPress user: `healthca_admin`
- App password name: `Codex API 2026-05-26`
- Secret storage: local Windows DPAPI encrypted file outside the repo:
  `C:\Users\pro\Documents\websites\.codex-secrets\wordpress-app-passwords\hea-lth.co.il.json`

Do not commit app passwords or plaintext credentials.

## REST behavior

Both REST route styles work:

- `https://hea-lth.co.il/wp-json/wp/v2/...`
- `https://hea-lth.co.il/?rest_route=/wp/v2/...`

## Published service pages

| ID | Slug | Status | Intent |
| --- | --- | --- | --- |
| 610 | `private-doctor-appointment` | publish | Private doctor appointment coordination |
| 611 | `medical-second-opinion` | publish | Second opinion coordination |
| 612 | `mri-ct-appointment` | publish | MRI/CT appointment coordination |
| 613 | `health-insurance-refund` | publish | Health insurance refund route assistance |
| 614 | `doctor-home-visit` | publish | Doctor home visit coordination |
| 615 | `premium-health-services` | publish | Premium health services pillar |

Updated: 2026-05-27 13:56 UTC. These pages were published after rewriting them as coordination/service pages, adding emergency copy, and adding no-medical-advice boundaries. They are visible first-pass pages, not reviewed treatment guides.

## Draft content upgraded

Updated: 2026-05-27 05:14 UTC

The six private-health service pages were upgraded through the WordPress REST API with Hebrew conversion copy, emergency notices, privacy-safe first-contact rules, source/reviewer/update requirements, provider-license checks, and no-medical-advice guardrails. All six were later published after public-language cleanup.

Operational note: the shared local API helper was fixed before the final post so JSON request bodies are read as UTF-8. Final REST verification returned proper Hebrew titles from WordPress.

See `docs/WP_DRAFT_UPDATE_LOG.md` for the full operational log.

## Research anchors

- Google E-E-A-T/Search Quality guidance treats health content as high-stakes YMYL.
- Competitor examples: MediNow, Onit, Hidoc, Tel Aviv Doctor, Dr Platinum, BikuRofe.
- Monetization should focus on appointment coordination, private-service routing, insurance-refund assistance, and provider partnerships, not unreviewed medical advice.
