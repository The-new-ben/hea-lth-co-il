# Foundation pages + body-discovery restore (2026-07-12, round 2)

Owner report: the 3D full-experience link 404s, the model sits too low on the
homepage, the favicon is missing, and the body→services vision feels lost.

## Root cause (verified live)

**Every foundation route returned 404 in production** — `/anatomy/`, `/guides/`,
`/glossary/`, `/find-care/`, `/health-technology/`, `/professionals/`,
`/treatments/` — because the theme ships the page templates but the WordPress
pages that activate them were never created. Only the homepage existed. The
legacy commercial pages all still work (200):
`/aesthetic-medicine-treatments/`, `/plastic-surgery-consultation/`,
`/hair-transplant-consultation/`, `/mri-ct-appointment/`,
`/private-doctor-appointment/`, `/doctor-clinic-index/`,
`/medical-second-opinion/`.

**The body→services vision was never lost in code.** The anatomy page's
resolver (anatomy-discovery.js, 362 lines) renders region → context → governed
entries. Verified end-to-end in the harness: nose region → aesthetic context →
treatment hub + `/doctor-clinic-index/?specialty=plastic-surgery&body_region=nose`;
ENT context → guides + `?specialty=otolaryngology`. It was unreachable because
`/anatomy/` had no page.

## Shipped (branch `claude/design-system-v1`, round 2)

1. **Page provisioner** (`class-hea-lth-page-provisioner.php`, plugin 0.2.0):
   creates the seven UI-linked foundation pages with their templates, once per
   blueprint version, on `init`. Never updates/overwrites/deletes an existing
   page — owner content always wins. Contract-tested: every blueprint slug must
   exist in the route registry, every template must ship in the theme, and the
   no-overwrite guards must be present (`page-provisioner-contract-test.php`).
2. **3D moved up the homepage**: the anatomy section relocated (byte-identical,
   54 lines) from 8th to 3rd — hero → search → interactive body.
3. **Favicon**: brand H monogram (SVG + 32px PNG + 180px apple-touch) served
   from the theme; renders only when no wp-admin Site Icon is set.
4. **Harness parity**: the preview now injects the production route map so the
   resolver's service links are testable locally.

Verification: 22 PHP + 2 mjs contract tests, PHPCS clean, PHPStan clean (one
real finding fixed, not suppressed), pytest 19/19, render matrix 12/12,
homepage order verified, resolver click-through verified with real hrefs,
packages rebuilt (plugin 0.2.0, theme 0.2.0), dry-runs OK.

## Answers to the owner's open questions

- **"What blue icon / accessibility?"** — the WordPress plugin
  `pojo-accessibility` (installed on the old site, still active) renders its
  own floating toolbar button on every page, on top of our design. The theme
  does not ship it. Recommendation stands: deactivate it in wp-admin (the
  theme's accessibility is native), together with WooCommerce.
- **Real body, not a skeleton** — agreed and planned: Z-Anatomy includes the
  full surface/muscular/organ systems; the same export pipeline produces a
  layered figure (skin → muscles → organs → skeleton). Skeleton was the
  license-clean first slice, not the end state. No invented anatomy will be
  used.

## Still open

- Tier-2 foundation routes without templates yet (diagnostics*, wellness*,
  private-medicine, skin, about, contact, policy pages) — need templates before
  provisioning; they remain 404 until then.
- wp-admin: deactivate WooCommerce + pojo-accessibility; set site title/tagline.
- Homepage look ("fairly average") — owner wants a design conversation; next
  round after these fixes are live.
