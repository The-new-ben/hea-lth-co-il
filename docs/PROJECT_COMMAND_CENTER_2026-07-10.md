# Hea-lth Enterprise Project Command Center

Date: 2026-07-10
Status: active foundation program
Current phase: Wave 0 complete; product definition and operating-system build have started
North star: a broad, medically governed Hebrew knowledge platform that feeds a premium provider, treatment, concierge, booking, product, equipment, and professional marketplace.

## Owner headline

We are on the way, but we are not close to the finished portal. The project has moved beyond an idea: it now has a source-controlled research pack, preservation-first SEO rules, an authenticated GSC baseline, a seed keyword/URL map, a product blueprint, a screen inventory, a GitHub repository, production secrets, and a validated release pipeline. However, the full keyword strategy, localized SERP evidence, approved information architecture, Figma system, medical content operation, lead-routing/CRM system, provider marketplace, commerce model, and advanced product are not built.

The correct description is:

- **Research control plane:** real and useful.
- **Technical delivery control plane:** mostly built, not yet live end-to-end.
- **SEO strategy:** foundation only, not a final keyword plan.
- **Design:** requirements and template inventory exist; approved Figma design does not.
- **Monetization:** hypotheses exist; operating economics and routing do not.
- **Content scale:** governed concept exists; production and clinical review capacity do not.
- **Category-leading portal:** planned, not built.

An overall percentage would create false precision. The critical path is still at **Foundation / Evidence-gated**. The live category-leading product is in the first tenth of the journey, while several foundation workstreams are approximately one quarter complete.

## Evidence completed

### Research and strategy

- Wave 0 is complete as a research control plane, explicitly not as the 36-workstream commission.
- The pack contains 48 source-ledger records, 38 competitor seed records, 10 capability-archetype rows, 207 Hebrew/English seed queries, 150 provisional canonical URL/task rows, and 47 preliminary risks.
- A product thesis and enterprise screen inventory cover public, member, professional, supplier, reviewer, and administrator experiences.
- A competitor capability matrix covers major global and Israeli archetypes.

### Search baseline

- Authenticated GSC snapshot for 2026-04-09 through 2026-07-08: **425 clicks, 58K impressions, 0.7% CTR, average position 28.2**.
- Current powered pages are identified and frozen from destructive migration decisions.
- The governed keyword-to-URL map has **150 rows**:
  - 13 existing URLs: `keep-improve`;
  - 137 new candidates: `hold`;
  - 150/150 still require localized Israeli desktop/mobile SERP evidence;
  - 150/150 still require authenticated Semrush metrics.

Highest current GSC page evidence:

| URL | Clicks | Impressions | Average position | Owner decision |
|---|---:|---:|---:|---|
| `/best-cosmetics/` | 104 | 15,186 | 29.9 | Preserve; audit before redesign or consolidation. |
| `/hair-loss-prevention-treatments-costs/` | 45 | 3,624 | 21.9 | Preserve and map to hair cluster. |
| `/hair-transplantation-guide/` | 42 | 5,116 | 31.9 | Preserve and improve after SERP evidence. |
| `/טיפול-בחמצן-במימון-קופת-חולים/` | 38 | 2,717 | 16.5 | Preserve; reconcile oxygen/hyperbaric overlap. |
| `/mount-sinai-health-system/` | 20 | 890 | 6.3 | Preserve while intent and strategic fit are audited. |

### WordPress and GitHub delivery

- GitHub repository is connected and the foundation pipeline is merged to `main`.
- The WordPress Application Password was created, verified as administrator, and stored in the protected GitHub `production` environment with the site URL and username.
- CI gates cover secret scanning, actionlint, Python compilation/unit tests, PHP lint, WordPress coding standards, static analysis, web dependency audit, reproducible builds, dry-run validation, and release evidence.
- PR #3 fixed the first uPress WAF failure and passed all CI gates before merge.
- Production is **not yet deployed**. The live health endpoint remains HTTP 404, proving no partial target plugin is active.
- Current upstream blockers are documented under Red alerts.

### Governance

- The `hea-lth-owner-operator` Codex skill is installed and validated.
- It defines 13 enterprise dimensions, maturity states, red alerts, competitor-evidence records, priority rules, and mandatory live-versus-planned reporting.

## Enterprise scorecard

