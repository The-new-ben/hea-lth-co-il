# Hea-lth National Premium Medical Portal Blueprint

Date: 2026-07-10  
Status: concept and planning only  
Implementation gate: explicit owner approval after visual design

## Product thesis

Hea-lth is a broad Hebrew medical knowledge platform whose authority base deliberately feeds premium commercial decisions. It combines:

- a reviewed medical encyclopedia and glossary;
- treatment, procedure, test, drug, device, and innovation knowledge;
- verified doctors, clinics, hospitals, suppliers, and technology providers;
- premium treatment, price, comparison, and coordination pages;
- B2C and B2B medical-device commerce;
- patient and professional accounts;
- booking, lead routing, checkout, financing, subscriptions, and analytics;
- evidence-grounded AI, semantic search, and carefully governed 3D/AR experiences.

The broad destination is fixed. Delivery is phased so quality, provider supply, privacy, and SEO authority compound instead of collapsing under thousands of thin pages.

## The pyramid

### Level 1: knowledge universe

- Medical glossary A-Z.
- Body systems and anatomy.
- Symptoms and signs.
- Conditions and diseases.
- Tests, diagnostics, biomarkers, and imaging.
- Drugs, active ingredients, devices, supplements, and safety notices.
- Treatments, procedures, surgery, rehabilitation, and prevention.
- AI, robotics, digital health, clinical trials, and new medical technologies.
- Evidence explainers, medical news, expert interviews, and original research/data.

### Level 2: authority and decision hubs

- Aesthetic medicine.
- Plastic and reconstructive surgery.
- Hair, skin, laser, and transplant medicine.
- Private medicine, diagnostics, second opinions, and concierge.
- Preventive medicine, longevity, executive screening, and wellness.
- Premium dental medicine.
- Fertility and reproductive medicine.
- Orthopedics, sports, rehabilitation, and pain.
- Vision, hearing, and precision devices.
- Medical travel and international care, only with legal and continuity-of-care safeguards.

### Level 3: money pages

- Treatment, method, and procedure comparisons.
- Price guides and transparent cost methodology.
- Best-fit provider/clinic search, without undisclosed pay-to-rank.
- Bookings, introductions, second-opinion and concierge packages.
- Device/product category, comparison, quote, lease, and checkout pages.
- Premium diagnostic programs and memberships.
- Professional profile, subscription, advertising, and growth packages.

### Level 4: transactions and recurring revenue

- Qualified lead or first-booking fees.
- Provider subscriptions and enhanced profiles.
- Clearly labeled sponsored placement and campaigns.
- Concierge/coordination fees.
- B2C commerce margin or affiliate revenue.
- B2B equipment quote, marketplace, finance, lease, and supplier fees.
- Events, education, research reports, and professional services.

## Medical knowledge graph

A glossary is not a disconnected A-Z list. Every concept is an entity in a governed graph.

Core entities:

- body system and anatomical structure;
- symptom and sign;
- condition and disease;
- treatment, therapy, and procedure;
- diagnostic test and biomarker;
- drug, active ingredient, supplement, and contraindication concept;
- medical device, equipment class, brand, model, and supplier;
- medical technology and clinical trial;
- specialty and subspecialty;
- provider, clinic, hospital, and location;
- payer, insurance route, and reimbursement path;
- author, medical reviewer, guideline, study, source, and correction;
- commercial offer, price observation, appointment, product, and subscription.

Key relationships:

```text
Symptom -> may relate to -> Condition
Condition -> assessed with -> Test
Condition -> managed with -> Treatment / Drug / Device
Treatment -> alternative to -> Treatment
Procedure -> performed by -> Specialty / Provider
Provider -> practices at -> Clinic / Location
Treatment -> has decision page -> Price / Risk / Recovery / Comparison
Device -> used for -> Test / Treatment / Rehabilitation
Guideline / Study -> supports -> Claim
Knowledge entity -> routes to -> Decision hub -> Money page
```

This graph powers internal links, breadcrumbs, related-content modules, faceted search, AI citations, schema, comparison tables, and editorial gap detection.

## Search and SEO flow

Not every encyclopedia page should force a commercial CTA. The correct path is contextual:

```text
Glossary or educational page
-> reviewed decision guide
-> treatment / price / provider / device page
-> comparison or booking
-> consented lead / checkout / concierge
```

SEO layers:

1. Entity coverage: medically coherent definitions and relationships.
2. Topical authority: deep reviewed clusters around body systems and decisions.
3. SERP fit: page type, evidence, media, and CTA matched to localized intent.
4. Commercial architecture: premium pages at the decision end of the journey.
5. Original assets: verified provider inventory, price methodology, tools, expert video, 3D, and first-party market data.
6. Distribution: digital PR, expert authors, professional societies, supplier/manufacturer collaboration, newsletters, and video.

## Advanced product capabilities

### Safe early capabilities

- Semantic Hebrew search with lay and medical synonyms.
- Evidence-grounded answer summaries with inline citations and uncertainty labels.
- Unified compare tray for providers, treatments, prices, and devices.
- Interactive decision tables and price calculators.
- Real-time or request-based appointment availability.
- Saved providers, guides, products, comparisons, bookings, and orders.
- Verified-provider data and transparent ranking/filter methodology.
- 3D anatomy, procedure education, and device models using optimized glTF assets.
- AR product placement/inspection where it genuinely helps device buyers.
- Voice search, accessibility preferences, and high-quality RTL UX.

