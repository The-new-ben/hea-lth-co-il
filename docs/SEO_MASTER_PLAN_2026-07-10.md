# Hea-lth.co.il SEO, Architecture, Monetization, and Design Plan

Date: 2026-07-10  
Status: planning baseline; no rebuild is authorized by this document  
Market: Hebrew searchers in Israel  
Positioning: premium health marketplace combining medical aesthetics, plastic surgery, hair/skin, private medicine, wellness diagnostics, and verified professionals

## Executive decision

Hea-lth should not be rebuilt as a general health magazine and should not become a collection of disconnected lead pages. It should become a trust-led premium-health marketplace with two connected journeys:

1. Consumers research a high-value decision, compare verified options, and request a privacy-safe introduction or paid coordination.
2. Licensed professionals build a verified profile, receive appropriately consented inquiries, and buy transparent marketplace services.

The target is the competitive head market, including `רפואה אסתטית`, `בוטוקס`, `ניתוחים פלסטיים`, `השתלת שיער`, `הסרת שיער בלייזר`, and `רופא פרטי`, together with distinct price and provider intents. Top rankings cannot be guaranteed. The operating advantage must come from better intent matching, verified entity data, original tools/data, strong medical review, technical reliability, and a materially better user decision journey.

## Current baseline and constraints

- The live Yoast sitemap currently exposes about 108 URLs: 76 posts, 24 pages, 4 products, 1 category, 2 product categories, and 1 author URL.
- Existing commercial pages include aesthetics, plastic surgery, hair transplant, laser hair removal, provider index, private medicine, price pages, and concierge services.
- Existing WordPress pages are logically flat. Changing their physical paths simply to create folders would risk earned signals.
- WooCommerce shop/cart/checkout/account and thin product URLs currently dilute topical focus unless commerce becomes a real product strategy.
- `robots.txt` returns a proper 404. Google permits a missing robots file, but an explicit file that references the sitemap may still improve operational clarity.
- GitHub remote and authentication are already active for `The-new-ben/hea-lth-co-il` on branch `codex/health-api-content`.
- Semrush data must be connected before volumes, CPC, keyword difficulty, competitor gaps, or traffic estimates are treated as facts. No such metrics should be invented.
- Lovable is research/design-only. It is not an implementation, hosting, CMS, or runtime dependency.

## Source-backed rules

### People-first and YMYL

- Google asks whether content is original, comprehensive, better than competing results, clearly sourced, and written or reviewed by a demonstrable expert. It also asks publishers to make the `Who`, `How`, and `Why` of content clear.
- Google explicitly states it has no preferred word count. Therefore 5,000+ words is a flagship editorial format, not a ranking rule. Every section must solve part of the search task.
- Scaled AI pages with little original value can fall under Google's scaled-content abuse policy.
- Medical facts require claim-level evidence and a named qualified reviewer before publication.

### Architecture and internal linking

- Use a clear semantic hierarchy, descriptive page names, contextual anchors, breadcrumbs, hub pages, and related decision paths.
- Every indexable page must receive at least one contextual internal link from another indexable page.
- Keep one canonical URL per primary intent. Variants and synonyms belong on the same page unless the localized SERP demonstrates a different search task.
- Physical URL directories are optional at this site size. Preserve valuable existing paths and express hierarchy through hubs, breadcrumbs, navigation, templates, and internal links.

### Migration and URL preservation

- Export every live URL from sitemap, CMS, Search Console, analytics, backlinks, and server logs.
- Give every URL one disposition: keep, improve, merge, redirect, canonical, noindex, or gone.
- Do not redirect many old URLs to the homepage. Redirect only to the closest equivalent destination.
- Avoid redirect chains. Update internal links, canonicals, sitemap entries, and structured data to the final URL.
- Keep permanent redirects for at least one year and preferably indefinitely for useful inbound links.
- Google recommends changing one major dimension at a time. Do not combine a domain move, CMS move, design rebuild, and URL rewrite in one uncontrolled launch.

### Medical advertising, provider facts, and privacy

- Verify provider license and specialty against the Israeli Ministry of Health before listing or routing.
- Avoid misleading medical advertising, therapeutic promises, unapproved cosmetic-health claims, guarantees, and implied suitability.
- First-contact forms collect administrative routing data, not diagnoses, medication lists, test results, or medical documents.
- Israeli privacy/database obligations still apply even when a database no longer requires registration. Legal review is required before scaling the sale or transfer of health-related leads.

