# Claude review — Codex B2B layer (PRs 17–28, plugin 0.10.0 → 0.18.0)

**Date:** 2026-08-18 · **Reviewer:** Claude Fable 5 (machine 777) · **Scope:** diff `66d33fe..HEAD` — 77 files, +6,317/−135 · **Method:** full receipt read + code map by a read-only exploration agent, findings then live-verified from machine 777 where remotely possible. No code was changed in this session.

**Evidence classes:** `eyes` = verified live (HTTP/screenshot, this session) · `code` = verified by reading the diff at the cited line · `unverified` = requires server access or elapsed time.

Screenshots for this review: `docs/qa-evidence/live-0180-{medical-equipment,supplier-nubway,biology,supplier-join}-2026-08-18.png` (all looked at, not just captured).

---

## Verdict in one paragraph

Codex shipped a coherent, well-engineered B2B marketplace (security hygiene on handlers is broadly correct, PHP 7.4-clean, no raw SQL) — but it systematically **routes around the product gates that make this site defensible**: nine auto-generated YMYL science pages self-certify their own editorial approval, two real companies are published as "verified" by a seeding script, the medical-information boundary block is absent from every new public surface (and was removed from `/health-technology/`), lead release to commercial suppliers bypasses the audited lead-route registry, and the primary lead funnel can silently drop submissions. Nothing here is malicious; all of it is the difference between "renders correctly" and "institutionally honest," which AGENTS.md calls the product.

---

## CRITICAL

**C1 · Brokerage agreement documents written to disk, location host-dependent — `unverified` on host**
`plugin-src/hea-lth-platform-core/includes/class-hea-lth-brokerage-agreement.php:293-298` — signed agreement HTML (supplier name, commission %, minimum fee, account id, SHA-256 fingerprints) is written via `wp_tempnam()` with a deterministic name (`HP-BRK-{request}-{stamp}-{hash10}.html`) for email attachment, then unlinked. On hosts where `get_temp_dir()` resolves under `wp-content/uploads/`, the file is HTTP-fetchable during the mail window — and permanently if the unlink is skipped (mail fatal). Recreated on every cron retry. **Next step (needs wp-admin/server): determine `get_temp_dir()` on UPress; if under uploads, treat as active exposure and rewrite to a guarded directory or in-memory attachment.**

**C2 · Every B2B lead rides on one unlogged `wp_mail`; contact fields have no admin UI — `code`**
`class-hea-lth-b2b-intake.php:171-182` emails the lead to `admin_email`; `class-hea-lth-platform-core.php:246,258` gives `hp_b2b_request` no custom-fields support, no REST, and no metabox renders `hp_contact_*`. If that one mail is lost (filter, spam, outage) the lead exists in the DB but is invisible to the owner — no retry, no failure record, no fallback view. This is the revenue funnel's single point of failure.

**C3 · Public B2B form nonce + timestamp inside a full-page-cached response — `code`, mechanism live-`unverified`**
`theme-src/hea-lth-portal/template-parts/b2b-intake-form.php:54-56` embeds `wp_nonce_field` + `time()`; the deploy manifest purges an ezcache full-page cache. A page cached >12–24h serves an expired nonce → every real submission bounces to a generic "check your fields" error with zero server-side trace. Verify by comparing the rendered nonce ≥25h apart; fix by AJAX-fetching the nonce or excluding these pages from cache.

## HIGH

