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

## Draft service pages created

| ID | Slug | Status | Intent |
| --- | --- | --- | --- |
| 610 | `private-doctor-appointment` | draft | Private doctor appointment routing |
| 611 | `medical-second-opinion` | draft | Second opinion service lead |
| 612 | `mri-ct-appointment` | draft | MRI/CT appointment routing |
| 613 | `health-insurance-refund` | draft | Health insurance refund assistance |
| 614 | `doctor-home-visit` | draft | Doctor home visit lead |
| 615 | `premium-health-services` | draft | Premium health services pillar |

These are intentionally drafts. This is YMYL content: do not publish before medical/legal/privacy review, source references, emergency disclaimers, and a clear boundary that the site coordinates services and does not provide diagnosis or treatment.

## Draft content upgraded

Updated: 2026-05-27 05:14 UTC

The six draft private-health service pages were upgraded through the WordPress REST API with Hebrew conversion copy, emergency notices, privacy-safe first-contact rules, CRM-ready CTAs, source/reviewer/update requirements, provider-license checks, and no-medical-advice guardrails. All six remain `draft`.

Operational note: the shared local API helper was fixed before the final post so JSON request bodies are read as UTF-8. Final REST verification returned proper Hebrew titles from WordPress.

See `docs/WP_DRAFT_UPDATE_LOG.md` for the full operational log.

## Research anchors

- Google E-E-A-T/Search Quality guidance treats health content as high-stakes YMYL.
- Competitor examples: MediNow, Onit, Hidoc, Tel Aviv Doctor, Dr Platinum, BikuRofe.
- Monetization should focus on appointment coordination, private-service routing, insurance-refund assistance, and provider partnerships, not unreviewed medical advice.
