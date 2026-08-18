# Release — advisory rooms (plugin 0.19.0, theme 0.18.2) + room #1 live

**Date:** 2026-08-18 · **Commit:** `cd3a63e` · **Run:** 32130700800 (51s, green) · **Operator:** Claude Fable 5 (machine 777)

## Context (the live deal)

First real brokered deal: ד"ר אחסאן, opening an obesity-treatment/aesthetics clinic, asked (explicitly, by phone to the owner) that equipment procurement be concentrated through Hea-lth — no direct sales calls. Advisory meeting: 2026-08-18 13:00–16:00. Updated needs: (1) fat-reduction/body-contouring system (Cryolipolysis / HIFEM / RF); (2) professional hair-removal laser (priority for opening). Suppliers engaged under the 10% / ₪8–15K + non-circumvention framework: **Nicro/NUBWAY** (Avi Peretz, CEO — accepted terms in writing 08-12, has the doctor's number, materials requested), **Galaxy Medical Technologies** (Itay Gal — engaged, materials requested), **Venus Concept** (Keren Zilberman — terms pending authorized signature per their own email policy; call slated Sunday). No supplier prices received as of this release — the room says so honestly.

## What shipped

`Hea_Lth_Advisory_Rooms` (plugin) + `template-advisory-room.php` (theme) + advisory CSS + contract test. Design properties, each deliberate:

- **Private by construction:** room content renders from class data only — empty `post_content`, no registered meta → nothing for REST, sitemaps, or search to leak. Live-verified: `/wp-json/wp/v2/pages?slug=clinic-2026-001` returns `content: "", protected: true`.
- **Gate:** native `post_password` + one-click `?code=` (digits, `hash_equals`). Code = the client's own mobile number — zero-friction on the call. `nocache_headers()` on every render; noindex meta on the page and the `/advisory/` parent.
- **Honesty gates learned from the B2B review:** equipment cards render only `publish` + `hp_editorial_state=approved` records (closes the H6 pattern on this surface); a transparency/medical-boundary block is present (closes the H7 pattern); the contract test asserts the template contains **no shekel literal** — invented prices are structurally forbidden.
- **Create-only provisioning** (no `wp_update_post` — the H2 pattern is contract-tested away).

## Verification (all eyes, live)

Healthcheck 0.19.0 deployment `gh-cd3a63e…`. Without code: gate only, zero client-name occurrences, `noindex, follow` present. With code: client greeting, 3 supplier tracks, curated equipment with supplier attribution and working links, process tracker, WhatsApp CTA. Battery pre-push: PHP contract 35/35 (incl. new test), node, pytest 19/19, PHPCS 0, PHPStan 0, both packages `dry_run_ok`. Screenshots (looked at): `docs/qa-evidence/live-0190-advisory-room-2026-08-18.png`, `live-0190-advisory-gate-mobile-2026-08-18.png`.

## Room #1

- URL: `https://hea-lth.co.il/advisory/clinic-2026-001/` · one-click: `?code=` + the client's mobile digits.
- Contents: 2 need categories, 3 supplier tracks with real statuses, 9 curated systems in 2 groups, 4-step process, boundary block.

## Next (owner-priority)

Per `docs/BUSINESS_PLAN_MULTITRACK_2026-08-18.md` §C week-1: Venus signature; record all 3 supplier terms as ledger opportunities; collect specs/prices and update the room; lead-funnel fix bundle (C2+C3+M4); C1 server check. Room content updates ship by editing `rooms()` + VERSION bump — pages are never edited by hand.

---

## Addendum — v2 decision toolkit + supplier rooms (same day, owner: "not enough, make it a decision tool")

**Commit `6fbe9b3` · plugin 0.20.0 / theme 0.18.3 · run 32133471649 (46s green) · all verified live (eyes).**

Buyer room upgraded: 3-step how-to strip; concentrated comparison table (9 systems x category/technology/supplier/price-status/interest); per-machine cards with category SVG badges and "why relevant" lines; technology guide (5 categories, claim-free); 7-point pre-purchase checklist; per-supplier quote-status pills; freshness stamp; 20 "interest" CTAs opening WhatsApp to the owner prefilled per machine - the input loop the owner asked for. Price honesty structural: every price field renders "pending supplier quote" until a WRITTEN range arrives (contract test forbids shekel literals in the template).

Three private supplier rooms shipped at /advisory/supplier-{nicro,galaxy,venus}/ (codes = each contact's known number; Venus = office line). Content: anonymized opportunity brief (zero doctor-name occurrences - live-verified in gate AND unlocked states), material asks with first-in-first-shown urgency, terms reminder, their shortlisted machines, WhatsApp/email reply CTAs.

Screenshots (looked at): live-0200-advisory-v2-full-2026-08-18.png, live-0200-supplier-room-nicro-2026-08-18.png.

**Update loop:** supplier sends a written range/materials -> edit rooms() + VERSION bump -> battery -> deploy (minutes). Prices appear only from written supplier quotes.
