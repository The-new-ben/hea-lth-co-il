# Content Governance and Master Brief — Foundation

**Status:** Foundation operating standard. Specialty-specific clinical review rules and legal validation remain pending.  
**Applies to:** encyclopedia, glossary, procedures, tests, drugs, devices, price pages, comparisons, provider/product content, news/innovation, video, 3D, AI-generated assistance, and commercial landing pages.

## Governance objective

Every indexed medical or premium-health page must be:

- useful for a distinct user task;
- supported by an explicit evidence pack;
- reviewed at the appropriate medical-risk level;
- transparent about authorship, review, conflicts, advertising and limitations;
- connected to verified entities and appropriate next steps;
- monitored, correctable and expirable.

The workflow controls claims, not merely prose.

## Roles and separation

| Role | Accountability | Must not control alone |
|---|---|---|
| Owner/GM | Strategy, resources, risk acceptance | Medical conclusions or sponsored ranking |
| Medical governance lead | Review policy, reviewer qualifications, escalations, corrections | Sales targets |
| Medical editor | Evidence pack, claim ledger, editorial QA | Provider commercial terms |
| Clinician reviewer | Accuracy, clinical limitations, urgent routing | SEO or sponsorship placement |
| SEO lead | Demand, intent, canonical, internal links, technical QA | Medical approval |
| Product/UX | User task, disclosure, states, accessibility | Clinical recommendation |
| Commercial lead | Provider/supplier value and contracts | Editorial conclusions or organic ranking |
| Privacy/security | Data purpose, consent, access, retention, incident controls | Growth override |
| Legal adviser | Israeli legal review and launch conditions | Clinical judgment |
| Publisher | Final release gate and accountability | Waiving required reviews |

## Page risk tiers

Use M0–M4 from `33_RESEARCH_PLAN_AND_OPERATING_SYSTEM.md`.

Examples:

- provider login help: M0;
- “how private reimbursement works” general navigation: M1–M2 depending claims;
- symptom/condition/test pages: M2;
- Botox, surgery, fertility, drugs, devices, longevity, price and comparison pages: M3;
- patient-specific triage, drug or treatment selection: M4.

## End-to-end workflow

1. **Opportunity selection**
   - user task and commercial role;
   - existing URL check;
   - expected unique value;
   - risk tier and reviewer capacity;
   - supply/data prerequisites.

2. **Legacy URL assessment**
   - current page, traffic, queries, links, conversions, canonical and history;
   - no create/merge/redirect decision without required data.

3. **SERP and user-task capture**
   - query, Hebrew language, geography, device, date;
   - organic/paid/local/AI/video/image/PAA/shopping features;
   - page type and trust patterns.

4. **Competitor coverage matrix**
   - topics, questions, evidence, assets, tools, conversion;
   - do not copy prose or structure mechanically.

5. **Evidence pack**
   - clinical guidelines/systematic reviews/authoritative references;
   - exact claims and limitations;
   - Israeli applicability;
   - patient population;
   - date and expiry.

6. **Content brief**
   - intent, scope, exclusions, entities, questions, page modules;
   - target reading level and Hebrew terminology;
   - conversion and emergency rules;
   - schema candidates;
   - evidence and original asset requirements.

7. **Draft**
   - claim IDs embedded in working copy;
   - no unsourced numbers;
   - AI assistance disclosed internally.

8. **Fact check**
   - verify every material claim, number, brand, product, regulatory status and link;
   - compare draft to claim ledger.

9. **Medical review**
   - reviewer sees source pack and exact claim set;
   - reviewer records approval, edits, limitations and expiry.

10. **Legal/privacy/commercial review**
    - required for M3/M4, advertising, reviews, before/after, intake, products, finance, pharmacy, devices and sponsored content.

11. **Design/media**
    - consent, provenance, alt text, captions, editing disclosure, still/video/3D fallback;
    - no simulation implying a guaranteed outcome.

12. **SEO/conversion QA**
    - canonical, index state, metadata, internal links, schema/visible parity;
    - CTA appropriateness and sponsor separation.

13. **Publication**
    - named author/reviewer, review date, source summary, disclosures, correction route.

14. **Monitoring**
    - rankings/traffic are not the only signals;
    - complaints, corrections, outdated claims, provider/product status and conversion quality.

15. **Correction/re-review**
    - expedite safety-critical correction;
    - preserve version history and retraction note where appropriate.

## Claim ledger minimum schema

| Field | Description |
|---|---|
| claim_id | Stable ID |
| page_id/url | Canonical content record |
| exact_claim | Atomic claim, not a whole paragraph |
| claim_type | Definition, prevalence, benefit, risk, duration, price, status, comparison, eligibility |
| evidence_label | VERIFIED FACT, PUBLIC CLAIM, ESTIMATE, etc. |
| source_ids | One or more ledger IDs |
| population/geography | Who/where evidence applies |
| evidence_type | Guideline, systematic review, RCT, regulator, registry, official product info, observed price |
| limitations | Uncertainty, disagreement, selection, generalizability |
| reviewer | Named qualified reviewer |
| approval_date | Date approved |
| expiry/review_date | Required re-check |
| commercial_conflict | Sponsor/provider/product relationship |
| correction_status | Open/closed/retracted |

