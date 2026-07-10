# Executive Decision Memo

**To:** Owner and management  
**From:** Research, product, SEO, medical governance, marketplace, commerce, AI, UX, and business-model workstream  
**Date:** 10 July 2026  
**Decision horizon:** Research foundation and staged national-category build  
**Status:** Foundation finding; market, legal, financial, and SEO conclusions remain subject to later waves.

## Decision in one sentence

Proceed with the national ambition, but treat Hea-lth as a **trust-and-supply platform built on a medical knowledge graph**, not as a content-volume project or a generic lead-generation site.

## What is already true

**VERIFIED FACT — current public surface reviewed.** The current website positions itself around private-care coordination, private specialists, second opinions, MRI/CT, insurance reimbursement, home visits, medical aesthetics, plastic surgery, hair transplantation, laser hair removal, skin treatments, price guides, and a developing provider/clinic index. It also presents emergency and non-diagnostic disclaimers.

**VERIFIED FACT — visible URL seeds reviewed.** Publicly reachable or discoverable URLs include the homepage, provider/clinic index, Botox price guide, MRI/CT coordination, laser hair removal, premium-health services, breast augmentation cost, rhinoplasty cost, and links to additional private-care, aesthetics, hair, skin, and insurance pages.

**OBSERVATION — limited to parsed pages reviewed.** Explicit named author/reviewer entities, a claim-source ledger, a reproducible price-observation methodology, a provider-verification methodology, and live provider availability were not visible in the parsed page content used for Wave 0. This is not a statement that they do not exist elsewhere in the stack; it is a control gap to verify.

**VERIFIED FACT — platform environment.** Official WordPress release pages show WordPress 7.0.1 released on 9 July 2026, following WordPress 7.0 on 20 May 2026. WordPress also documents Interactivity, Block Bindings, and Abilities APIs. WooCommerce documents HPOS and a customer-facing Store API. These capabilities create a credible controlled-stack option, but the live site’s actual version, plugins, security state, custom code, and compatibility remain DATA REQUIRED.

**VERIFIED FACT — Google AI search guidance.** Google’s official guidance states that normal foundational SEO practices continue to apply to AI Overviews and AI Mode, that no special schema or AI text file is required, and that eligibility still depends on indexing and snippet eligibility. Therefore GEO/AIO work should focus on evidence, crawlability, internal links, visible text, useful media, accurate structured data, source clarity, and original value rather than unsupported “AI optimization” rituals.

## Strategic thesis

### 1. The venture needs two coupled moats

The first moat is a **medically governed entity and evidence layer**: conditions, symptoms, tests, treatments, procedures, drugs, devices, providers, organizations, places, products, prices, claims, authors, reviewers, and sources connected by explicit relationships.

The second moat is a **verified operating dataset**: provider identity and license status, service inventory, real or request-based availability, response times, observed prices, inclusions, payer routes, verified reviews, supplier documentation, product status, order/quote outcomes, and correction history.

Generic medical prose is reproducible. A governed Hebrew entity graph connected to current Israeli supply and transaction data is substantially harder to copy.

### 2. “Publish everything” is the wrong first move

**RECOMMENDATION.** Do not launch thousands of glossary, city, provider, price, or comparison pages merely because templates can generate them. Each indexed page needs a distinct task, evidence, reviewer coverage, useful inventory or original data, and an internal-link role.

The risk is not only thin-content classification. It is operational: stale prices, empty provider pages, contradictory medical claims, unverified credentials, poor response, and a false impression of national coverage.

### 3. Trust infrastructure precedes aggressive monetization

Before scaling high-risk money pages, establish:

- organization and editorial-board identity;
- named author and reviewer records;
- reviewer qualification rules by risk;
- source and claim ledgers;
- provider and supplier verification policies;
- price methodology and timestamping;
- review eligibility and moderation rules;
- correction, retraction, expiry, and re-review procedures;
- advertising/editorial separation;
- privacy-safe intake and consent;
- incident and complaint ownership.

Commercial conversion should be designed into pages, but it should not outrun the ability to verify what is being sold or referred.

### 4. The first commercial wedge should be a portfolio, not the whole market

Final selection requires Israeli market/supply/demand evidence. The initial validation portfolio should test three different economic and trust models:

| Validation wedge | Why test it | Principal uncertainty | Early revenue route |
|---|---|---|---|
| Private specialist / second opinion / imaging navigation | Clear decision friction, strong need for availability and payer-route clarity, supports a broad trusted brand | Verified inventory, response SLA, insurance/SHABAN complexity | Concierge, qualified request, first booking, provider subscription |
| One high-value aesthetic/surgical cluster | Existing site relevance and strong commercial intent; suitable for price, provider, procedure, recovery, comparison, and media journeys | Competition, advertising rules, review/before-after risk, provider quality variation | Provider subscription, qualified request, labeled sponsorship, booking |
| One product or professional-equipment category | Tests commerce, specifications, supplier data, quote, warranty, and repeat operations | Regulation, catalog quality, fulfillment, service burden, B2B sales cycle | Margin, supplier subscription, RFP/transaction, finance/service referral |

Hair, premium dental, fertility, executive diagnostics, and longevity should be scored against actual supply, demand, evidence, repeat economics, and legal burden rather than selected by intuition alone.

### 5. B2B equipment is strategically attractive but operationally separate

