# Deep audit round 4 (2026-07-13) — "check everything to the bottom"

Owner asked for a bottom-up audit and vast improvement. Live release after this
round: **3fb5a51** (run 29264909192). Every claim below was verified against the
live site, not assumed.

## Audit results (live)

| Check | Result |
|---|---|
| Hero 3D renders for real visitors | ✓ canvas 1259×959, model visible |
| Click region → contexts → service links | ✓ real hrefs (consultation page + filtered provider index) |
| Console errors on homepage | ✓ zero |
| Mobile 390px | ✓ no overflow, hero + resolver work, correct 619KB mobile LOD auto-selected, DOM 1.3s |
| All 7 foundation pages | ✓ 200 with correct Hebrew H1s |
| Resolver config + GLBs served | ✓ 200 |
| Search results page | ✓ renders (H1 "תוצאות עבור: …") |
| 404 handling | ✓ 404 |
| Sitemap | ✓ Yoast sitemap_index.xml 200 |
| `<title>` element | ✓ clean (earlier "pollution" was SVG `<title>`s from the pojo toolbar — false alarm, verified before touching anything) |

## Defects found and fixed (deployed in 3fb5a51)

1. **Duplicate SEO signals — introduced by me in round 3.** Yoast SEO v28 is
   active on the live site (discovered in this audit) and its site
   representation IS configured, so it already emits a complete graph
   (Organization, WebSite, SearchAction, WebPage, BreadcrumbList) and og:*
   tags. The theme's round-3 SEO module duplicated og:title/og:image and added
   a second JSON-LD graph. **Fix:** the theme now defers social meta entirely
   to a dedicated SEO plugin and emits its own Organization/WebSite only while
   the plugin doesn't own site schema. Contract-tested in three scenarios.
   **Live after fix:** og:title ×1, og:image ×1, one coherent JSON-LD graph
   containing Organization+WebSite+SearchAction. Nothing lost, conflict gone.
2. **WooCommerce dead weight on every page.** 4+ commerce stylesheets and the
   cart-fragments AJAX script loaded sitewide with no shop in use. **Fix:**
   dequeued on portal pages (commerce surfaces keep the full stack; removing
   the plugin remains an owner decision). **Live after fix:** zero woocommerce
   CSS refs, zero cart-fragments on the homepage.

## Still owner-actions (wp-admin, unchanged)

- Site title/tagline is still the legacy "שירותי בריאות פרימיום" (shows in the
  browser tab + Google). Set in Settings → General (and Yoast title templates).
- pojo-accessibility still active (the floating blue button). Note: Israeli
  accessibility regulations are why such toolbars exist — decide replacement
  before removal, don't just delete.
- WooCommerce deactivation (now costless on portal pages, still cleaner off).

## Next queued build

Real-body swap (muscle figure exported and staged), imagery licensing decision,
mega-menu taxonomy depth, money-keyword content build.

---

## Round 5 addendum (same day): a11y + identity shipped — release 721974f

Deployed and live-verified: native accessibility panel (5 adjustments,
persisted, pre-paint boot), statement page at /accessibility/ (IS 5568 / WCAG
2.1 AA, honest gaps), pojo-accessibility retired in the same release, site
name now "Hea-lth" sitewide ("מדריכים ומחקרים - Hea-lth"), theme+plugin 0.3.0.

Two failed deploy attempts first, both instructive:
1. WPCS escaping error CI caught that my local gate-run masked (piped PHPCS
   exit code — never again; explicit exit codes now).
2. **Elementor is still ACTIVE on the host** — its blogname/blogdescription
   listener threw "Access denied" on the anonymous provisioning request,
   500-ing the healthcheck; the pipeline auto-rolled-back and verified the
   rollback. Fixed with tolerant option updates (value persists before
   listeners fire). Owner decision pending: deactivate Elementor (dead weight
   + interference; theme does not use it).

Residue: the homepage title's leading phrase is still the legacy tagline
(likely Yoast's static homepage title template — wp-admin, or next round);
accessibility-coordinator contact details pending owner input.