## SEO project method

### Gate 0: access and measurement

Required connections:

- Google Search Console: 16-month query/page exports, index coverage, links, sitemaps, removals, manual actions.
- GA4/GTM or equivalent privacy-safe analytics: landing page, qualified lead, provider acceptance, paid conversion.
- Semrush: Organic Research, Keyword Strategy Builder, Keyword Gap, Backlink Analytics, Position Tracking, Cannibalization Report.
- WordPress/UPress: URL export, page status, templates, plugins, redirects, staging, server logs where available.
- CRM: source URL, UTM, service, region, consent, provider handoff, partner response, outcome, revenue class.

No keyword volume, CPC, KD, traffic value, or current rank enters the master map until its source and collection date are recorded.

### Gate 1: complete legacy inventory

Create a row for all current URLs and add:

- status code, indexability, canonical, title, H1, template, word count, schema, last update;
- Search Console clicks, impressions, average position, and queries;
- analytics sessions, leads, assisted conversions, and revenue class;
- backlinks/referring domains and internal-link count;
- content cluster and target intent;
- final disposition and redirect destination if applicable.

The current seed keyword map is not a migration map. It must be joined to the full legacy inventory before build decisions.

### Gate 2: keyword universe

For each silo, collect:

1. Site queries from Search Console.
2. Organic keywords for hea-lth and confirmed competitors in Semrush.
3. Keyword Gap terms.
4. Israeli Hebrew seed expansions, questions, price, risk, recovery, method, provider, city, insurance, and comparison modifiers.
5. Query-level SERP features and page-type evidence.
6. Commercial values: CPC, trend, difficulty, current rank, business margin, lead quality, partner supply.

Do not select only low-difficulty leftovers. Use a portfolio: head terms for authority, commercial modifiers for income, and supporting questions for topical completeness.

### Gate 3: localized SERP research

For every primary keyword capture Hebrew/Israel desktop and mobile results, date, intent, SERP features, top-10 titles/descriptions/domains/page types, PAA, related searches, repeated entities, trust signals, media, tables, conversion patterns, and weaknesses.

Initial live sampling found:

- `רפואה אסתטית`: provider/directory and clinic intent, with a local pack; MedReviews and large clinic networks are prominent.
- `השתלת שיער`: mixed guide, clinic, institutional, video, and AI-result intent; repeated subtopics are methods, process, permanence, suitability, and side effects.
- `ניתוחים פלסטיים`: category/clinic/provider intent with local and video features.
- `בוטוקס`: mixed aesthetic-treatment and medical-use intent.
- `בוטוקס מחיר`: a separate price/comparison SERP dominated by price lists, guides, offers, and clinics.
- `השתלת שיער מחיר`: a separate commercial price SERP with guides, clinics, media, and Israel/abroad comparisons.

This supports separate general and price URLs for Botox and hair transplant. Other splits remain hypotheses until researched.

### Gate 4: keyword-to-URL map and cannibalization

Use one primary intent per canonical URL. Apply this internal top-10 overlap heuristic:

- 50%+ shared organic domains: default to one page.
- 30% or less plus a distinct intent/page type: separate pages may be justified.
- 31-49%: manual decision using SERP features, conversion goal, and existing URL authority.

This is an operating heuristic, not a Google rule. Semrush Position Tracking then monitors whether multiple URLs rank for the same tracked query.

### Gate 5: evidence pack and content brief

Every brief includes:

- query/intent and canonical decision;
- top-result coverage matrix without copied prose;
- primary/official medical sources and claim ledger;
- original-value asset such as a verified directory, expert interview, calculator, price methodology, decision table, owned imagery, or original dataset;
- title/H1/outline, tables, media, internal links, schema candidates, CTA, privacy-safe fields, reviewer, and update cadence;
- explicit forbidden claims and open evidence gaps.

Competitor content supplies coverage signals and format DNA. It is not source text for imitation. Facts are re-verified through primary sources.

### Gate 6: design and conversion prototype

Lovable, product-design, and Figma may be used for research, wireframes, design system, and prototype only. Production remains in the controlled WordPress/UPress/GitHub stack.

Prototype and test:

