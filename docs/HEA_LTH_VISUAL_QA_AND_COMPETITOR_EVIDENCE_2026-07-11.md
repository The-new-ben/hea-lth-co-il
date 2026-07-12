# Hea-lth visual QA and competitor evidence

## Scope and boundary

This is evidence for the new portal source only. It is rendered by the local
preview harness at `http://127.0.0.1:8787/tooling/theme-preview/`. It does not
activate a theme, write to WordPress, alter the live site, publish provider
records, or manufacture medical content.

The build is intentionally a foundation for a premium Hebrew health portal:

- a large RTL homepage with treatment, directory, guide, technology, account,
  professional, and anatomy paths;
- a data-gated provider and clinic experience rather than invented cards;
- a selection engine for anatomy that remains text-first until a licensed,
  clinically reviewed human model is approved;
- a reusable theme and a separate platform plugin, in line with WordPress
  guidance that custom content types belong in plugins.

## Screenshot evidence

All captures below were made in a real Chrome session. They are an evidence
baseline, not current approval evidence: the homepage directory surface and
the anatomy/map release gates changed after these captures. The current
visual-regression folder is `output/visual-regression/2026-07-11/`.

| Check | Evidence | Result |
| --- | --- | --- |
| Desktop homepage | `hea-lth-home-desktop-tokens-v2.png` | Token refresh reviewed visually |
| Mobile homepage | `hea-lth-home-mobile-token-system-v4.png` | 390 x 844 touch viewport reviewed visually |
| Desktop mega menu | `hea-lth-home-desktop-mega-menu-v1.png` | Opens from the treatment trigger and closes with Escape |
| Anatomy selection | `hea-lth-anatomy-breathing-resolution.png` | Selecting breathing resolves only the matching discovery path |
| Directory | `hea-lth-directory-desktop-top.png` | No fictional provider cards or public-health-data collection |
| Desktop templates | `template-desktop-contact-sheet-v1.png` | Eight template tops checked together |
| Mobile templates | `template-mobile-contact-sheet-v1.png` and `template-mobile-account-v2.png` | Critical responsive routes checked together; account harness defect was corrected and rechecked |
| Clalit comparison | `hea-lth-vs-clalit-desktop-v3.png` | Same desktop capture format, local portal alongside live local competitor |
| RealSelf comparison | `hea-lth-vs-realself-desktop-v3.png` | Same desktop capture format, local portal alongside live international marketplace |
| 3D interaction comparison | `hea-lth-3d-gate-vs-zygote-benchmark-v1.png` | Hea-lth's gated owned-runtime path beside a live Zygote Body benchmark. The gap is called out explicitly. |

## Browser and accessibility checks

The following audit belongs to the captured baseline. It must be re-run after
the current map-gate correction, once Chrome Profile 3 is attached. Source
contracts and local HTTP rendering were rechecked on 2026-07-11, but that is
not a substitute for a real browser audit:

| Audit | Score |
| --- | --- |
| Accessibility | 100 |
| SEO | 100 |
| Best practices | 100 |
| Agent-readable structure | 100 |

Report: `output/visual-regression/2026-07-11/lighthouse-mobile-v4/report.html`

The previous route sweep covered treatment hub, directory, guides,
professionals, account, anatomy, glossary, and technology pages. Each had one
`h1`, one `main` landmark, no duplicate IDs, no page fatal output, and no
browser-console errors. Re-capture is required before release because the
current source differs from that baseline.

## Evidence-led comparison

The comparison is a design and capability baseline, not a claim that the local
source has already earned live-marketplace scale.

| Competitor signal | Hea-lth response now | Release gate still required |
| --- | --- | --- |
| Clalit offers a dense, service-led health interface with account and care-access paths. | Hea-lth uses a calmer premium portal layer, deep mega navigation, research, directory, and provider acquisition paths. | Confirm actual operational services and clear distinction from health-fund functions. |
| RealSelf exposes procedures, provider discovery, photos, reviews, questions, and sponsored marketplace placement. | Hea-lth has treatment, directory, guide, professional, and verified-record foundations without synthetic profiles or claims. | Verified provider onboarding, review policy, disclosure rules, consent, and real inventory. |
| Zocdoc is an appointment-oriented directory benchmark. | Hea-lth architecture leaves booking and lead routing outside the presentation theme. | Establish a lawful workflow, CRM ownership, routing SLAs, and actual availability data. |
| Zygote Body demonstrates a dense, orbitable anatomy viewer with hierarchy and layer controls. | Hea-lth now has a self-hosted renderer, model gate, named-mesh resolver, layer controls, camera controls, and text fallback. | A licensed, clinically reviewed, ultra-real asset and production performance evidence are still required. |

## Fixed during evidence review

1. Removed invalid `role="list"` from a mixed link/button navigation container.
2. Repaired the visible-name mismatch in the brand link's accessible name.
3. Strengthened bronze and light-bronze tokens to satisfy text contrast.
4. Removed negative Hebrew letter spacing from all portal source styles.
5. Added a cautious theme meta-description fallback that yields to established
   SEO plugins.
6. Fixed the account screen in the local preview harness after the mobile
   screenshot exposed a missing WordPress escaping helper stub.

## Sources

- WordPress: [Templates](https://developer.wordpress.org/themes/templates/templates/)
- WordPress: [Theme JSON](https://developer.wordpress.org/themes/global-settings-and-styles/introduction-to-theme-json/)
- WordPress: [Custom post types in plugins](https://developer.wordpress.org/plugins/post-types/registering-custom-post-types/)
- Clalit: [homepage](https://www.clalit.co.il/he/Pages/default.aspx)
- RealSelf: [homepage](https://www.realself.com/)
- Zocdoc: [homepage](https://www.zocdoc.com/)

## Next release gates

1. Re-capture local desktop and mobile routes in Chrome Profile 3, then make
   fresh labelled side-by-side comparisons with Clalit, RealSelf, and the
   selected 3D benchmark. The earlier images cannot be used as signoff for the
   current source.
2. Select and license the anatomy model, then complete clinical and legal
   review before a public 3D renderer is enabled.
3. Import only verified provider, clinic, treatment, equipment, and editorial
   data.
4. Approve the canonical URL inventory and redirect plan before touching live
   WordPress routing.
5. Configure consent, CRM routing, notification policy, and reporting before
   collecting leads.
6. Package the theme and platform plugin into the existing controlled
   WordPress deployment pipeline only after approval.