| Dimension | State | Evidence | What prevents the next state |
|---|---|---|---|
| Business strategy and economics | Evidence-gated | Product thesis and revenue hypotheses exist. | Financial actuals, legal role, offers, pricing, contracts, unit economics, and three launch verticals are undecided. |
| Information architecture and taxonomy | Foundation | Pyramid, entity graph concept, semantic silos, and screen inventory exist. | Approved entity model, sitemap, URL dispositions, navigation, role journeys, and primary taxonomy. |
| SEO evidence and governance | Foundation | Wave 0, GSC snapshot, 207 seeds, governed 150-row map, preservation rules. | 16-month GSC API export, GA4, crawl, backlinks, Semrush, localized SERP evidence, and cannibalization decisions. |
| Experience and design system | Foundation | Requirements, homepage modules, template inventory, and an HTML exploration exist. | Approved Figma file, tokens, components, responsive screens, prototypes, accessibility annotations, and usability evidence. |
| Technical platform and delivery | In progress | GitHub/CI, deterministic artifacts, protected secrets, rollback/cleanup code, authenticated WordPress user. | End-to-end production deploy, rollback drill, monitoring, staging proof, current-stack audit, and uPress-compatible durable runner/pull path. |
| Medical content operation | Foundation | Risk tiers, master brief, claim-ledger concept, correction/review rules. | Reviewer panel, specialty evidence packs, author credentials, publication workflow, and page-level medical review. |
| Monetization and lead operations | Not started / red alert | Revenue routes and privacy-safe first-form principle are documented. | Existing lead-routing audit, CRM, consent ledger, partner capacity, SLAs, contracts, attribution, outcomes, and revenue reconciliation. |
| Provider marketplace | Not started | Provider data schema and portal requirements are described. | Provider supply, verification process, contracts, ranking policy, availability, reviews policy, and operations. |
| Commerce and premium products | Evidence-gated | WooCommerce exists and B2C/B2B concepts are defined. | Seller role, eligible categories, supplier data, regulation, margin, stock, fulfillment, returns, payment, and service model. |
| Product intelligence and advanced interfaces | Concept only | AI, semantic search, comparison, 3D/AR, and safety tiers are specified. | Foundational data, evaluation, model/vendor selection, clinical/legal governance, assets, and production UX. |
| Data, analytics, and experimentation | Foundation | Three-month GSC top-row snapshot is stored. | Full GSC, GA4/GTM, CRM events, call tracking, data dictionary, KPI tree, dashboards, and experiment ownership. |
| Trust, legal, privacy, and clinical safety | Foundation | Checklists, risk register, emergency boundaries, and data-minimization rules exist. | Qualified Israeli counsel, privacy/security audit, medical reviewers, provider/product verification, and incident ownership. |
| Operating model and roadmap | Foundation | Wave plan, continuation ledger, risk register, and owner skill exist. | Named owners, capacity, budget, vendor choices, decision cadence, and live command-center metrics. |

## Red alerts

### P0 - Lead routing and revenue leakage

**Evidence:** owner reports that leads are arriving but are not routed correctly.
**Impact:** revenue loss, poor user experience, provider dissatisfaction, privacy/consent risk, and unreliable SEO economics.
**Containment:** do not scale traffic or add broad lead forms until every current form, recipient, handoff, and outcome is mapped.
**Next proof:** one lead inventory with source URL, fields, consent version, destination, owner, response SLA, status, outcome, and revenue class.

### P0 - Production pipeline not end-to-end

**Evidence:** GitHub main run `29112118620` passed CI but uPress terminated the GitHub-hosted TLS connection before the first WordPress response. Local authenticated bootstrap then reached WordPress, but uPress could not download Code Snippets from WordPress.org within its server timeout.
**Impact:** code remains source-controlled and validated, but deployment is not yet zero-friction.
**Current health:** target plugin health route is HTTP 404; no partial release is active.
**Containment:** no blind retries, no concurrent deploys, no permanent unverified endpoint.
**Next resolution:** upload the checksum-verified official Code Snippets 3.9.6 archive once through the authenticated WordPress admin, then rerun the temporary-bridge deployment; in parallel select uPress native Git/pull or a stable approved runner path.

### P0 - Organic equity migration risk

**Evidence:** powered legacy URLs already produce measurable clicks/impressions; the complete 16-month dataset, crawl, backlinks, and redirect map do not exist.
**Impact:** a visually attractive rebuild could destroy current search value.
**Containment:** URL and redirect freeze.
**Next proof:** complete legacy inventory joined to GSC, GA4, backlinks, canonical state, internal links, and final disposition.

