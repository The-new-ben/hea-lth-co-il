# Foundation hierarchy route gate

Decision date: 2026-07-11  
Scope: from-scratch Hea-lth theme source only. No WordPress page, menu, redirect, canonical tag, sitemap, or live URL was changed.

## Decision

All named internal routes rendered by the theme now resolve through one of two controlled registers in `inc/portal-route-registry.php`:

1. `hea_lth_portal_route()` is limited to current, evidence-approved legacy destinations from the keyword-to-URL seed map. It is used for high-intent money and directory routes.
2. `hea_lth_portal_foundation_route()` holds the portal hierarchy needed by the new design, including guides, diagnostics, glossary, anatomy, professional, account, policy, and technology areas.

The second register is explicitly marked `evidence-gated`. It is an IA contract, not an approval to create, index, redirect, migrate, or publish a URL. A future release must reconcile each route with the legacy crawl, Search Console, backlink evidence, content equivalence, keyword map, and redirect decision.

## Why this exists

Before this decision, header, footer, homepage, and page templates embedded named paths independently. That creates three avoidable risks:

| Risk | Containment now | Release proof still required |
| --- | --- | --- |
| One design change silently creates inconsistent internal URLs | Named paths resolve through a central key | Crawl the staging and production route map |
| A new portal category is mistaken for an approved SEO target | Foundation entries declare `evidence-gated` status | Legacy and SERP evidence, keyword-map row, and publishing disposition |
| A powered legacy URL is replaced by a plausible but wrong destination | High-intent links still use the existing canonical registry | GSC, backlink, canonical, redirect, and content-equivalence review |

## Current controlled scope

The foundation register includes the visible portal skeleton already designed in source: treatments, diagnostics and its subareas, guides, glossary, wellness, health technology and equipment, anatomy, professional flows, account, contact, legal pages, and utility pages. Unknown keys fail closed to the homepage in development rather than emitting a new path.

The canonical registry remains limited to current approved routes such as aesthetic medicine, plastic-surgery consultation, hair-transplant consultation, private doctor appointment, MRI or CT appointment, medical second opinion, insurance refund, doctor home visit, premium services, and the doctor or clinic index.

## Evidence captured

- `tooling/tests/portal-route-registry-test.php` verifies canonical routes, foundation routes, evidence-gated state, unknown-key failure, and absence of named raw `home_url()` routes in public PHP templates.
- `tooling/tests/directory-browser-contract-test.php` verifies that directory entry points do not emit ungoverned specialty or city skeleton routes.
- PHP lint passed for the route registry, edited templates, header, footer, and functions.
- The local preview returned HTTP 200 with exactly one `main` and one `h1` for home, treatments, directory, guides, professionals, account, anatomy, glossary, and technology views.

## Open release gates

1. The complete legacy crawl, GSC page and query export, Semrush visibility export, backlink sample, canonical inventory, and redirect map are still required. The current seed map is not a replacement for that evidence.
2. `assets/data/anatomy-discovery-v1.json` now carries controlled route keys and bounded query filters rather than raw paths. Its route map must still be reviewed against the release inventory whenever a destination changes.
3. Foundation pages require individual keyword intent, Hebrew SERP, medical-review, content, and indexability decisions. No long-form content is generated or published by this source change.
4. No release may proceed until visual review in Chrome, responsive and accessibility evidence, and the existing WordPress migration and rollback gates are approved.