### Controlled capabilities

- AI concierge for site navigation, provider discovery, and administrative preparation.
- AI summary of the user's non-clinical needs for a human coordinator.
- Professional AI assistant for profile quality, lead response drafts, and content/source checks.
- Personalized prevention or checkup content only when reviewed and based on declared non-sensitive preferences.
- Virtual consultations and secure document exchange only in a compliant protected workflow.
- Patient-specific 3D aesthetic simulation through a licensed specialist vendor with clear non-guarantee language.

### High-risk or regulated capabilities

- Symptom triage, probable-condition outputs, treatment selection, image analysis, medication advice, or patient-specific clinical recommendations.
- These require a regulated/validated vendor, medical governance, legal/privacy approval, explicit scope, audit logs, human escalation, and emergency handling. A general-purpose LLM cannot provide them directly.

## WordPress product architecture

Recommended approach: WordPress-native, not a cloud-locked site builder and not a fully headless rewrite.

- Custom block theme and design system in `theme.json`.
- Server-rendered, indexable templates for content and directories.
- WordPress Interactivity API for selective app-like components such as compare trays, filters, calculators, saved items, and instant search.
- Custom plugins for medical entities, provider marketplace, editorial evidence, lead routing, accounts, and AI governance.
- WooCommerce with HPOS for eligible devices, subscriptions, orders, quotes, and checkout.
- Separate B2C and verified-professional B2B catalog permissions.
- Faceted/semantic search service when the catalog and encyclopedia outgrow native search.
- glTF and `<model-viewer>` for performant 3D; WebXR/AR only as progressive enhancement.
- REST APIs and later FHIR-compatible integration boundaries where legitimate providers expose them.
- Object storage/CDN for large media and 3D assets.
- Privacy-safe analytics and an auditable consent ledger.
- Local payment/invoice integrations; exact providers are selected after commercial/legal review.

## Account model

### Visitor

Browse, search, compare, view providers/products, request contact with consent, and checkout as guest where allowed.

### Member/patient account

Saved guides/providers/products, comparison history, inquiries, bookings, orders, invoices, subscriptions, notifications, consents, data export/delete request, and security settings. Do not turn this into an ungoverned medical-record repository.

### Professional account

Identity/license verification, profile, specialties, locations, services, languages, payer routes, photos/video/3D, team access, availability, lead inbox, response status, bookings, product/service listings, subscription/billing, campaign disclosure, analytics, reviews, corrections, and content collaboration.

### Supplier account

Company verification, regulatory/product documentation, catalog/feed, inventory or quote availability, shipping/service regions, warranty/service, orders/RFPs, finance/lease options, leads, billing, and analytics.

### Reviewer/editor account

Assigned content, source and claim review, revision requests, approval, expiry/re-review queue, conflicts disclosure, and correction history.

### Administrator

Taxonomy/entity graph, SEO map, editorial operations, provider/supplier verification, lead routing, marketplace, orders, subscriptions, advertising, privacy, analytics, AI governance, redirects, and audit logs.

## Business model

### Consumer side

- Free trustworthy research and comparison.
- Free provider/device discovery.
- Paid concierge and administrative coordination.
- Eligible product/device checkout.
- Premium diagnostic, executive-health, or second-opinion packages delivered by qualified partners.
- Financing/leasing referral where lawful and transparent.

### Professional side

- Free verified base profile.
- Pro profile subscription: richer media, services, analytics, and availability.
- Growth subscription: enhanced lead tools, campaigns, content collaboration, call tracking, and integrations.
- First-booking or qualified-lead fee where contractually and legally approved.
- Sponsored visibility clearly labeled and independent from editorial/medical conclusions.

### Supplier side

- Product listing and catalog subscription.
- Transaction or quote/RFP fee.
- Featured supplier/launch package.
- B2B lead, leasing, and service-contract opportunities.
- Sponsored educational content with strict disclosure and independent review.

## Success moat

The moat is not 5,000-word pages by themselves. It is the combined system:

- the best structured Hebrew medical knowledge graph;
- named expert review and claim-level sources;
- verified Israeli provider and supplier inventory;
- transparent price and ranking methodology;
- original decision tools, market data, video, and 3D assets;
- real booking, commerce, and professional operations;
- longitudinal learning from privacy-safe search, comparison, routing, booking, and purchase outcomes.

## Delivery program

### Program 1: research and design

Competitor capability matrix, full URL/keyword map, entity taxonomy, business requirements, screen inventory, information architecture, user journeys, low-fidelity wireframes, high-fidelity design system, desktop/mobile screens, and clickable prototype.

### Program 2: platform foundation

WordPress design system, content model, evidence workflow, semantic search foundation, provider MVP, consent/lead system, analytics, and migration protection.

### Program 3: premium marketplace

Bookings, provider accounts, subscriptions, verified reviews, price/comparison tools, concierge packages, and professional portal.

### Program 4: commerce

B2C eligible products, B2B equipment catalog/RFP, supplier portal, quotes, leasing/financing, orders, service/warranty, and regulatory product records.

### Program 5: intelligence and immersion

Evidence-grounded AI search, administrative concierge, professional assistant, 3D anatomy/devices, licensed simulation integrations, and carefully governed health-data interoperability.

## Approval question

Confirm whether this is the intended north star: a broad Hebrew medical encyclopedia and knowledge graph feeding a premium provider, treatment, device, booking, commerce, and concierge marketplace, delivered in governed stages without narrowing the final ambition.

