# ChatGPT Pro / Deep Research — Israel market data collection for Hea-lth (paste as one task per block)

You are collecting REAL, verifiable market data for hea-lth.co.il, a Hebrew private-health portal. Output machine-readable JSON/CSV only. Every row needs `source_url` + `retrieved_date`. Mark anything unverifiable as `UNKNOWN` — never invent names, prices, or coverage. Hebrew values in `he` fields, English in `en`.

## Block A — Private clinics & hospitals (Israel, complete sweep)
All private medical centers, hospitals with private tracks (שר״פ), and clinic chains: Assuta network, Herzliya Medical Center, Ramat Aviv Medical Center, Elisha, Horev, private aesthetic/dermatology/hair chains, private imaging institutes, IVF/dental/ophthalmology chains — and independents per city.
Fields: `name_he, name_en, type(hospital|clinic|imaging|chain_branch), specialties[], city, address, phone, website, booking_url, private_track(yes/no), accepts_shaban(list of funds if published), source_url, retrieved_date`.

## Block B — Private doctors by specialty
For the top demand specialties (plastic surgery, dermatology, ENT, ophthalmology, orthopedics, gynecology, urology, hair restoration): senior specialists with public private-practice presence. Cross-check names against the Israeli MoH public practitioner registry (רישום מקצועות רפואיים) and include license number where published.
Fields: `full_name_he, specialty_he, subspecialty, license_no(or UNKNOWN), clinic_affiliations[], cities[], website_or_profile_url, shaban_agreement_funds(if published), source_url`.

## Block C — שב״ן entitlement tables (the comparison engine fuel)
For each fund tier — מכבי שלי, מכבי זהב, כללית מושלם זהב, כללית פלטינום, לאומית זהב, לאומית כסף, מאוחדת עדיף, מאוחדת שיא — extract from the OFFICIAL תקנון/benefit pages: surgeon-choice surgery terms (copay amounts or formula, contracted-list rules, waiting/approval), second-opinion reimbursements, private imaging benefits, aesthetic exclusions, physiotherapy/paramedical, dental. Include exact quote + page URL per benefit.
Fields: `fund, tier, benefit_category, benefit_he(verbatim summary), copay_or_reimbursement(exact if published), conditions_he, exclusions_he, source_url, retrieved_date`.

## Block D — Suppliers & products (store pipeline)
Israeli suppliers/distributors in: post-surgery recovery equipment, clinical skincare (medical-grade brands sold in IL), home monitoring devices, hair-restoration aftercare. Also global DTC leaders per category for benchmark.
Fields: `company_he/en, category, product_lines[], b2b_or_b2c, israel_distributor(yes/no), website, contact_page, regulatory_note(אמ״ר registration if applicable/UNKNOWN), source_url`.

## Block E — Competitor teardown
Doctorim.co.il, Rofim.org.il, Infomed, MedReviews, Assuta doctors, Top10Dental-style verticals, plus Zocdoc/Doctolib/DocPlanner/Vezeeta. For each: business model, pricing to providers (if published), booking mechanics, review policy, traffic estimate (state tool), strongest feature, weakest gap vs Hea-lth's planned comparison+3D+store combo.
Fields: `platform, country, model, provider_pricing, patient_features[], provider_features[], gap_vs_healh, source_url`.

Return each block as its own file (blockA.json … blockE.json/csv). I (Claude) will validate every source_url, drop unverifiable rows, and load approved data into the portal's verified-provider and content gates. Nothing publishes unvalidated.