**H1 · Nine new public YMYL pages self-certify editorial review — `eyes`**
`class-hea-lth-page-provisioner.php:202-204` stamps `hp_editorial_state=approved` + `hp_last_reviewed=2026-08-13` at provisioning time on `/biology/` (+4 spokes), `/longevity-medicine/`, `/skin/`, `/health-technology/{biomarkers,ai-robotics}/`. Live `/biology/` renders "נבדק לאחרונה: 2026-08-13" — a review that never happened. Copy is hard-coded in `class-hea-lth-knowledge-graph.php:48-148`, sourced to 4 generic institutional URLs reused across all nine. AGENTS.md gate 2 is being satisfied by manufacture, not by review. **There is also no `docs/agent-sync/` receipt for this PR (#20) — the only undocumented shipment in the era.**

**H2 · Provisioner now rewrites templates/metadata on pre-existing pages — `code`**
`class-hea-lth-page-provisioner.php:297,363-372` — existing pages on blueprint paths get their template switched (if default) and editorial metadata seeded. Breaks the recorded "never overwrites existing pages" contract.

**H3 · Lead PII released to commercial suppliers via a parallel, unaudited gate — `code`**
`class-hea-lth-supplier-portal.php:253-257` hands name/phone/email to the supplier's browser gated only by `Hea_Lth_Brokerage_Ledger::can_release()` (commercial acceptance), never by `Hea_Lth_Lead_Route_Resolver` (zero references in new code) — bypassing AGENTS.md gate 4 (verified recipient, capacity, consent version, audit date). The consent copy the buyer signs (`b2b-intake-form.php:78`) does not disclose commercial handoff under a commission agreement.

**H4 · Two real companies published as Hea-lth-"verified" by a seeding script — `eyes`**
`class-hea-lth-showroom-provisioner.php:22-46,140-141,178-181` hard-codes NUBWAY/Nicro + Galaxy (real phones, real addresses), stamps `hp_public_state=verified`, `hp_last_verified=2026-08-13`, `hp_editorial_state=approved`, source = the supplier's **homepage**. Live `/suppliers/nubway/` renders it all, including "פרטים עודכנו: 2026-08-13". 20 machines with third-party trademarks (PicoSure, ARTAS, miraDry…) published under distributor attribution with no permission record. "Verified" is currently an assertion by code, not a process.

**H5 · Public REST + sitemaps expose the new types with no state filter — `eyes`**
`class-hea-lth-platform-core.php:254-260,277`. Live-confirmed: `/wp-json/wp/v2/hp_supplier` → 2, `hp_equipment` → 20, `hp_clinic_plan` → 1; `sitemap_index.xml` lists all three type sitemaps. Today the records happen to be the approved set; the gate exists only in templates, so the first pending/unapproved published record leaks by design.

**H6 · Equipment detail page has no editorial gate at all — `code`**
`single-hp_equipment.php:3-8` renders any published record regardless of `hp_editorial_state` — on the canonical model-intent URL. The index filters (`template-medical-equipment.php:23-24`); the detail page doesn't.

**H7 · Medical-information boundary absent from every new public surface; `/health-technology/` gutted — `eyes` + `code`**
`template-health-technology.php` went 74→21 lines: boundary block, catalog-honesty section, and the WP loop (owner-editable body + title) all removed. Boundary render call present on all 11 legacy content templates, absent from all 8 new public B2B/science templates. Live screenshots confirm no boundary on `/medical-equipment/`, `/suppliers/nubway/`, `/biology/`.

## MEDIUM

**M1 · Unverified supplier/plan profiles render full content under a 404 status — `code`** — `single-hp_supplier.php:4-8`, `single-hp_clinic_plan.php:4-9`: `status_header(404)` without exiting; body still ships. Default state of every new supplier is `pending`.
**M2 · `/skin/` provisioned against the recorded routing decision — `eyes`** — live returns 200 page (no legacy 301 anymore); the decision record says "intentionally NOT provisioned". Silently reversed, justified by a CSV row Codex itself added.
**M3 · Public metrics beacon accepts `b2b_submit` — `code`** — `class-hea-lth-metrics.php:25,41-53` (`__return_true` permission): anyone can inflate the revenue-funnel dashboard or exhaust `MAX_KEYS_PER_MONTH=300` to blind a whole month.
**M4 · Anonymous intake: no rate limit — `code`** — honeypot + 3s floor only (`class-hea-lth-b2b-intake.php:17-31`); unbounded private-post creation + one mail per hit to `admin_email`; a flood buries real leads (compounds C2).
**M5 · Metabox save-order makes release flow need two saves — `code`** — p10 forces `held` before p20 records acceptance (`supplier-portal.php:56,593-595` vs `brokerage-ledger.php:43,229-235`); cross-metabox `$_POST` read at `:237`.
**M6 · English on public Hebrew surfaces — `eyes`** — live-confirmed on `/professionals/supplier-join/`: "Hea-lth for Suppliers", plan names Verified/Showroom/Growth; marketplace tech labels render in English ("Robotic Hair Restoration", "Laser Platform", "AI Skin Analysis"); the language contract test is a fixed 8-string denylist that catches none of it.
**M7 · Theme version split — `eyes`** — `functions.php:16` = 0.17.0 vs `style.css:7` = 0.18.0 (diverged in PR #28). Live homepage serves `portal.css?ver=0.17.0` + `templates.css?ver=0.17.0` — 0.18.0 asset changes ship under a burned cache-bust; returning visitors/CDN keep stale bundles. **Smallest-possible fix, and a natural candidate for the pipeline-verification release once the deploy secret is refreshed.**
**M8 (new, this session) · Source-card typography breaks on LTR titles — `eyes`** — on live `/biology/`, the "מקורות ותאריך בדיקה" card wraps its English source titles one word per line and the text overruns the card boundary (see `live-0180-biology-2026-08-18.png`). Public YMYL page, visibly broken. Likely a narrow flex column + missing `unicode-bidi`/width handling for LTR runs in the sources list.

## LOW / informational

**L1** Preview-harness edit sits inside a freeze-protected section (`tooling/theme-preview/index.php:63-73`) — functionally additive, non-deploying, but inside the fence. **L2** Shipped 3D stack otherwise untouched — verified file-by-file against the freeze list. **L3** PHP 7.4 compat: clean sweep of the new code (no 8.x syntax); battery PHPCS/PHPStan exit 0 on machine 777. **L4** Handler security hygiene broadly correct: nonces + capability checks on all save/admin_post paths, server-side ownership derivation, no SQL, no uploads, consistent escaping. **L5** Archive pagination counts filtered-out records (`archive-hp_supplier.php:19-32`) — short/empty pages once suppliers grow. **L6** `?equipment=/&supplier=` parameter variants of clinic-build are crawlable at scale (no canonical handling; noindex scoped to directory template only). **L7** Provisioners run `wp_insert_post` + `flush_rewrite_rules` inside front-end `init` on version bump — first-hit race/latency. **L8** All 8 new contract tests are source-text `strpos` greps, not behavior tests — they were green while H5/H6/M1 shipped.

## Live route sweep (all `eyes`, 2026-08-18)

All 10 new public routes return 200: `/medical-equipment/` (resolves to the page, no CPT-rewrite collision), `/suppliers/`, `/suppliers/nubway/`, `/biology/`, `/skin/`, `/longevity-medicine/`, `/health-technology/`, `/professionals/{supplier-join,supplier-portal,clinic-build}/`. `/professionals/supplier-portal/` **does** carry `noindex, follow` (a stricter first grep false-alarmed; corrected). Healthchecks: platform 0.18.0 + portal child, same deployment id.

## Proposed triage (owner decides priorities)

1. **C1** — one server-side check of `get_temp_dir()`; rewrite attachment handling if it lands in uploads.
2. **C2 + C3 + M4** — make the lead funnel loss-proof: admin-visible lead list, mail-failure logging, cache-safe nonce, basic rate limit. One coherent fix.
3. **H1 + H7 + M8** — YMYL honesty: real review pass over the 9 science pages (or downgrade their stamps), restore the information boundary on all new public templates, fix the sources-card typography.
4. **H4** — owner decision: either record a real verification process for NUBWAY/Galaxy (calls, permission for trademarks/contact data) or drop the "verified" stamp to a neutral state.
5. **H5 + H6 + M1** — close the three template-layer bypasses (REST filter, detail-page gate, hard 404).
6. **H3** — route supplier releases through the lead-route registry + fix consent copy.
7. **M7** — trivial version-constant bump; ideal first deploy to verify the rotated `WP_APP_PASSWORD`.

Dependabot PRs #29/#16/#12 intentionally left unmerged until the deploy secret is refreshed (merging touches trigger paths).