- rich RTL homepage;
- desktop mega menus and accessible mobile navigation;
- hub, treatment, price, comparison, directory, provider, editorial, and lead-result templates;
- consumer lead journey and professional onboarding journey;
- trust/reviewer/source modules;
- Core Web Vitals budgets and accessibility.

### Gate 7: staged implementation and migration

Build only after the URL map, template inventory, design prototype, medical governance, lead contracts, privacy model, analytics events, and redirect plan are approved. Test on UPress staging, crawl it, compare old/new URL sets, and launch in controlled waves.

## Semantic information architecture

Keep strong existing URLs even when the hierarchy below is shown as a tree.

```text
Homepage: premium health marketplace
├── Aesthetic medicine
│   ├── Botox: guide / price
│   ├── Hyaluronic acid: guide / price
│   ├── Lips and facial contouring
│   └── Skin: pigmentation / acne scars / laser treatments
├── Plastic surgery
│   ├── Nose: guide / price
│   ├── Breast: augmentation / lift / reduction / price
│   └── Body and face: liposuction / tummy tuck / facelift / eyelids
├── Hair and laser
│   ├── Hair transplant: guide / price / methods / Israel vs Turkey
│   └── Laser hair removal: guide / price
├── Private medicine
│   ├── Private specialist appointment
│   ├── Second opinion
│   ├── MRI/CT coordination
│   ├── Insurance/refund support
│   └── Non-emergency home visit / concierge
├── Premium wellness and diagnostics
│   ├── Executive screening
│   ├── Preventive medicine
│   ├── Genetic and sleep testing
│   └── Longevity medicine, with strict evidence boundaries
├── Premium dental, phase 3
│   ├── Implants / All-on-4
│   ├── Veneers / aligners
│   └── Full-mouth rehabilitation
└── Professionals
    ├── Doctor and clinic index
    ├── Verified provider profiles
    ├── Evidence-supported specialty/location listings
    └── Join hea-lth / plans / verification / partner portal
```

## Navigation and homepage design

### Header

- Primary navigation: אסתטיקה רפואית, ניתוחים פלסטיים, שיער ועור, רפואה פרטית, בדיקות ו-Wellness, רופאים וקליניקות.
- Utility links: מדריכי מחירים, איך זה עובד, אודות ומתודולוגיה.
- Primary CTA: התאמה לרופא או קליניקה.
- Professional CTA: הצטרפות אנשי מקצוע.
- Mega menus show no more than the most important decision paths, not every article.

### Rich homepage modules

1. Premium value proposition and two-path hero: find care / join as professional.
2. High-value category navigator.
3. Verified professional search with specialty and region.
4. Price and decision guides.
5. “How verification and matching work” transparency block.
6. Featured reviewed guides by silo.
7. Professional marketplace value proposition.
8. Evidence, reviewer, correction, privacy, and editorial policy trust block.
9. Compact privacy-safe routing form.

## Monetization system

### Consumer revenue

- Fixed-price administrative concierge packages.
- Appointment/second-opinion/imaging coordination after scope and legal review.
- Premium comparison or document-readiness services.

### Professional revenue

- Free verified basic profile to build inventory.
- Paid enhanced profile subscription.
- Clearly labeled sponsored visibility, separated from editorial ranking.
- Fixed qualified-lead or appointment-introduction fee under written terms.
- Practice growth package: profile, content collaboration, call tracking, analytics, and response SLA.

### Lead routing

1. First form: service, region, timing, payer route when relevant, contact details, preferred channel, consent.
2. Exclude clinical detail and documents.
3. Match only verified, contracted, in-scope providers.
4. Default route to one provider; maximum three when comparison is requested.
5. Rank routing by objective fit and availability, never by undisclosed payment alone.
6. Log source, consent, recipient, response, acceptance/rejection reason, and revenue class.
7. Measure qualified-lead rate, contact rate, provider acceptance, booked consultation, paid conversion, refund/cancellation, and revenue per landing page.

## Provider and location page rules

An indexable provider profile requires verified identity, specialty/license source, real locations, services, languages, public commercial terms where appropriate, verification date, corrections path, and unique content.

An indexable specialty/location page requires:

- distinct Israeli query demand and a local/provider SERP;
- at least three useful verified providers, preferably five;
- unique inventory, local explanation, methodology, and FAQs;
- no doorway copy or city-name substitution.