### P1 - Keyword strategy is being mistaken for complete

**Evidence:** 137/150 proposed new URLs are on hold; every row lacks required Semrush and localized SERP evidence.
**Impact:** cannibalization, unsupported page growth, and content spend without a ranking thesis.
**Containment:** no bulk article commissioning from the seed universe.
**Next proof:** evidence-complete keyword/URL decisions for the first commercial portfolio.

### P1 - Design can drift ahead of architecture

**Evidence:** no approved Figma source, role model, entity model, or migration-aware sitemap.
**Impact:** expensive high-fidelity screens that cannot map cleanly to WordPress, SEO, accounts, or operations.
**Containment:** design the first integrated journeys and system after IA v1, not dozens of isolated mockups.
**Next proof:** approved sitemap, mega-menu model, design tokens, and desktop/mobile prototypes for the homepage plus one complete money journey.

### P1 - Medical scale without reviewer capacity

**Evidence:** no specialty evidence packs or named reviewer panel.
**Impact:** YMYL quality, legal, brand, and AI-citation risk.
**Containment:** no industrial 5,000-word publishing program yet.
**Next proof:** reviewer roster, claim ledger, source hierarchy, correction SLA, and five pilot pages passed end-to-end.

### P1 - Slack workspace connection is incomplete

**Evidence:** Codex currently sees only workspace `justice`; it does not see a `Hea-lth` workspace.
**Impact:** alerts and operational channels cannot be wired to the intended workspace.
**Next resolution:** reconnect the Slack plugin and explicitly authorize the Hea-lth workspace.

### P1 - Interactive WordPress password should be rotated

**Evidence:** the interactive password was visible during login setup. The separate Application Password is safely stored in GitHub.
**Containment:** do not rotate until the deployment path is stable, to avoid losing the current administrative session mid-bootstrap.
**Next resolution:** rotate the normal WordPress login password after pipeline and recovery proof; preserve the scoped Application Password unless a credential-rotation event is required.

## Competitor gap reality

| Benchmark | Their proven strength | Hea-lth current state | Gap-closing proof required |
|---|---|---|---|
| Mayo Clinic / Cleveland Clinic | Deep medical taxonomy, reviewed libraries, institutional trust, patient-care connection. | Seed content and governance concepts only. | Reviewer operation, entity graph, evidence-rich clusters, re-review cadence, and user task coverage. |
| Zocdoc / Doctolib | Provider discovery, availability, booking, accounts, communications, and practice operations. | Provider/portal requirements only. | Verified supply, transparent ranking, response/availability data, booking/account journeys, and measurable completion rates. |
| RealSelf | Aesthetic treatment depth, doctor Q&A, verified reviews, before/after media, consultation path. | Aesthetic organic seed but no equivalent marketplace trust system. | Media consent/provenance, provider verification, verified-event reviews, Q&A, comparison, and booking/lead economics. |
| MedReviews | Israeli provider inventory, specialties, languages, payer/location filters, and local SEO footprint. | General provider-index seed with no verified national inventory. | Credible provider coverage, verification timestamps, unique profiles, filters, availability, and supply operations. |
| Henry Schein / DOTmed | Professional catalog, equipment lifecycle, quotes/RFP, financing/leasing, supplier workflows. | WooCommerce and marketplace concept only. | Supplier/regulatory data, B2B permissions, catalog, quote/RFP, finance/service flows, and commercial contracts. |
| Crisalix | Specialist 3D/VR/AR aesthetic simulation. | 3D roadmap only. | Licensed integration or owned educational 3D with consent, performance, non-guarantee language, and measured consultation value. |
| Prenuvo / Function Health | Premium packaging, membership, longitudinal results and preventive-health journey. | Premium wellness concept only. | Evidence boundaries, partner supply, pricing, longitudinal account design, and compliant data operations. |
| Infermedica / Ada | Clinically governed structured assessment and regulated safety model. | Explicitly excluded from general-LLM implementation. | Validated specialist vendor, exact clinical scope, governance, evaluation, escalation, and legal approval if ever pursued. |

**Current verdict:** Hea-lth does not yet outperform these benchmarks. The opportunity is credible because no sampled competitor combines all of the desired Hebrew/Israel layers, but the whitespace is operational, not visual. It closes only through verified supply, medical review, data, transactions, and superior journeys.

