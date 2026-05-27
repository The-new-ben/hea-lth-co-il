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

## Published money-pillar pages

Updated: 2026-05-27 14:35 UTC.

| ID | Slug | Status | Intent |
| --- | --- | --- | --- |
| 644 | `plastic-surgery-consultation` | publish | Plastic surgery consultation and surgeon-selection intent |
| 646 | `aesthetic-medicine-treatments` | publish | Aesthetic medicine, Botox, hyaluronic acid, facial contouring |
| 648 | `hair-transplant-consultation` | publish | Hair transplant clinic, price, Israel vs abroad |
| 650 | `laser-hair-removal-private` | publish | Laser hair removal price, suitability, clinic selection |
| 652 | `skin-treatments-private` | publish | Private skin treatments, dermatologist, acne, pigmentation, rejuvenation |

All five pages were verified by HTTP checks for status 200, emergency copy, no-medical-advice disclaimer, and no blocked internal language. Chrome verified the plastic-surgery page and homepage links.

## Homepage and footer

Updated: 2026-05-27 14:20 UTC.

- Homepage page ID: `2`
- Homepage URL: `https://hea-lth.co.il/`
- Homepage title: `שירותי בריאות פרטיים בתיאום אישי`
- Homepage was rewritten as a public service-router page linking to the six published service pages.
- Live checks confirmed HTTP 200, visible service hero, six service links, emergency copy, no-medical-advice disclaimer, and no blocked internal language.
- Homepage featured media was set to WordPress media ID `642`.
- Homepage now renders generated hero image `https://hea-lth.co.il/wp-content/uploads/2026/05/health-premium-concierge-hero-2026-05-27.png` with alt text `תיאום שירותי בריאות פרטיים בקליניקה מודרנית`.
- Live checks confirmed the old `hea-lth-on-line-300x171.png` homepage image is no longer rendered and the page has one H1.
- Footer menu `בסיס למטה` / menu ID `16` was cleaned to keep only the six service-page menu items. Legacy article pages were not deleted.

Footer menu items now visible:

| Menu item ID | Label | URL |
| --- | --- | --- |
| 630 | `תיאום תור לרופא פרטי` | `https://hea-lth.co.il/private-doctor-appointment/` |
| 628 | `חוות דעת רפואית שנייה` | `https://hea-lth.co.il/medical-second-opinion/` |
| 638 | `תיאום MRI ו-CT פרטי` | `https://hea-lth.co.il/mri-ct-appointment/` |
| 636 | `החזרי ביטוח בריאות` | `https://hea-lth.co.il/health-insurance-refund/` |
| 634 | `ביקור רופא פרטי בבית` | `https://hea-lth.co.il/doctor-home-visit/` |
| 632 | `שירותי בריאות פרימיום` | `https://hea-lth.co.il/premium-health-services/` |
| 655 | `ייעוץ לניתוחים פלסטיים` | `https://hea-lth.co.il/plastic-surgery-consultation/` |
| 656 | `רפואה אסתטית פרטית` | `https://hea-lth.co.il/aesthetic-medicine-treatments/` |
| 657 | `ייעוץ להשתלת שיער` | `https://hea-lth.co.il/hair-transplant-consultation/` |
| 658 | `הסרת שיער בלייזר` | `https://hea-lth.co.il/laser-hair-removal-private/` |
| 659 | `טיפולי עור פרטיים` | `https://hea-lth.co.il/skin-treatments-private/` |

Operational note: deleting menu items through `?rest_route=/...&force=true` returned no deletion result on this site. The clean-up succeeded through the direct `/wp-json/wp/v2/menu-items/{id}?force=true` endpoint.

## Lovable usage

Updated: 2026-05-27 14:20 UTC.

- Approved the pending Dubai-Team internal SEO/content/design plan in Lovable. Lovable saved it to `.lovable/plan.md`.
- Sent a new hea-lth.co.il prompt to Lovable for high-money private health, medical aesthetics, plastic surgery, hair transplant, skin, wellness, doctor/clinic index, premium homepage/logo/design, and first 20 page priorities.
- Lovable showed `25.3 credits expire in 9 hours` after the prompt was sent and was still working on the research result.
- Lovable later completed the hea-lth.co.il run with `Used 27 tools`. Visible summary named Estheticare as the strongest aesthetics competitor pattern and "היועצת" as a marketplace/treatment-price pattern. It recommended five pillars: plastic surgery, aesthetic medicine, hair transplant, laser, private medicine/imaging.

## Draft content upgraded

Updated: 2026-05-27 05:14 UTC

The six private-health service pages were upgraded through the WordPress REST API with Hebrew conversion copy, emergency notices, privacy-safe first-contact rules, source/reviewer/update requirements, provider-license checks, and no-medical-advice guardrails. All six were later published after public-language cleanup.

Operational note: the shared local API helper was fixed before the final post so JSON request bodies are read as UTF-8. Final REST verification returned proper Hebrew titles from WordPress.

See `docs/WP_DRAFT_UPDATE_LOG.md` for the full operational log.

## Research anchors

- Google E-E-A-T/Search Quality guidance treats health content as high-stakes YMYL.
- Competitor examples: MediNow, Onit, Hidoc, Tel Aviv Doctor, Dr Platinum, BikuRofe.
- Monetization should focus on appointment coordination, private-service routing, insurance-refund assistance, and provider partnerships, not unreviewed medical advice.