Pause at 30 planned location pages and require executive review. Hard stop at 50 without evidence of demand, inventory, and unique usefulness.

## Schema policy

- `Organization` on the authoritative organization/about surface using verified real-world details.
- `Article` for reviewed editorial content with visible authors and reviewer relationships represented truthfully.
- `BreadcrumbList` matching visible breadcrumbs.
- `Person`, `Physician`, `MedicalClinic`, or service entities only for visible verified facts.
- Google ProfilePage rich-result guidance generally expects the profiled person or organization to be affiliated with the site; do not promise provider-directory eligibility.
- No fabricated ratings, no self-serving review markup, no hidden FAQ content, and no HowTo schema.

## Publication waves

### Wave 1: protect and fix

- Full legacy inventory and Search Console/Semrush scoring.
- Technical crawl, sitemap/indexation cleanup, schema validation, Core Web Vitals baseline.
- Preserve and improve existing powered commercial pages.
- Finalize organization, editorial, reviewer, privacy, directory, and advertising policies.

### Wave 2: highest-value existing silos

- Aesthetic medicine, plastic surgery, hair transplant, laser hair removal, and private medicine hubs.
- Separate price pages only where SERP evidence supports them.
- Verified provider MVP and controlled lead routing.

### Wave 3: marketplace depth

- Provider profiles, selected specialty/location pages, premium wellness diagnostics, and premium dental.
- Original comparison tools and first-party market data.

### Wave 4: authority expansion

- Scientifically reviewed supporting content, digital PR, expert contributions, research/data releases, and selective video.

## Success measures

- Zero lost high-value legacy URLs without an evidence-backed disposition.
- Cannibalization health and count of affected queries.
- Nonbrand top-3/top-10 share of voice by silo.
- Qualified organic leads and paid conversions by canonical landing page.
- Verified active provider supply, response SLA, and provider retention.
- Revenue per qualified lead and per provider, without clinical-outcome incentives.
- Percentage of YMYL pages with current reviewer, evidence ledger, visible update date, and corrections path.
- Crawl/index coverage, Core Web Vitals, and structured-data validity.

## Blocking inputs before the complete keyword list

- Connect Semrush to obtain Israel database volumes, CPC, KD, trend, competitor keywords, gaps, and snapshots.
- Export Search Console and analytics so existing URLs can be protected by evidence.
- Confirm the medical reviewer model and privacy/legal owner.
- Confirm which professionals and clinics can be recruited for the provider MVP.

## Sources

Primary operating sources:

- Google people-first content: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Google SEO Starter Guide: https://developers.google.com/search/docs/fundamentals/seo-starter-guide
- Google site moves and URL mapping: https://developers.google.com/search/docs/crawling-indexing/site-move-with-url-changes
- Google spam policies and scaled content abuse: https://developers.google.com/search/docs/essentials/spam-policies
- Google internal-link guidance: https://developers.google.com/search/docs/crawling-indexing/links-crawlable
- Google Article structured data: https://developers.google.com/search/docs/appearance/structured-data/article
- Google Organization structured data: https://developers.google.com/search/docs/appearance/structured-data/organization
- Google ProfilePage structured data: https://developers.google.com/search/docs/appearance/structured-data/profile-page
- Israeli Ministry of Health licensed-practitioner lookup: https://www.gov.il/en/service/licensed-medical-practitioners
- Israeli Ministry of Health misleading medical advertising service: https://www.gov.il/en/service/complaint_mislead_medical_advertisement
- Israeli Privacy Protection Authority database-registration guidance after Amendment 13: https://www.gov.il/he/service/registration_in_the_database
- Semrush Organic Positions manual: https://www.semrush.com/kb/494-organic-rankings-positions-report
- Semrush Position Tracking Cannibalization Report: https://www.semrush.com/kb/1066-position-tracking-cannibalization-report

Observed competitor/SERP-format sources, not medical authorities:

- MedReviews aesthetic medicine directory: https://www.medreviews.co.il/search/aesthetic-medicine
- Clalit Aesthetics doctor directory: https://www.clalitaesthetics.co.il/doctors/
- Estheticare content/price/lead marketplace: https://www.estheticare.co.il/
- Lumera hair-transplant landing and price format: https://lumera.co.il/hair-transplant
- MyPrice plastic-surgery comparison funnel: https://www.myprice.co.il/myprice/Category.aspx?catid=94

