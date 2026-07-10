# Israel Regulation, Privacy, Security, and Medical Advertising Checklist

**Status:** Research and legal-review checklist only.  
**Not legal advice:** No item below should be treated as a final interpretation of Israeli law. A qualified Israeli lawyer, privacy adviser, clinician, pharmacist, medical-device expert, accessibility professional, security professional, accountant, and/or credit specialist must validate the applicable model.

## 1. Operating-role definition

Before legal analysis, document which role Hea-lth performs for each journey:

- publisher;
- directory;
- ranking/comparison service;
- advertising platform;
- lead generator;
- matching coordinator;
- appointment intermediary;
- concierge;
- merchant;
- marketplace operator;
- seller of record;
- importer/distributor;
- pharmacy partner;
- software provider;
- processor/controller/database owner;
- clinical or medical-device software provider.

A single site may have multiple roles, but terms, consent, liability, verification, invoicing and data flows must be separated.

## 2. Privacy and health-information review

For every form, account, integration, call, message, upload and event:

- What exact purpose requires each field?
- Is the data ordinary personal data, health information, authentication data, payment data, communications content, or inferred sensitive data?
- What is the legal basis and consent language?
- Who is controller/holder/processor and who receives the data?
- Is the recipient named before submission?
- Is the data transferred to more than one provider?
- Is marketing consent separate from service consent?
- Is there an auditable consent ledger with version, channel, timestamp and withdrawal?
- What is the retention period and deletion trigger?
- Can the user access, correct, export or request deletion where applicable?
- Is data transferred outside Israel; on what safeguards?
- Are children/dependents involved; what authority and identity proof are required?
- Is the database subject to registration, notification or other obligations after Amendment 13 review?
- Do analytics, call tracking, session replay, ad pixels or chat tools receive sensitive form or URL data?
- Are free-text fields minimized and scanned for accidental health details?
- Are data-processing agreements and subprocessor lists current?
- Is there a breach-response path and statutory notification decision owner?

### First-contact gate

A general first request should ordinarily be limited to:

- service/category;
- region;
- timing;
- payer route;
- language;
- preferred contact channel;
- source/UTM;
- explicit consent.

Do not request diagnoses, medication lists, test results, medical images/files or full history through the first generic form.

## 3. Direct marketing and communications

Review:

- consent and opt-out for email, SMS, WhatsApp and calls;
- service messages versus promotional messages;
- proof of consent and suppression lists;
- provider/supplier co-marketing;
- remarketing and custom audiences;
- sensitive-category ad-platform restrictions;
- caller identification and recording notice;
- retention and access to call/message content.

## 4. Provider and clinic verification

For every professional/organization type:

- authoritative verification source;
- license/specialty/status fields;
- identity matching and name-change handling;
- verification date and expiry;
- periodic re-check;
- sanctions/status changes;
- organization affiliation proof;
- claims about expertise/experience;
- malpractice, disciplinary or quality claims, if used;
- correction and dispute route;
- sponsored-placement disclosure;
- ranking methodology;
- prohibited implications of clinical quality.

Do not claim that a provider is “best,” “leading,” “top,” or clinically superior without a lawful, documented, objective and defensible method.

## 5. Medical advertising and professional claims

Qualified counsel should review:

- advertising of medical services and professionals;
- outcome promises and superlatives;
- testimonials and reviews;
- inducements, limited-time pressure and discount framing;
- before/after images;
- influencer/affiliate disclosures;
- comparative claims;
- physician/clinic titles and specialty claims;
- use of hospital/university affiliations;
- treatment/package pricing and exclusions;
- sponsored editorial;
- claims for off-label uses;
- cross-border care advertising.

## 6. Medicines and pharmacy

For each proposed experience, determine:

- prescription versus OTC status;
- pharmacy licensing and seller role;
- online-sale requirements;
- prescription handling;
- price regulation and reimbursement;
- substitution;
- shortages and availability sources;
- advertising restrictions;
- approved labeling and patient leaflets;
- pharmacovigilance/adverse-event links;
- recall/status updates;
- delivery, storage and identity controls;
- reminder functionality and clinical boundary.

No prescription commerce should be approved without an exact licensed operating model and named licensed partner/owner.

## 7. Medical devices and consumer products

For every device/product category:

- classification and intended use;
- AMAR/other applicable registration/status;
- importer/distributor/manufacturer identity;
- approved claims and instructions;
- professional-only restriction;
- prescription or fitting requirement;
- serial/lot/UDI where applicable;
- warranty, service, calibration and maintenance;
- installation/training;
- adverse-event/recall path;
- used/refurbished condition and decontamination;
- ecommerce returns and hygiene exclusions;
- product-review moderation;
- comparison/recommendation boundary.

A product being sold elsewhere is not evidence that Hea-lth may sell or advertise it.

