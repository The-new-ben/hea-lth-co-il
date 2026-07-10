# Research Plan and Operating System

**Program:** Hea-lth.co.il National Premium Medical Portal and Marketplace  
**Research date:** 10 July 2026  
**Status:** Wave 0 completed; Waves 1–9 queued  
**Effort unit:** Net multidisciplinary researcher-days, not elapsed calendar time and not a commercial quote.

## Program design

The commission is divided into ten waves. The sequence protects legacy SEO value, prevents premature legal or clinical assumptions, and forces commercial strategy to be grounded in Israeli supply, payer routes, demand, and operating capacity.

| Wave | Name | Primary questions answered | Workstreams | Sources and tools | Principal files created or updated | Dependencies | Estimated effort | Public work possible now | Owner/authenticated input |
|---|---|---|---|---|---|---|---:|---|---|
| 0 | Control plane, preservation baseline, and data room | What is the evidence standard? What may not be changed yet? What is visibly present on the current site? What data blocks decisions? | Cross-cutting; partial 2, 4, 7, 10, 24, 34–36 | Owner brief, public site pages, official standards/docs, source ledger, structured templates | 00, 01, 02–07 seeds, 13, 27–35 | None | 5–8 | Yes | Helpful but not required |
| 1 | Israeli market, regulation, payer, and supply map | Where are the real revenue pools, buyer journeys, payer routes, licensing boundaries, and supply constraints? | 1, 3, 18, 24, 36 | Israeli regulators, MOH, CBS, Competition Authority, insurers/SHABAN, hospital/clinic price pages, industry interviews, supplier sources | 02, 04, 12, 27, 28, 30 | Wave 0 controls | 16–24 | Substantial | Contracts, supply lists, prior leads, legal documents |
| 2 | Global/Israeli competitor, product, UX, and monetization benchmark | Which capabilities create trust, conversion, supply liquidity, and defensibility? What should be matched, exceeded, avoided, bought, or partnered? | 2, 25, 26, partial 27 | Official product/help/pricing/policy pages, app stores, structured walkthroughs, accessibility/mobile tests | 03, 05, 16, 23 seed, homepage/menu synthesis memo | Wave 1 market context helps prioritization | 14–22 | Yes | Competitor subscriptions/logins helpful |
| 3 | SEO demand, localized SERPs, legacy URL, and premium vertical portfolio | What do Israelis search, what page types win, which URLs already have value, and which head terms warrant investment? | 4–7, 19 | Google Israel manual SERPs, Search Console, GA4, Semrush/Ahrefs, crawl, backlinks, YouTube/Maps/Shopping captures | 06–08, 12, legacy/canonical/redirect framework | Full owner SEO data room | 24–38 | Partial without metrics | Required for destructive URL decisions and current performance |
| 4 | Medical ontology, content system, E-E-A-T, and answer-engine visibility | How should entities, evidence, authorship, internal links, schema, glossary thresholds, and citation-worthy assets work? | 8–12 | WHO/NLM/terminology licenses, guidelines, medical societies, Google docs, answer-engine tests | 09–14, expanded 13 | Waves 1–3 priorities | 18–28 | Yes | Reviewer roster, editorial capacity, CMS constraints |
| 5 | Provider marketplace, accounts, lead operations, and integrations | How are professionals verified, ranked, onboarded, matched, booked, and monitored without over-collecting health data? | 13–15, 23 | MOH verification sources, provider interviews, scheduling/CRM vendor docs, privacy review | 15, 16, 20, 22, integration appendix | Wave 1 legal/supply findings; Wave 3 demand priorities | 18–30 | Partial | Provider inventory, contracts, CRM, lead events, call/booking data |
| 6 | B2C/B2B commerce, medication, devices, and supplier marketplace | Which categories can transact, which require quote/RFP, and which are legally or operationally unsuitable? | 16–18 | MOH/AMAR/drug/pharmacy sources, supplier catalogs, warranty/returns/shipping/tax/finance sources | 21, product/supplier data model, commerce sections of 15/17/23 | Wave 1 regulation; supplier interviews | 15–24 | Partial | Supplier catalog, margins, fulfillment, payment/tax data |
| 7 | AI, 3D/AR/VR, WordPress/WooCommerce, security, and platform architecture | What can safely launch, what must remain controlled or regulated, and how is the stack kept portable? | 20–22, technical parts of 23–24 | Official WordPress/Woo docs, W3C, OWASP, NIST, WHO, vendor/API docs, threat modeling | 17–19, AI safety case, technical architecture | Product requirements from Waves 4–6 | 17–27 | Yes | Hosting, UPress, GitHub, plugin/theme, data-residency constraints |
| 8 | Business model, unit economics, sales, marketing, moats, KPIs, and team | Who pays, what must be true for attractive unit economics, what supply/demand engine comes first, and what team is required? | 27–33 | Public pricing, owner P&L, lead/order data, provider/supplier interviews, marketing data | 23–26, 24 XLSX, updated 28–30 | Waves 1–7 | 22–34 | Benchmarks only | Required: financials, conversion funnel, costs, contracts, churn/refunds |
| 9 | Integrated roadmap, capital allocation, launch gates, and pre-mortem | What should be funded in each phase, what is reversible, what should be killed, and what could break the venture? | 34–36 and final synthesis | All completed evidence, scenario model, board decision workshop | Final 29, 28, 30, completed ledger, synthesis memo | All prior waves | 10–16 | No meaningful finalization without prior waves | Owner/board decisions |

