# Competitive benchmark — world + Israel (2026-07-13)

Rendered captures in session scratchpad `design-qa/bench-*.png`. Sources listed
at the end. This is the standard we design against; every point below is from a
real screenshot or a cited source, not memory.

## 3D anatomy leaders

**BioDigital Human** (biodigital.com) — the category king (6,000+ structures).
- Hero shows a **realistic body with skin and muscle** across devices (desktop,
  VR, phone), not a bare skeleton. Headline "Powering the World's Understanding
  of the Human Body," red "Get Started for Free" CTA.
- Documented interaction (developer docs + support): **click a body part → a
  top-right panel shows that structure and related health conditions → click a
  condition for detail + a source citation.** This is exactly the owner's
  "click a part → services/products for that part" vision.
- Embeds via iframe/URL; "Interact in 3D" overlay button on a blurred preview.

**Zygote Body** (zygotebody.com) — free consumer version of the Zygote model.
- A **photoreal full human (skin surface, clothed figure) fills the entire
  viewport**. Left vertical toolbar = system layers (skeleton, muscle, organs,
  vascular, nervous…). Right panel = orbit/capsule controls + "Discover more"
  category list. Search box top-right.

**Takeaway for us:** the flagship must (1) show a **real body with skin**, not a
skeleton; (2) **fill the view / sit above the fold**; (3) **click part → panel of
connected content/services**; (4) offer **system-layer toggles**.

## Global content portals

**Healthline** (healthline.com)
- Prominent **E-E-A-T trust bar**: "130 medical reviewers in network," "19 years
  of experience," "50 million monthly readers," "Medically reviewed content."
- **Real human portrait photography on every topic card** ("Explore by health
  topic": Anxiety & Depression, Digestive Health, Heart Health, Menopause), each
  a portrait on a colored panel.
- Trending sidebar with thumbnails; newsletter capture with a photo; Tools band.

**Mayo Clinic** — (capture blocked by bot wall; from prior research) reference
IA: symptom→condition→care, doctor finder by name/specialty/location, deep
procedure library. E-E-A-T through named clinical authorship.

## Israeli market (direct RTL competitors)

**Clalit** (clalit.co.il) — the RTL design bar.
- Utility bar: language switcher (FR/EN/RU/AR), search, contact CTA, member area.
- Service menu: זימון תורים · תוצאות בדיקות · פנייה לרופא/ה · מה מגיע לי? · איתור שירותים.
- Hero: 6 icon service tiles + a **login/personal-area box**.
- "**פשוט להיות בריא**" content grid: 5 category cards, each with a **real photo**
  and a colored category chip (סגנון חיים, מידע וזכויות, הריון/לידה, מדריכים
  רפואיים, בריאות לכל גיל). Promo banners with lifestyle photography throughout.

**Infomed** (infomed.co.il) — our closest analog (independent health-info portal +
provider directory; monetizes doctor listings).
- Rich **mega-menu with real medical taxonomy**: חיפוש רופאים · מילון רפואי ·
  תרופות · מחלות · ניתוחים וטיפולים · תוכן · פורומים · **מחשבונים** (calculators).
  Second row = specialties (אסתטית, אורולוגיה, אורתופדיה, משלימה, עיניים, עור
  ומין, שיניים).
- **Doctor cards = the monetization**: photo, star rating, review count,
  call/message CTA ("קראו עליי").
- Category icon grid (בדיקות, מומחים, ויטמינים, טיפולים, חיסונים, תרופות, מחלות),
  podcasts (שומעים רפואה), video (רואים רפואה), news, real photos everywhere.

## Our gaps vs. this bar (honest)

| Dimension | Leaders | Hea-lth today | Severity |
|---|---|---|---|
| Imagery | Real photos on every card | **Zero images** — text/gradients only | **High** |
| 3D body | Real skin body, above fold, click→content | Skeleton, mid-page, small | **High** |
| Menu depth | Diseases/drugs/procedures/calculators/specialties | Thin nav, mostly gated | **High** |
| E-E-A-T signals | Reviewer counts, ratings, authorship shown | Not surfaced on homepage | Med |
| Provider cards | Photo + rating + contact (monetization) | Gated empty states | Med (needs real data) |
| SEO schema | Rich JSON-LD, OG imagery | Minimal | Med |

## Design decisions this drives (build plan)

1. **3D above the fold** as the hero centerpiece + a live **services/products
   panel** beside it (BioDigital pattern), on the homepage, additive to the
   frozen viewer.
2. **Real body asset**: export Z-Anatomy skin/muscle layers so the model reads
   as a human body, not a skeleton (existing pipeline, additive).
3. **Imagery system**: real photography on topic/category cards — **needs an
   owner licensing decision** (stock license vs. commissioned). Until then, build
   the slots with tasteful medical-gradient placeholders, never fake photos of
   fake people presented as real.
4. **E-E-A-T trust bar** using only true, sourced facts.
5. **Richer mega-menu** reflecting the real taxonomy (guides, glossary,
   treatments, diagnostics, specialties) — routes that now resolve (200).
6. **SEO/JSON-LD + OG** layer.

## Sources
- https://www.biodigital.com/ · https://developer.biodigital.com/docs/widget/getting-started/embed-content · https://support.biodigital.com/hc/en-us/articles/360025502533-BioDigital-Human-glossary
- https://www.zygotebody.com/
- https://www.healthline.com/
- https://www.clalit.co.il/ · https://www.maccabi4u.co.il/ · https://www.infomed.co.il/
