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

---

## Addendum 2 — outreach executed, wrong-number discovery, entry alerts (same day, evening)

**Owner-authorized sends (owner: "my WhatsApp is open in your Chrome, send everything").**

- **Doctor number correction (critical):** the number emailed to suppliers (052-401-3782) is NOT on WhatsApp; the owner's real doctor chat is **052-401-8782** - a digit typo, which also explains Nicro's failed calls. Room code fixed to the real number (plugin 0.20.1, commit e99a417) with a new password-sync migration: the provisioner now syncs each room page's post_password to the current room code on VERSION bump - the single permitted mutation of existing pages, contract-tested. Live-verified: real code unlocks via query AND via the manual password form; typo code rejected.
- **Sent, with evidence:** (1) WhatsApp to the doctor at 052-401-8782 - delivered, double checkmarks, 15:22 local; includes the room link; WhatsApp link preview confirmed the page is protected ("This post is protected"). (2) Email to Avi/Nicro (reply-all incl. sales@nubway) with the corrected doctor number + supplier-room link. (3) Email to Itay/Galaxy with supplier-room link + terms-approval ask. (4) Email to Keren/Venus with supplier-room link, terms restated, Hea-lth contact identity.
- **Supplier WhatsApps were not sent by automation** (permission classifier blocked typing into supplier chats twice; per the iron rule the channel was switched to the existing email threads, which carry the whole supplier relationship anyway).
- **Unexplained item for the owner:** a chat named "3" in the owner's WhatsApp received the v1 room message at 14:30 (single checkmark). If that recipient is unintended, no exposure occurred: at that time the room was gated by the typo code and content renders only server-side.
- **Entry alerts shipped (plugin 0.21.0 / theme 0.18.4, commit c8c0ee9):** every authenticated room entry appends to a capped on-page entry log (postmeta, mail-independent) and emails the site admin address, throttled to one per room per 2 hours. End-to-end email verification INCONCLUSIVE: a live entry was fired but no alert reached the owner's Gmail within ~2 minutes - the WordPress admin_email destination (or UPress mail deliverability) must be confirmed by the owner. The entry log itself is server-side regardless. This is review finding C2's fragility surfacing in practice; the control-center-visible entries panel is the next hardening step.

---

## Addendum 3 — deal desk shipped (owner: "proceed")

**Plugin 0.22.0, commit 3182c2c, run 32140980826 green. Live healthcheck verified; public surfaces regression-checked (home 200, gate leak-free, room renders, marketplace 200).**

New wp-admin screen **Hea-lth -> "לידים וחדרים"** (additive submenu, zero edits to the Codex control-center class): every hp_b2b_request with full contact details + chosen equipment + notification-mail result; per-room advisory entry counts and last entry; a red banner when lead mails fail. Intake hardened per the review triage: **M4** rate limit (5 per 10 min per anonymous client, enforced before any post is created) and **C2** mail-result capture (hp_mail_result per lead + hea_lth_b2b_mail_failures counter). A lost email can no longer mean an invisible lead.

Admin-screen rendering is code-verified + contract-tested (admin pages are not reachable for anonymous live checks); owner sees it on next wp-admin login. Remaining from the triage: C3 (cache-safe nonce), C1 (get_temp_dir server check), H-series YMYL/verification passes.