**Directional total:** 159–251 net researcher-days. The range reflects the number of authenticated datasets, interviews, legal questions, SERP captures, and page-level competitor audits required. It is not a delivery promise.

## Synthesis cadence

A strategy synthesis memo is mandatory after:

- **Wave 2:** market + competitor implications;
- **Wave 5:** demand + ontology + marketplace implications;
- **Wave 8:** economics + platform + go-to-market implications.

Each memo must state which prior assumptions were confirmed, rejected, or narrowed.

## Research evidence architecture

### Source authority levels

| Code | Source class | Typical examples | Default use |
|---|---|---|---|
| A1 | Binding/official Israeli primary | Statute, regulation, regulator guidance, official registry, government dataset | Legal/regulatory facts and verification |
| A2 | International regulator/standards primary | WHO, W3C, NIST, official terminology license, FDA/EMA where relevant | Standards, risk, licensing, evidence framework |
| A3 | Clinical primary/authoritative | Guideline, systematic review, medical society, hospital/university | Medical claims and review pack |
| B1 | Official commercial source | Competitor product, pricing, policy, help, developer, investor pages | Product/business claims, labeled public claim where appropriate |
| B2 | High-quality secondary | Peer-reviewed market study, audited report, reliable journalism | Context, triangulation, disputed issues |
| C1 | First-party owner data | GSC, GA4, CRM, contracts, P&L, provider/supplier records | Performance, unit economics, existing asset decisions |
| C2 | Interview/survey evidence | Provider, patient, supplier, operator interviews | Needs, process, willingness, constraints |
| D | Discovery only | Search snippets, aggregators, Wikipedia, unsourced lists | Leads to primary sources; not sole support for important decisions |

### Claim record

Every material claim should carry:

- claim ID;
- exact claim;
- evidence label;
- source ID(s);
- source date and access date;
- geography and population;
- evidence type;
- limitations or disagreement;
- confidence;
- medical-review level;
- expiry/re-review date;
- owner of the claim.

### Numerical record

Every important number must record:

`value | currency/unit | geography | period | source_id | source_date | accessed_date | evidence_label | method | confidence | sensitivity`

A field remains `DATA REQUIRED` when any of these is unavailable. Global market figures must not be silently substituted for Israel.

## Medical risk and review tiers

| Tier | Typical content or feature | Minimum control |
|---|---|---|
| M0 | Company, administrative navigation, non-medical account help | Editorial QA and privacy check |
| M1 | General health-system navigation, definitions with low actionability | Qualified medical editor or approved authoritative reference set |
| M2 | Symptoms, conditions, tests, treatments, side effects, suitability | Named clinician review; claim ledger; update date; urgent-care routing |
| M3 | Surgery, fertility, drugs, devices, diagnostics, aesthetics, longevity, price/decision pages | Relevant specialist review; commercial-conflict check; legal/privacy review where indicated; higher update frequency |
| M4 | Patient-specific guidance, triage, treatment selection, medication advice, image analysis, outcome prediction | Do not launch as ordinary content/product. Requires an exact regulated model, clinical validation, safety case, human oversight, incident process, and legal approval |

