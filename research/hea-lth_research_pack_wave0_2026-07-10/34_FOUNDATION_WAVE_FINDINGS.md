# Foundation Wave Findings

**Access date:** 10 July 2026  
**Scope:** Publicly visible starting-state evidence, control gaps, and immediate research actions.  
**Important limitation:** This was not a full crawl, authenticated analytics review, Search Console audit, backlink audit, code audit, or legal review.

## 1. Current public-site baseline

### Public positioning observed

The reviewed homepage presents Hea-lth as a private-health coordination and decision-support surface. It points users toward:

- private specialist appointments;
- medical second opinions;
- private MRI/CT coordination;
- health-insurance/SHABAN reimbursement questions;
- private home visits;
- premium/private health services;
- medical aesthetics;
- plastic surgery;
- hair transplantation;
- laser hair removal;
- skin treatment;
- provider/clinic comparison and price guides.

It includes an emergency warning and says the site is not an emergency center and does not diagnose or treat.

**Source:** `SRC-002`.

### Public URLs observed or discovered

| URL | Observed role | Foundation state |
|---|---|---|
| `https://hea-lth.co.il/` | Homepage / private-care coordination | Preserve; full performance and intent audit required |
| `https://hea-lth.co.il/doctor-clinic-index/` | General provider/clinic decision guide and developing index | Preserve; actual inventory and verification model DATA REQUIRED |
| `https://hea-lth.co.il/botox-price/` | Botox education and 2026 price guide | Preserve; medical evidence and price methodology audit required |
| `https://hea-lth.co.il/mri-ct-appointment/` | Private MRI/CT coordination guidance | Preserve; supply, referral, payer and intake audit required |
| `https://hea-lth.co.il/laser-hair-removal-private/` | Laser hair-removal guide and price framing | Preserve; evidence, price method, operator/device/regulatory audit required |
| `https://hea-lth.co.il/premium-health-services/` | Private-health services hub | Preserve; hub/child intent overlap audit required |
| `https://hea-lth.co.il/breast-augmentation-cost/` | Breast-augmentation cost guide | Preserve; clinical/reviewer/price/ad rules audit required |
| `https://hea-lth.co.il/nose-surgery-cost/` | Rhinoplasty cost guide | Preserve; clinical/reviewer/price/ad rules audit required |
| `https://hea-lth.co.il/private-doctor-appointment/` | Link-discovered private-doctor page | Fetch/crawl and performance DATA REQUIRED |
| `https://hea-lth.co.il/medical-second-opinion/` | Link-discovered second-opinion page | Fetch/crawl and performance DATA REQUIRED |
| `https://hea-lth.co.il/health-insurance-refund/` | Link-discovered reimbursement page | Fetch/crawl and payer-evidence DATA REQUIRED |
| `https://hea-lth.co.il/hair-transplant-consultation/` | Link-discovered hair-transplant page | Fetch/crawl and performance DATA REQUIRED |
| `https://hea-lth.co.il/aesthetic-medicine-treatments/` | Link-discovered aesthetic-medicine page | Fetch/crawl and performance DATA REQUIRED |
| `https://hea-lth.co.il/plastic-surgery-consultation/` | Link-discovered plastic-surgery page | Fetch/crawl and performance DATA REQUIRED |

This is a **partial visible inventory**, not the site URL inventory.

## 2. Strengths already present

### Sensible user framing

The reviewed pages frequently encourage users to ask what is included, who performs the service, what the risks are, what follow-up exists, and whether price alone is a sufficient decision criterion.

### Emergency and non-diagnostic boundaries

The homepage, provider index, and imaging page contain clear statements that emergency symptoms should be directed to emergency services and that the site does not diagnose or treat.

### Commercially coherent seed cluster

The site already connects private specialist care, imaging, second opinions, aesthetics, plastic surgery, hair, skin, price, and provider comparison. This is a more coherent seed than an unrelated general-health blog.

### RTL/accessibility surface

The parsed pages expose Hebrew RTL content and an accessibility toolbar. Formal WCAG compliance is still DATA REQUIRED; the presence of a toolbar is not evidence of conformance.

## 3. Material gaps to verify

The following are **observations from parsed public pages**, not claims about unseen CMS or back-office systems.

