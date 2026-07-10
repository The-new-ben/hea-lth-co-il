# Open Questions and Data Requests

**Purpose:** Identify the minimum owner information required to protect existing value and replace speculation with first-party evidence.  
**Data handling:** Provide aggregated/redacted exports where possible. Do not place patient medical records or unneeded health details in the research data room.

## P0 — Required before URL, migration, current-performance, or financial decisions

| Input/access | Requested scope | Why required | Decisions blocked without it | Safety note |
|---|---|---|---|---|
| Google Search Console | 16–24 months query/page/date/country/device exports; indexing; sitemaps; manual actions; links; removals | Current search value and losses | keep/merge/redirect, keyword ownership, migration risk | Export only; no account password sharing |
| Analytics | 16–24 months landing pages, acquisition, events, conversions, device, geography; data dictionary | Actual use and conversion | page prioritization, funnel, revenue attribution | Exclude PII/free text/health detail |
| Full URL/crawl inventory | XML sitemaps, CMS export, status, canonical, noindex, title, headings, links, word/media, schema | Complete legacy map | any architecture or redirect decision | Immutable dated snapshot |
| Backlinks | Ahrefs/Semrush/Majestic/GSC exports, disavow, linked URLs/anchors | Preserve authority | redirect and deletion | Do not rely on one tool only |
| WordPress/CMS | Version, database/content export, CPTs/taxonomies/fields, users/roles, media, redirects | Data/architecture reality | technical plan, migration, entity model | Secure transfer and least privilege |
| Plugins/theme/custom code | Plugin/version/license list, theme, snippets, mu-plugins, integrations | Compatibility/security | upgrade, Woo, accounts, booking | No production changes during audit |
| UPress/hosting/CDN | Architecture, PHP/DB versions, cache/CDN, backups, staging, logs, WAF, cron/queues | Reliability/security/performance | technical baseline | Read-only access or exports |
| GitHub/deployment | Repositories, branches, CI/CD, secrets approach, deployment/rollback | Portability and control | engineering plan | Never export secrets into research files |
| CRM/lead/call data | Aggregated request→contact→accept→booking→show→sale/cancel/refund/lost reason by source/category/provider/date | Unit economics and operations | first revenue engine, SLA, routing | Remove medical narrative and direct identifiers |
| Current forms/consents | Screenshots/fields, consent text/version, recipients, routing, retention | Privacy and conversion | lead-flow design | Include all chat/WhatsApp/call widgets |
| Provider/clinic inventory | Legal name, profession/license source/status, specialty, location, services, commercial status, response, contract | Supply truth | marketplace pages, matching, economics | No “verified” assumption |
| Supplier/product inventory | Supplier identity, contracts, brands/models, regulatory docs, stock, margin, warranty/service | Commerce feasibility | B2C/B2B plan | Separate public data from confidential terms |
| Financial actuals | Revenue by stream, payroll/contractors, hosting/tools, media, sales, legal, refunds, payment fees, marketing | 36-month model | budget, runway, break-even | Accountant-approved aggregation |
| Contracts/terms/privacy | Provider/supplier/consumer terms, privacy notice, DPA, vendor list, cookie/marketing policies, insurance | Legal role and obligations | launch gates | Privileged documents handled appropriately |
| Author/reviewer records | Names, qualifications, licenses, specialty, conflicts, contract, throughput | Medical governance | publication capacity | Consent for public profile use |

## P1 — Required for high-confidence strategy

| Input | Use |
|---|---|
| Google Ads/Microsoft Ads search-term and conversion exports | Validate commercial intent, language and lead economics |
| Semrush/Ahrefs project and keyword exports | Volume/trend/CPC/KD/rank data with source dates |
| Call tracking and redacted transcript themes | Objections, qualification, lost reasons, privacy issues |
| Support/complaint/refund logs | Trust and operating burden |
| Email/SMS/WhatsApp journey maps | Communication, consent and SLA |
| Provider/supplier pipeline | Acquisition stages, objections, sales cycle and churn |
| Existing price observations/quotes | Price methodology and inclusions |
| Brand assets/Figma/design system | UX and implementation continuity |
| Prior SEO/content briefs and editorial calendar | Avoid duplicate work and understand assumptions |
| Surveys/interviews | User/provider/supplier needs and willingness |
| Payment/order/invoice data | Commerce and fee economics |
| Security assessments/incidents | Risk and remediation priorities |

