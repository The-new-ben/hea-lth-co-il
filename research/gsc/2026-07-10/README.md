# Google Search Console authenticated baseline

**Property:** `sc-domain:hea-lth.co.il`  
**Captured:** 2026-07-10  
**Report data window:** 2026-04-09 through 2026-07-08  
**Search type:** Web  
**Source:** authenticated Google Search Console performance UI  
**Coverage:** top rows visible in the current three-month report; not a complete 16-month API export.

## Property totals

| Metric | Value |
|---|---:|
| Clicks | 425 |
| Impressions | 58K |
| CTR | 0.7% |
| Average position | 28.2 |

## Immediate evidence-led decisions

1. Preserve and audit `/best-cosmetics/` before any architecture change: it leads current pages with 104 clicks and 15,186 impressions.
2. Preserve the existing hair-loss and hair-transplant URLs; together they already receive material impressions and clicks.
3. Treat HMO-funded oxygen/hyperbaric treatment as a proven search wedge and reconcile overlapping oxygen/hyperbaric URLs before creating another page.
4. Preserve anti-aging, diabetic-foot, medical-equipment, hospital-system, and home-hyperbaric URLs until the full query-to-page/canonical/backlink audit is complete.
5. The homepage has weak current organic performance relative to specialist pages; homepage redesign must improve navigation and conversion without absorbing or replacing powered specialist intents.
6. Botox, rhinoplasty, hyaluronic-acid, aesthetics, and medical-equipment pages show large position/CTR upside, but require localized SERP and content-quality analysis before rewriting.

## Limitations and next connection

This is a UI-derived top-row snapshot, not the complete Search Console dataset. The permanent connection must use the Search Console API with read-only OAuth and capture 16 months by date/query/page/country/device/search appearance. Until that export is stored, this snapshot is sufficient to freeze powered URLs but not sufficient for redirect, cannibalization, or traffic-forecast decisions.

Files:

- `gsc_queries_2026-04-09_2026-07-08_top20.csv`
- `gsc_pages_2026-04-09_2026-07-08_top20.csv`