## AI capability classes

- **A — Lower-risk:** semantic search, synonym mapping, evidence retrieval, citation extraction, content QA, translation assistance, neutral navigation.
- **B — Controlled administrative:** appointment assistance, intake summarization, provider response drafting, secure document classification, human-approved follow-up.
- **C — Clinical/high-risk:** symptom assessment, triage, probable conditions, treatment selection, medication advice, image analysis, patient-specific recommendations.

Class C is not a general-purpose LLM product backlog. It is a separate regulated and clinically validated program.

## URL preservation and cannibalization control

1. Inventory all URLs, status codes, canonicals, noindex rules, sitemaps, internal links, traffic, conversions, backlinks, and historical versions.
2. Assign each query to a user task and SERP page type.
3. Compare top-ten overlap for neighboring queries.
4. Use the owner’s heuristic only as an internal triage rule:
   - ≥50% overlap: default to one canonical page;
   - ≤30% overlap plus materially different intent/page type: separation may be justified;
   - 31–49%: manual review.
5. No redirect is approved without content equivalence and destination relevance.
6. Never redirect unrelated pages to the homepage.
7. Preserve a redirect ledger, rollback plan, and pre/post migration measurements.

## Page launch gates

| Gate | Requirement |
|---|---|
| P0 — Demand and uniqueness | Search/user task, canonical intent, non-duplicate value, inventory threshold defined |
| P1 — Evidence | Claim ledger, authoritative sources, limitations, review level |
| P2 — Trust | Named author/reviewer, conflicts, methodology, correction path |
| P3 — Supply/transaction | Verified provider/product inventory, response/availability logic, price method |
| P4 — Legal/privacy/security | Data minimization, consent, advertising, licensing, accessibility, security review |
| P5 — Technical | Crawl/index control, schema validity, RTL, accessibility, performance budget, analytics privacy |
| P6 — Operations | Owner, SLA, moderation, incident, update and re-verification cadence |
| P7 — Measurement | Primary, quality, revenue, and risk KPI with event definition |

## Feature decision template

Every proposed feature must answer:

`user | problem | evidence | business value | medical/compliance risk | data required | inventory required | build/buy/partner | operating owner | failure mode | launch gate | primary KPI | quality KPI | risk KPI`

## Recommended research repository structure

- `/sources/` — archived or referenced primary sources and source notes;
- `/data/raw/` — immutable exports;
- `/data/processed/` — normalized tables;
- `/serp/` — query/device/location/date captures;
- `/claims/` — medical and business claim ledgers;
- `/decisions/` — decision records and reversibility;
- `/deliverables/` — current narrative and tabular outputs;
- `/state/` — continuation ledger and assumptions;
- `/qa/` — audits, broken-link checks, schema tests, accessibility and evidence reports.

Sensitive owner exports require access controls and must not be placed in ordinary shared folders.

## Immediate public-data research

Public work can begin without credentials on:

- official Israeli laws, regulator guidance, registries, service directories, drug/device sources, public price and procurement evidence;
- official competitor product, help, pricing, policy, verification, review, and developer pages;
- manual Hebrew Google Israel SERPs, Maps, video, image, shopping, AI Overview, PAA, and related-search captures;
- terminology licenses and medical-source policies;
- WordPress, WooCommerce, accessibility, security, AI-risk, and web-platform documentation;
- provider/supplier public inventory and geographic presence;
- public payer/SHABAN/private-insurance routes and limitations.

Public research cannot reliably provide Hea-lth’s historical performance, current ranking ownership, conversions, provider response, unit economics, legal agreements, or technical implementation.

## Work that remains blocked without owner data

- final keep/merge/redirect/noindex decisions;
- current cannibalization and ranking-loss diagnosis;
- current lead, booking, show, sale, refund, and revenue economics;
- provider/supplier verification and inventory sufficiency;
- realistic 36-month financial scenarios;
- security and privacy assessment of the live stack;
- plugin/theme/data-model migration design;
- paid-search and customer-acquisition efficiency;
- factual claims about current business operations.