| Control | What was not visible in the reviewed parsed content | Why it matters |
|---|---|---|
| Named authors/reviewers | Clear byline and medical-review entity with qualifications and review date | YMYL trust, accountability, re-review |
| Source citations | Page-level citations supporting medical and price claims | Evidence quality and correction |
| Claim ledger | Structured record of exact claim, source, limitation and expiry | Consistency across pages and AI retrieval |
| Price methodology | Observation source set, date, sample, inclusions, geography, update method | Avoid stale or misleading price guidance |
| Provider inventory | Verified profiles, license/source/date, services, locations, availability | Marketplace value and trust |
| Ranking method | Explanation of organic/sponsored order and quality-neutral filters | Conflict and advertising transparency |
| Review policy | Eligibility, moderation, fraud, disputes, provider response | Fake-review and defamation risk |
| Conversion status | Consent, recipient, routing, SLA, lead outcome, deletion/retention | Privacy and operating economics |
| Editorial governance | correction, retraction, update, conflicts, advertising separation | Category authority |
| Structured data method | Visible-to-markup parity and entity ID strategy | Search and data integrity |

## 4. Price-content control issue

The Botox page says public Israeli advertisements may show several hundred shekels for one area and more than one thousand for broader treatment, while emphasizing variation and that ranges are not commitments. The laser page similarly gives broad market framing.

The wording is cautious, but a premium portal should add a transparent method:

- observation period;
- public sources sampled;
- geography;
- number of observations;
- whether VAT, consultation, review, correction, materials, anesthesia, facility, tests, or aftercare are included;
- median/range/outlier treatment;
- data freshness;
- provider-submitted versus independently observed status;
- conflicts/sponsorship;
- correction channel.

Until that exists, price statements should be labeled as broad public-advertising observations, not a proprietary market index.

## 5. Provider-index control issue

The provider-index page is currently primarily an educational comparison guide and describes the index as developing. That is acceptable as a seed, but a national marketplace requires the following minimum provider record:

- canonical provider ID;
- legal/professional name;
- profession, license type, license number/source, status and verified date;
- specialty and sub-specialty source;
- organization affiliations, separately verified;
- locations and service areas;
- languages and accessibility;
- payer routes;
- service inventory;
- price and inclusions, with source/date;
- request-based or real availability and freshness;
- media rights and before/after consent;
- disclosures, sponsorship and conflicts;
- review eligibility;
- sanctions/status-change handling;
- correction history;
- next re-verification date.

No “verified” badge should be a manual marketing label without those fields and an audit trail.

## 6. Search and AI visibility implications

Official Google guidance reviewed in Wave 0 says:

- normal foundational SEO practices apply to AI Overviews and AI Mode;
- no special schema or AI text file is required;
- pages need to be indexed and eligible for snippets;
- crawlability, internal links, page experience, textual availability, useful images/video, and structured-data/visible-text consistency remain relevant;
- AI-feature traffic is reported within overall Web performance in Search Console.

**Implication:** The priority is not an `llms.txt` launch or speculative markup. It is a crawlable, evidence-rich, entity-consistent, well-linked, useful platform with original data and clear authorship. `llms.txt` may be tested as an emerging convention, but no ranking or citation benefit should be assumed.

## 7. Current WordPress/WooCommerce baseline

Official sources reviewed show:

- WordPress 7.0.1 was released on 9 July 2026, following WordPress 7.0 on 20 May 2026;
- WordPress documents the Interactivity API, Block Bindings, and the Abilities API;
- WooCommerce HPOS uses dedicated order tables and is the recommended storage option in its documentation;
- the WooCommerce Store API exposes customer-facing product, cart, checkout and order functionality without exposing other customers’ sensitive data.

**Implication:** A portable WordPress/WooCommerce control plane remains technically credible. This is not approval to upgrade or install features on the live site. Actual version, compatibility, custom code, plugins, data volumes, security, hosting and rollback must be audited first.

## 8. Immediate control actions

### Within the research program

1. Freeze destructive SEO changes.
2. Create immutable exports and a full crawl.
3. Establish source, claim, author/reviewer, provider, price, consent, redirect, and decision ledgers.
4. Inventory all public forms and the data they request.
5. Remove or redesign any first-contact form that requests unnecessary medical detail.
6. Label all current provider, price, availability, and “2026” claims with source/method/review status internally.
7. Add an owner and expiry date to every high-risk page.
8. Define sponsored placement and editorial ranking before selling either.
9. Measure current lead response and outcome before scaling traffic.
10. Select Wave 1 data sources and Israeli qualified advisers.

### Technical no-change rule

No core, theme, plugin, database, URL, schema, redirect, or hosting change is approved by this foundation pack. It only defines the evidence required to make those changes safely.

## 9. Foundation strategic inference

Hea-lth’s strongest near-term opportunity is not to replace the current site with a visually larger version. It is to turn the current seed into a verifiable operating system:

`evidence → entity → reviewed page → verified supply → privacy-safe request → response/booking/quote/order → outcome data → correction/update`

That loop can support SEO, AI retrieval, provider value, consumer trust, and future marketplace economics. Without the loop, scale mainly increases exposure.