A professional equipment marketplace is not an extension of a patient directory. It has different users, verification, specifications, tax/invoicing, quote/RFP, service, installation, training, warranties, parts, leasing, trade-in, and account permissions. It can become a defensible revenue and data engine, but it should have a separate product owner, supplier-acquisition motion, and launch gate.

### 6. AI should begin with retrieval and administration

**RECOMMENDATION.** Initial AI should be limited to controlled tasks such as semantic search, Hebrew lay/medical synonym mapping, evidence retrieval, citation extraction, content QA, provider-profile completeness, translation assistance, neutral comparison explanation, and administrative navigation.

Appointment and intake agents may follow only with explicit scope, human escalation, access controls, logging, evaluation, privacy review, and failure recovery.

Diagnostic, triage, treatment-selection, medication, medical-image, and patient-specific guidance are a separate regulated/high-risk program. WHO and NIST guidance support governance, human rights, trustworthiness, risk management, and accountability; OWASP identifies prompt injection, sensitive information disclosure, insecure output handling, and excessive agency among relevant GenAI security risks.

### 7. Keep the stack portable and boring at the core

**RECOMMENDATION.** Retain WordPress as the governed content and entity-control plane unless later architecture evidence disproves its fit. Use custom post types/tables and APIs deliberately; avoid plugin sprawl; keep Git as the source of code truth; use staging, automated tests, backups, observability, feature flags, queues, object storage/CDN where needed, and documented data portability.

Use interactive islands for search, comparison, accounts, booking, quote, and 3D rather than turning every page into a fragile headless application. Lovable may be used for design exploration only, consistent with the owner constraint.

## Immediate owner decisions

### Decision 1 — approve the preservation rule

No existing URL or content is deleted, merged, renamed, or redirected until the required SEO and content-equivalence data are available. This includes pages that appear weak or redundant.

### Decision 2 — choose the operating identity

The owner must decide whether Hea-lth will initially be:

- a publisher with referrals;
- a verified provider marketplace;
- a concierge/coordinator;
- a booking intermediary;
- an ecommerce merchant/marketplace;
- a B2B procurement platform;
- or a staged combination with separate terms and responsibilities.

The legal, data, insurance, operational, and revenue model changes materially by role.

### Decision 3 — establish accountable governance owners

Name accountable owners for:

- medical editorial quality;
- provider/supplier verification;
- advertising/editorial separation;
- privacy and security;
- lead/booking operations;
- corrections and complaints;
- AI safety;
- financial model and commercial contracts.

A policy without an operating owner is not a control.

### Decision 4 — authorize the data room

The owner should provide the exports and records listed in `30_OPEN_QUESTIONS_AND_DATA_REQUESTS.md`. Without them, the program can continue public research but cannot responsibly finalize URL migration, unit economics, current performance, supply sufficiency, or financial scenarios.

### Decision 5 — fund reviewer and verification capacity as product infrastructure

Medical reviewers and verification operations are not optional publishing overhead. They are core product capacity. The content and provider growth rate must be capped by qualified review and re-verification throughput.

## What should not be built early

- a general medical-record vault;
- an autonomous symptom checker or clinical assistant;
- prescription medicine checkout;
- patient-specific aesthetic outcome prediction;
- national city/provider pages without verified inventory;
- open reviews without eligibility and fraud controls;
- real-time booking without reliable calendar integration and fallback operations;
- broad product inventory without classification, documentation, warranty, returns, and service;
- a 3D showroom without performance/accessibility fallbacks and asset governance;
- an elaborate headless architecture before the WordPress control plane, data model, and workflows are understood;
- a financial model populated with generic marketplace benchmarks instead of owner and Israeli evidence.

## Earliest defensible operating sequence

1. Preserve and instrument the current asset.
2. Build the source, claim, author/reviewer, provider, price, and consent ledgers.
3. Complete Israeli market/regulatory/supply mapping.
4. Complete competitor and localized demand/SERP evidence.
5. Select two or three launch wedges with anchor supply.
6. Upgrade existing high-value URLs before mass creation.
7. Pilot privacy-safe matching with explicit SLA and outcomes.
8. Publish original price/supply/response datasets only after methodology and sufficient observations.
9. Add accounts, booking, commerce, AI, and 3D in increasing order of operational and regulatory risk.
10. Scale national coverage only when inventory, review, support, and monitoring thresholds are met.

## Board-level success definition

The North Star should not be raw sessions or lead count. A more defensible candidate is:

> **Monthly verified decisions completed:** the number of users who reach a documented, quality-controlled next step—such as a verified provider contact accepted, a booking confirmed, a quote completed, or an eligible product order fulfilled—without breaching trust, medical, privacy, or response guardrails.

This candidate must be refined in Wave 8. Guardrails should include complaint rate, stale inventory, reviewer coverage, verification freshness, provider response, cancellation/refund, privacy incidents, unsupported claims, accessibility, and page-performance budgets.

## Capital-allocation principle

Fund in this order:

1. evidence, data room, verification, governance, and preservation;
2. high-value existing URL improvement and anchor supply;
3. matching/lead/booking operations with measured outcomes;
4. structured entity architecture and authority expansion;
5. differentiated data, comparison, and media assets;
6. commerce and B2B workflows;
7. controlled AI and 3D experiences;
8. national horizontal scale.

The ambition remains national. The execution should be stage-gated rather than simultaneously broad.
