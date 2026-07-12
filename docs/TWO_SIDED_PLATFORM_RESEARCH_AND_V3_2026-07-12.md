# Hea-lth two-sided platform — research, v3 design, and data plan (2026-07-12)

## Owner directive
The portal must work like a real two-sided marketplace: patients find/compare/book; doctors and clinics manage patients and appointments; plus store/products and HMO (שב״ן) entitlement comparison on the front page. Benchmark above the world's best.

## Verified market research (sources checked 2026-07-12)

**Global model (Zocdoc / Doctolib / Practo):**
- Patient side: search by specialty + insurance + location + real-time availability; verified profiles with reviews; instant booking; telehealth; mobile app. ([Zocdoc for Providers](https://www.zocdoc.com/business/), [Capterra Zocdoc 2026](https://www.capterra.com/p/10032407/Zocdoc/))
- Provider side: profile management, calendar sync, 175+ EHR integrations, intake forms, automated reminders, new-patient acquisition as the paid value. ([Emitrr competitive review](https://emitrr.com/blog/zocdoc-alternative/))
- 2026 trend: matured from "booking apps" into integrated platforms (booking + telehealth + payments + practice management + care navigation). Doctolib ARR €348M FY2024, +22.5% YoY. ([Healthcare.Digital analysis](https://www.healthcare.digital/single-post/practo-zocdoc-doctolib-are-a-wave-of-healthcare-appointment-booking-ipos-on-the-horizon))

**Israeli competitive field:**
- [Doctorim.co.il](https://www.doctorim.co.il/) — booking for ~27,000 doctors, search by specialty/city/HMO. Booking only.
- [Rofim.org.il](https://www.rofim.org.il/) — appointment marketplace. [Infomed](https://www.infomed.co.il/expertsresults/all_specialities/all_subspecialities/all_areas/) — doctor index + content. [MedReviews](https://www.medreviews.co.il/) — doctor reviews. [Assuta doctors](https://doctors.assuta.co.il/) — hospital-owned booking.
- HMO locators (e.g., [Maccabi service guide](https://serguide.maccabi4u.co.il/heb/doctors/)) serve own members only.

**שב״ן (supplementary insurance) facts that power the comparison engine:**
- שב״ן tiers (מכבי שלי/זהב, כללית מושלם/פלטינום, לאומית זהב, מאוחדת שיא/עדיף) allow choosing a surgeon from a contracted list with a copay; copay differs by tier. ([Maccabi Sheli entitlements](https://www.maccabi4u.co.il/insurance-plans/maccabi-sheli/join_maccabi_sheli/), [Maccabi שב״ן overview](https://www.maccabi4u.co.il/healthguide/administrative_terms/supplementary_insurance/))
- Private insurers reimburse beyond שב״ן copays; שב״ן enrollment needs no health declaration; age-based pricing. ([Menora claims guide](https://www.menoramivt.co.il/claims/f-shaban-private-operations), [2bit explainer](https://www.2bit.co.il/%D7%91%D7%99%D7%98%D7%95%D7%97-%D7%9E%D7%A9%D7%9C%D7%99%D7%9D/), [Fresh Concept](https://fresh-concept.co.il/supplementary-insurance/))
- Purely aesthetic procedures (e.g., cosmetic-only rhinoplasty, hair transplant) are generally NOT שב״ן-covered — functional indications are. This distinction is encoded in the compare data.

## The strategic gap Hea-lth exploits
No Israeli player combines: cross-HMO שב״ן entitlement comparison per treatment + private booking + verified reviews + 3D body discovery + store. Doctorim books; HMO sites serve members; nobody answers "מה מגיע לי ומה עדיף לי" across funds on one page. That comparison module is the front-page differentiator (and a lead-gen magnet).

## V3 design (live preview: claude.ai/code/artifact/321cfc1d-9c37-4e1a-ba1f-7cd631d717b4)
Modules verified rendering + interactive in browser: 3D hero (Z-Anatomy skeletal, auto-rotate), search tabs (רופא/טיפול/בדיקה/מרפאה) + "השב״ן שלי" filter, booking app (specialty→clinic→calendar→slot→request; tested end-to-end), track comparison (4 treatments × private/שב״ן/public with HMO-aware rows), doctor-side dashboard (schedule, requests, stats — labeled product demo), store (4 verified-supplier categories, honest "coming soon"), keyless SVG Israel map (6 cities), 6 content cards, mobile bottom app-bar.

Data honesty rules kept: no invented clinic names presented as real (demo labels), no fabricated prices/volumes, aesthetic-vs-functional coverage distinction stated, compare table carries a תקנון disclaimer.

## Production port plan (next block)
1. Port v3 design system into `theme-src/hea-lth-portal` (front-page.php + portal.css + JS modules), 3D via approved manifest (editorial reviewer decision doc 2026-07-12).
2. Booking = consent-first lead request into the existing gated lead-route resolver (no fake instant scheduling until real clinic calendars connect).
3. Compare = content-managed table per treatment, sourced from קופות public תקנונים (data via research prompt below).
4. Map = Leaflet + OpenStreetMap (keyless) behind the existing plugin map gate.
5. Store = category pages first; commerce only with verified suppliers.
6. Full verification (22 PHP contract tests + mjs + pytest + PHP 7.4 + PHPStan/PHPCS + dry-run) → deploy via pipeline → public verification.

## Doctor-side (SaaS) roadmap
Phase 1: verified profile + availability + request inbox (email/SMS notify). Phase 2: calendar management + reminders. Phase 3: patient CRM (external system — no PHI in this repo, per contract). Monetization: subscription per verified provider + qualified-lead pricing, disclosure-first.