## 8. Ecommerce and marketplace

Review:

- seller of record;
- marketplace disclosure;
- price/VAT/shipping;
- payment and invoice;
- cancellation and returns;
- refunds/chargebacks;
- warranty/service;
- merchant/supplier verification;
- prohibited or restricted products;
- product-feed accuracy;
- stock/availability representation;
- subscriptions and renewal;
- marketplace terms and dispute handling;
- fraud and sanctions screening where relevant.

## 9. Financing and leasing

Determine:

- who offers credit/lease;
- whether Hea-lth is a referrer, broker, merchant or party;
- required licensing/disclosure;
- APR/cost and representative examples;
- affordability/marketing restrictions;
- treatment cancellation/refund interaction;
- supplier settlement and recourse;
- B2B versus consumer rules;
- data shared with finance providers.

## 10. Reviews, testimonials, and before/after media

Controls:

- verified-event eligibility;
- incentive disclosure;
- identity/duplicate/fraud checks;
- no requirement to disclose diagnosis publicly;
- moderation standards;
- clinical-outcome and defamatory claims;
- provider right of response;
- dispute/escalation;
- image consent and scope;
- editing/cropping/filter disclosure;
- representative-results warning;
- withdrawal and retention;
- minors and sensitive procedures;
- sponsor separation.

## 11. Accessibility

Qualified accessibility review should determine the applicable Israeli obligations and target. Independently, the product should use WCAG 2.2 AA as the engineering target unless counsel or procurement requires more.

Audit:

- semantic structure and landmarks;
- keyboard and focus;
- contrast and text resizing;
- form labels/errors;
- screen-reader order in RTL;
- mobile navigation;
- captions/transcripts;
- image/diagram/3D alternatives;
- motion and vestibular controls;
- comparison tables;
- authentication and MFA;
- third-party widgets, ads, booking and payment;
- accessibility statement and contact;
- manual testing with assistive technology.

An accessibility toolbar is not proof of conformance.

## 12. Security baseline

### Identity and access

- least privilege and RBAC;
- MFA/passkeys for privileged and professional accounts;
- provider/supplier identity proof;
- session timeout, device and recovery controls;
- separate admin/operator/reviewer roles;
- periodic access review;
- offboarding.

### Data

- encryption in transit and at rest where appropriate;
- secrets management;
- field-level minimization;
- segregated sensitive uploads;
- retention/deletion jobs;
- backups and restore tests;
- audit logs;
- data residency/vendor review;
- no sensitive data in URLs, logs or ordinary analytics.

### Application and operations

- secure development/review;
- dependency/plugin inventory;
- patch SLA;
- staging separation;
- WAF/rate limiting/bot controls;
- vulnerability scanning and penetration testing;
- payment scope reduction;
- incident response and tabletop tests;
- provider/supplier account takeover controls;
- abuse, scraping and review fraud controls;
- business continuity and manual fallback.

### AI-specific

- prompt injection;
- sensitive-information disclosure;
- insecure output handling;
- excessive agency;
- tool and connector permissions;
- retrieval-source poisoning;
- model/vendor logging and retention;
- red-team tests;
- human escalation;
- kill switch and feature flags;
- model/version provenance.

## 13. Analytics and tracking

No ordinary analytics event should contain:

- diagnosis or suspected diagnosis;
- medication;
- test/result;
- uploaded file name/content;
- free-text request;
- provider conversation;
- full phone/email;
- government ID;
- payment detail.

Use category IDs, funnel states, timestamps, source IDs and pseudonymous/internal identifiers. Review page URLs and query strings for sensitive content before enabling analytics or ad pixels.

## 14. Feature launch risk levels

| Level | Example | Launch requirement |
|---|---|---|
| L0 | Public non-medical page | Standard content, accessibility, privacy and security QA |
| L1 | Reviewed education/navigation | Medical-editor review and evidence controls |
| L2 | Provider/product discovery and request | Verification, consent, routing, SLA, complaint and legal review |
| L3 | Booking, account, checkout, quote, files | Strong identity, security, data-flow, terms, failure and support controls |
| L4 | Clinical AI, Rx, medical-image or patient-specific prediction | Separate regulated/clinical program and explicit executive risk acceptance |

## 15. Legal evidence record

For every legal conclusion, record:

`issue_id | exact question | applicable role/journey | primary legal source | regulator guidance | adviser | conclusion | uncertainty | required control | effective date | review date | launch decision`

## 16. Mandatory launch sign-offs

Depending on feature:

- product owner;
- medical governance;
- privacy;
- security;
- Israeli legal adviser;
- pharmacy/device specialist;
- finance/accounting;
- accessibility;
- operations/support;
- executive risk owner.

“No objection by email” is not a launch record. Use a signed decision log with conditions and expiry.
