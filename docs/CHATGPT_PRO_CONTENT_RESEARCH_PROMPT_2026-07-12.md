# ChatGPT Pro / Deep Research — Hea-lth content engine brief (2026-07-12)

Purpose: produce the structured, source-backed content taxonomy that Hea-lth needs (body regions/organs → treatments → equipment → products), in a shape Claude can ingest directly into the WordPress portal behind its YMYL gates. Run each block below as a separate Deep Research task; return **machine-readable output** (JSON or CSV as specified), not prose.

## Non-negotiable rules for the researcher (read first)

1. **Every medical claim needs a real, citable source** (Mayo Clinic, Cleveland Clinic, NHS, MedlinePlus, UpToDate, Israeli Ministry of Health, peer-reviewed). Put the URL in a `source_url` field and the org in `source_name`. No source → omit the row. Never invent studies, statistics, or outcomes.
2. **No fabricated providers, prices, reviews, or availability.** Those are commercial records Hea-lth adds separately; do not generate them.
3. **Hebrew is the public language** (`he`), calm and factual — no medical promises, no urgency, no "best/guaranteed". Keep an English `en` label for internal mapping. Provide a short `disclaimer_he` where a topic could be mistaken for advice.
4. **Map to standard anatomy.** Where a row concerns a body structure, include its Terminologia Anatomica code from the Hea-lth TA2 map (`design-lab/3d-human-engine/ta2-structure-map-seed.json`, fields `ta2Id`/`english`/`latin`). If unknown, put `ta2Id: "UNKNOWN"` — do not guess.
5. **Output only the requested schema.** No commentary, no markdown around the JSON/CSV. UTF-8. Real newlines.

## Block 1 — Body-region → clinical-context map (JSON)

For each major body region a private-health consumer explores (start with: face/nose, skin, scalp/hair, breast, abdomen, spine, knee/joints, teeth/jaw, eyes), return objects:

```json
{
  "region_en": "nose", "region_he": "אף", "ta2Id": "UNKNOWN_or_code",
  "contexts": [
    {"context_he":"אסתטיקה ושינוי מבני","specialty_he":"כירורגיה פלסטית","specialty_en":"plastic surgery"},
    {"context_he":"נשימה ואף-אוזן-גרון","specialty_he":"אף אוזן גרון","specialty_en":"otolaryngology"}
  ],
  "common_questions_he": ["...3-6 real questions patients ask..."],
  "source_name":"", "source_url":""
}
```

## Block 2 — Treatments / procedures (JSON, this is the priority)

For each private-care treatment relevant to the Israeli market (aesthetic medicine, plastic surgery, hair restoration, dermatology, dental, diagnostics/imaging, bariatric, ophthalmology — expand as coverage allows), return:

```json
{
  "treatment_en":"rhinoplasty", "treatment_he":"ניתוח אף",
  "region_en":"nose", "ta2Id":"UNKNOWN_or_code", "specialty_he":"כירורגיה פלסטית",
  "summary_he":"2-3 sentence factual overview",
  "how_it_works_he":"", "preparation_he":"", "recovery_he":"", "risks_he":"",
  "who_is_it_for_he":"", "questions_to_ask_he":["..."],
  "typical_setting_he":"מרפאה פרטית / בית חולים",
  "disclaimer_he":"מידע כללי, אינו תחליף לייעוץ רפואי",
  "source_name":"", "source_url":"", "reviewed_basis":"e.g. Mayo procedures page",
  "seo": {"primary_kw_he":"", "secondary_kw_he":["..."], "search_intent":"informational|commercial"}
}
```

Target 60–120 treatments in the first pass, ranked by Israeli private-care demand.

## Block 3 — Equipment & technology (JSON)

Medical devices/technologies patients encounter (MRI, CT, ultrasound, laser types, injectables categories, FUE/DHI hair tech, IPL, etc.):

```json
{"equipment_en":"","equipment_he":"","category_he":"דימות|לייזר|הזרקות|...",
 "what_it_is_he":"","safety_notes_he":"","related_treatments_en":["..."],
 "source_name":"","source_url":""}
```

## Block 4 — Product/service categories (JSON, commercial — descriptive only)

Category-level only (no brands, no prices): e.g. "second-opinion service", "private imaging referral", "aesthetic consult". Fields: `category_he`, `category_en`, `what_it_offers_he`, `when_relevant_he`, `related_specialty_he`, `source_name`, `source_url` (source optional here since these are service descriptions, but mark `commercial:true`).

## Block 5 — SEO pillar/spoke + keyword map (CSV)

Align to the existing Hea-lth structure (`docs/SEO_MASTER_PLAN_2026-07-10.md`, `docs/KEYWORD_URL_MAP_SEED_2026-07-10.csv`). Columns:

`pillar_he, spoke_he, keyword_he, monthly_intent(informational|commercial|transactional), suggested_url_slug, primary_region_en, related_treatment_en, competition_note, source_of_volume_estimate`

Do **not** invent exact search volumes; if estimating, cite the tool/basis in `source_of_volume_estimate` or write `UNKNOWN`. Prefer Israeli/Hebrew SERP reality (Clalit, Maccabi, Assuta, Ichilov, private clinics).

## How to return

One file per block, named `block1.json … block5.csv`. Zip or paste each separately. When done, hand the files back to me (Claude) — I will validate every `source_url`, drop unsourced medical rows, map `ta2Id`s against the real TA2 file, and load approved rows into the portal behind the editorial gate (approved review state + review date + visible source). Nothing publishes without that gate.