## Next three portfolio moves

### 1. Close the control plane and recover zero-friction delivery

Acceptance evidence:

- official Code Snippets package installed and active from the verified archive;
- exact GitHub release deployed and health identity matches commit/version;
- temporary deployment route returns 404 after cleanup;
- rollback drill is proven on staging or a safe canary;
- durable uPress Git/pull or stable runner architecture is selected and documented;
- normal WordPress login password is rotated after recovery proof.

### 2. Stop lead leakage and create the revenue truth system

Acceptance evidence:

- inventory of every form, phone, WhatsApp, email, WooCommerce, and manual lead source;
- privacy-safe intake standard and versioned consent;
- CRM stages, routing rules, deduplication, ownership, provider capacity, rejection reasons, and escalation;
- response SLA dashboard and test leads across every high-value journey;
- revenue/outcome attribution to landing page, category, provider, and channel;
- first monetization pilot with explicit economics and contract.

### 3. Produce the evidence-locked IA and first Figma decision package

Acceptance evidence:

- 16-month GSC export, GA4 landing/conversion export, full crawl, backlink data, and authenticated Semrush project;
- localized Israel SERP packs for the first 20 high-value commercial intents;
- approved v1 keyword-to-URL ownership and migration dispositions for those intents;
- approved entity model, sitemap, navigation/mega-menu map, and role journeys;
- Figma tokens/components plus desktop/mobile homepage and one complete path: research -> treatment/price -> provider comparison -> privacy-safe request;
- usability and accessibility findings before WordPress implementation.

## Work that must not start yet

- mass publication of 3,000-5,000-word medical pages;
- deletion, consolidation, slug change, or broad redirect program;
- production implementation of all portal screens before IA/Figma approval;
- provider `verified` claims without source/date/reverification;
- patient-specific medical AI, symptom triage, diagnosis, medication advice, or medical-image analysis;
- 3D simulation presented as predicted patient outcome;
- large-scale paid lead acquisition before routing, consent, SLA, and attribution are proven.

## Access/actions required from the owner

1. Sign in to the uPress hosting panel page already opened in Chrome. This allows verification of Security -> REST API, firewall controls, staging, backups, and native Git/pull.
2. At action time, confirm the upload/install of the verified official Code Snippets 3.9.6 archive to `hea-lth.co.il`; this is the one-time bootstrap dependency.
3. Reconnect the Codex Slack plugin and explicitly choose the Hea-lth workspace; Codex currently receives only `justice`.
4. Provide or connect 16-month GSC API, GA4/GTM, Semrush, crawl/backlinks, and the existing lead/CRM exports using redacted or aggregated data.

## Sources of truth

Local project evidence:

- `research/hea-lth_research_pack_wave0_2026-07-10/00_README.md`
- `research/hea-lth_research_pack_wave0_2026-07-10/34_FOUNDATION_WAVE_FINDINGS.md`
- `research/hea-lth_research_pack_wave0_2026-07-10/35_QA_SELF_AUDIT.md`
- `research/hea-lth_research_pack_wave0_2026-07-10/30_OPEN_QUESTIONS_AND_DATA_REQUESTS.md`
- `research/gsc/2026-07-10/README.md`
- `research/gsc/2026-07-10/gsc_pages_2026-04-09_2026-07-08_top20.csv`
- `research/codex-wave0-intake-2026-07-10/07_KEYWORD_TO_URL_MAP_GOVERNED.csv`
- `docs/PORTAL_PRODUCT_BLUEPRINT_2026-07-10.md`
- `docs/COMPETITOR_CAPABILITY_GAP_MAP_2026-07-10.md`
- `docs/WORDPRESS_SCREEN_AND_BACKOFFICE_INVENTORY_2026-07-10.md`

External primary/supporting sources:

- uPress REST API control: <https://support.upress.io/security/rest-api/>
- uPress Git/file-manager support: <https://support.upress.io/advanced/manage-git-via-file-manager/>
- WordPress plugin REST endpoint: <https://developer.wordpress.org/rest-api/reference/plugins/>
- Official Code Snippets plugin: <https://wordpress.org/plugins/code-snippets/>
- Google people-first content: <https://developers.google.com/search/docs/fundamentals/creating-helpful-content>
- Google site moves: <https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes>
