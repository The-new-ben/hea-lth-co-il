# B2B monetization operating model — 2026-08-13

## Commercial products

| Product | Recommended commercial range | Value delivered |
|---|---:|---|
| Verified supplier presence | 690–1,290 ILS/month | Verified company identity, categories, contact and index presence |
| Premium showroom | 4,900–12,000 ILS setup + 1,990–3,900 ILS/month | Mini-site, managed catalog, canonical equipment pages, internal distribution and reporting |
| Category sponsorship | 5,000–20,000 ILS/month | Clearly labeled category visibility and content collaboration |
| Qualified equipment opportunity | 10% of closed value or minimum 8,000–15,000 ILS | Protected introduction, attribution and follow-up |
| Clinic-build sourcing | 10,000–25,000 ILS buyer project fee plus agreed supplier commissions | Multi-category requirements map, supplier coordination and quote comparison |
| Commerce | Category-specific margin | Consumables, accessories, wellness devices and other transaction-suitable products |

These are internal starting ranges. Each supplier agreement records the selected model, attribution window, non-circumvention terms and payment trigger.

## Funnel

1. Scientific and technology content creates qualified discovery.
2. Equipment pages capture model-level intent.
3. Supplier showrooms establish commercial trust.
4. Clinic plans expand one machine inquiry into a complete procurement account.
5. The B2B intake stores the business context and consent privately.
6. Operations qualify the request, select relevant suppliers and record attribution.
7. Revenue is reconciled by subscription, project fee, qualified lead, closed-deal commission or commerce margin.

## Current product instrumentation

- `clinic_quote` and `supplier_join` are the only accepted public B2B request types.
- Requests are private `hp_b2b_request` records.
- Source context, project stage, selected categories, plan interest and consent version are stored.
- Aggregate `b2b_submit` counters show submission volume by context without public lead exposure.
- The existing internal lead-route resolver remains the eligibility layer for later automated distribution.

## Supplier account product now implemented

- Public plan selection records `verified`, `showroom`, or `growth` on the private supplier application.
- Published commercial anchors are ₪990/month for Verified, ₪7,500 setup plus ₪2,490/month for Showroom, and ₪3,900/month plus an agreed success fee for Growth.
- A supplier account is linked to an existing reviewed `hp_supplier` profile by explicit WordPress user IDs.
- The private supplier portal shows plan state, showroom link, published catalog count, catalog submissions, and assigned opportunities.
- Supplier catalog changes enter a private review queue. They cannot publish directly or receive a public URL.
- A business request reaches a supplier dashboard only after explicit assignment.
- Buyer contact details remain absent until `hp_lead_release_state` is explicitly changed to `released` by an administrator.
- Supplier pipeline updates are limited to the assigned request and append an audit event.
- Each assigned opportunity can now carry its own percentage, fixed, or hybrid brokerage model, minimum fee, and attribution window.
- Supplier acceptance is affirmative, timestamped, tied to the assigned supplier account, and protected by a terms fingerprint.
- Buyer contact details cannot be released unless the current terms fingerprint validates.
- Reassignment or any economic change invalidates the earlier acceptance and returns contact details to the held state.
- Closed deal value produces an integer ILS commission calculation. Invoice readiness requires accepted terms, a won deal, and a positive closed value.
- Invoice states follow `not_ready → ready → issued → paid`; state skipping is rejected and paid records are immutable.
- The owner control center shows offered and accepted terms, invoice counts, outstanding commission, and collected commission.

## Next operating controls

- CRM export or integration after the operational owner selects the system.