## Reviewer qualification rule

- M0: trained editor/operations owner.
- M1: medical editor or clinician depending actionability.
- M2: licensed clinician with relevant general competence; specialist for contested or high-consequence topics.
- M3: relevant specialty reviewer, with pharmacist/device/legal expertise where applicable.
- M4: formal clinical-safety and regulatory team; ordinary editorial review is insufficient.

Reviewer capacity determines publication capacity. Do not publish faster than the qualified review system can sustain.

## Update and expiry

Use risk- and volatility-based intervals rather than a universal annual date.

| Content type | Trigger examples |
|---|---|
| Emergency/safety/drug/device status | Immediate event-driven monitoring |
| Price/availability/provider status | Frequent refresh based on observation source |
| Guidelines/treatments/diagnostics | Guideline update, material evidence change, scheduled review |
| Stable anatomy/glossary | Longer scheduled interval plus source-version monitoring |
| Technology/AI/WordPress/regulation | Version/release/regulatory event |
| News/innovation | Clear publication date; do not silently evergreen unsupported claims |

A displayed “updated” date must correspond to a substantive review, not a cosmetic timestamp change.

## Corrections and retractions

- public correction channel;
- triage by safety, legal, privacy and reputational impact;
- immediate unpublish or warning for dangerous claims;
- versioned change log for material corrections;
- notify affected providers/suppliers/users where required;
- preserve internal evidence and decision history;
- retraction page when content should not silently disappear.

## Conflicts and sponsorship

- editorial inclusion cannot be contingent on payment;
- sponsored modules must be labeled at the point of exposure;
- sponsor cannot approve medical conclusions;
- provider payment cannot create an unqualified “best” ranking;
- affiliate/commerce relationships must be disclosed;
- reviewer/provider conflicts must be captured;
- before/after and testimonials are not evidence of typical results.

## AI assistance boundaries

Allowed with human accountability:

- source discovery;
- extraction into a claim table;
- outline alternatives;
- Hebrew terminology support;
- consistency, broken-link and structured-field checks;
- draft assistance using the approved evidence pack.

Not allowed as final authority:

- inventing or “filling in” evidence;
- patient-specific advice;
- deciding clinical suitability;
- validating its own output;
- translating licensed terminology where the license prohibits adaptation;
- producing bulk pages without unique-value and review gates.

All AI-assisted claims must resolve to human-approved sources and reviewers.

## Master content brief template

### A. Record

- brief ID:
- owner:
- target URL:
- existing URL status:
- primary Hebrew query:
- English meaning/transliteration:
- user task:
- funnel role:
- medical risk tier:
- monetization allowed/forbidden:
- reviewer required:
- publication gate:

### B. SERP evidence

- query/device/location/date:
- result types and features:
- dominant page type:
- repeated entities/questions:
- trust/evidence patterns:
- unique assets:
- conversion patterns:
- unanswered tasks:
- overlap/cannibalization notes:

### C. Scope

- page promise:
- includes:
- excludes:
- urgent/emergency route:
- intended audience:
- reading level:
- Hebrew terminology/synonyms:
- related entities:
- prerequisite pages/data/supply:

### D. Evidence pack

For each claim:

`claim_id | exact claim | source_id | evidence type | population/geography | limitations | reviewer required | expiry`

### E. Required modules

Choose only those relevant:

- concise answer summary;
- definition;
- anatomy/entity context;
- who may consider it;
- who may not;
- alternatives;
- what happens before/during/after;
- benefits and evidence;
- risks and warning signs;
- recovery/timeline;
- questions for the clinician;
- price method and inclusions;
- payer route;
- provider/product comparison;
- verified supply;
- sources/reviewer/disclosures;
- correction and update history.

### F. Conversion

- appropriate CTA:
- information required at first contact:
- information explicitly not requested:
- recipient and routing:
- response SLA:
- anonymous/account journey:
- sponsor separation:
- event names:
- quality and risk metrics:

### G. Technical/SEO

- canonical:
- parent hub:
- required inbound/outbound links:
- breadcrumbs:
- schema candidates:
- structured data fields:
- index/noindex condition:
- media:
- accessibility/RTL:
- performance budget:
- monitoring query set:

## Drafting prompt template

> Use only the supplied brief, claim ledger and approved source extracts. Preserve all Hebrew terms exactly. Draft for the specified user task and risk tier. Do not add numbers, medical claims, provider claims, product status, prices or legal conclusions not present in the claim ledger. Clearly state uncertainty and limits. Do not imitate competitor prose. Do not diagnose, select treatment, recommend medication, or imply guaranteed outcomes. Insert claim IDs in brackets after every material factual statement for editorial verification. Use the specified CTA and do not request prohibited health data. Return a final section listing unresolved evidence gaps rather than filling them.

## Thin-page and indexation threshold

A page should not be indexed merely because an entity exists. Minimum unique-value candidates include:

- a reviewed answer to a distinct task;
- sufficient authoritative evidence;
- meaningful relationships and navigation;
- original Israeli data, inventory, media, or tools where relevant;
- no near-duplicate canonical target;
- active owner and review cadence.

Entity stubs may exist in the database for relationships/search while remaining noindex or unpublished.