## P2 — Helpful

- owner/board budget range and risk appetite;
- preferred initial verticals and reasons;
- institutional/clinical relationships;
- desired exit/strategic outcomes;
- language expansion priorities;
- media/3D assets and rights;
- competitor accounts or subscriptions;
- prior research and market reports;
- customer personas and brand studies.

## Owner decisions required

1. Which legal/commercial role should the company assume in Phase 1?
2. Which three verticals are eligible for validation and which are excluded?
3. What clinical and reputational risk is unacceptable?
4. Will the portal publish provider reviews? Under what verified-event model?
5. Will providers pay subscriptions, leads, bookings, sponsorship, software fees, or a combination?
6. Is request-based matching acceptable before live calendars?
7. Will Hea-lth collect any medical documents? For which exact journey and with what secure system?
8. Will Hea-lth ever be seller of record?
9. Will inventory be owned, dropshipped, affiliated, or partner-fulfilled?
10. Is B2B equipment a separate business unit?
11. What minimum response SLA can operations enforce?
12. What evidence is required before displaying a price?
13. What constitutes provider verification?
14. Who may approve medical claims, corrections and emergency unpublishing?
15. Who owns privacy/security incidents?
16. What is the minimum reviewer budget/capacity?
17. What technical changes are already planned or contracted?
18. Are there URL/brand/domain changes contemplated?
19. What geographic supply exists outside the center?
20. What data rights can be negotiated with providers/suppliers?

## Owner-level questions not in the original brief

1. What is the legally and commercially precise service promised to a consumer after they submit a request?
2. Can the company enforce provider response and data-quality standards contractually?
3. What happens when no suitable provider accepts a request?
4. Is the company willing to remove a paying provider for verification, response, complaint or disclosure failures?
5. Which first-party dataset can reach sufficient coverage to be credible within 12 months?
6. What is the minimum viable supply for a national-looking page to avoid misleading users?
7. Which vertical has the highest ratio of decision friction to operational burden?
8. Which vertical produces repeat use rather than one-off SEO traffic?
9. Which payer/reimbursement data can be maintained accurately?
10. Can a provider’s availability be displayed with a trustworthy timestamp?
11. What is the correction and redress process when a price or service description is wrong?
12. Who bears the cost of failed, duplicate, rejected or ineligible requests?
13. What is the company’s policy when commercial and editorial priorities conflict?
14. Can reviewer throughput support the desired publishing cadence?
15. Which content or data rights are actually owned and transferable?
16. What would make a hospital, society or manufacturer share authoritative data?
17. What is the acquisition value of the platform: audience, supply, workflow, data, brand, or technology?
18. Which decisions create irreversible regulatory or working-capital exposure?
19. What is the manual fallback for every integration?
20. What evidence would justify stopping the project or a vertical?
21. What will users trust Hea-lth to do that they will not trust a clinic, insurer, search engine or general AI to do?
22. How will the company prevent the platform from becoming a pay-to-rank directory?
23. What can be measured without sending sensitive data to ordinary analytics?
24. What is the expected support load per booking/order/quote?
25. Which strategic partner could reduce the largest launch risk rather than merely add brand prestige?

## Safe transfer principles

- Prefer exports to shared passwords.
- Use read-only access where possible.
- Do not export production secrets.
- Redact direct identifiers and medical narratives unless a legally approved, necessary secure analysis is defined.
- Timestamp every export.
- Preserve raw files immutably and process copies separately.
- Record source system, query/filter, timezone and known data-quality issues.
