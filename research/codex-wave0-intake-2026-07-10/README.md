# Codex intake: Wave 0 foundation pack

## Intake result

The user-supplied archive was accepted as the immutable Wave 0 research baseline.

- Source archive: `hea-lth_research_pack_wave0_2026-07-10.zip`
- Archive SHA-256: `BE4E14062E6B314BDE33EF4DFA625E613A6C8A9BE002DD6F595AC80343361F2C`
- Manifest files checked: 18
- Hash or size mismatches: 0
- Unsafe archive paths: 0
- Imported source directory: `../hea-lth_research_pack_wave0_2026-07-10/`

The source directory is preserved unchanged. Derived files and Codex decisions live in this sibling intake directory.

## What is usable now

- 48 source-ledger rows establish evidence provenance and authenticated-data requests.
- 38 competitor rows establish the initial international and Israeli research universe.
- 207 Hebrew/English seed queries establish a broad demand taxonomy.
- 150 unique keyword-to-URL hypotheses provide a preservation-first starting map.
- 47 preliminary risks establish cross-functional control points.
- The continuation ledger and prompts make the unfinished waves explicit.

## What is not approved yet

Wave 0 does not prove search volume, CPC, keyword difficulty, rankings, traffic, market size, conversion, provider supply, legal eligibility, or financial outcomes. It also does not contain the required localized SERP evidence file, live crawl, Search Console mapping, backlink history, or authenticated Semrush metrics.

Therefore:

- no destructive URL, slug, canonical, or redirect change is approved;
- no candidate URL is approved for bulk generation or publication;
- no medical claim is approved without a claim-level source and assigned review tier;
- no provider is verified merely because a public profile exists;
- no lead route is approved until consent, minimization, recipient, partner status, and outcome logging are defined.

## Schema normalization

The source map uses expanded Wave 0 column names. The derived map conforms to the `hea-lth-seo-operator` contract:

- `existing_or_new` becomes `url_state`;
- `keep_create_merge_hold_noindex` becomes `decision`;
- `SERP_research_status` and `Semrush_status` are normalized to lowercase contract names;
- `cannibalization_notes` becomes `notes`.

All 129 `create candidate` source decisions become `hold` in the governed derived map. This is deliberate: they become `create` only after the existing URL inventory, localized SERP overlap, Search Console, Semrush, backlinks, content equivalence, medical review, and migration gates pass.

## Next evidence connections

1. Authenticate Google Search Console and export page/query/indexing data.
2. Authenticate GA4 and document conversion, consent, and health-data leakage controls.
3. Authenticate Semrush and populate volume, CPC, KD, rank, trend, and competitors without invention.
4. Run a full crawl and reconcile sitemaps, canonicals, noindex, redirects, structured data, and internal links.
5. Import backlink and historical URL exports.
6. Complete dated Google Israel mobile and desktop SERP records for the first money-page clusters.
7. Reconcile CRM lead routing and outcome data before monetization rollout.

The deployment pipeline is the implementation transport for approved changes; it does not bypass these SEO, medical, privacy, or migration gates.

## Authenticated GSC correction

The 2026-07-10 authenticated Search Console snapshot proves that the Wave 0 URL map is not a complete legacy inventory. All five leading pages in the three-month report are absent from the governed map:

- `/best-cosmetics/`
- `/hair-loss-prevention-treatments-costs/`
- `/hair-transplantation-guide/`
- `/טיפול-בחמצן-במימון-קופת-חולים/`
- `/mount-sinai-health-system/`

These URLs are now mandatory `legacy-review` preservation records. They must not be replaced, merged, redirected, or absorbed into a new taxonomy until page/query, canonical, backlink, content-equivalence, conversion, and localized SERP evidence is complete. See `../gsc/2026-07-10/`.
