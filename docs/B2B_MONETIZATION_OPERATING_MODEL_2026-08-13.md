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

## Next operating controls

- Request ownership and response SLA.
- Supplier agreement and attribution-window fields.
- Deal stage and estimated/closed value.
- Commission schedule and invoice state.
- CRM export or integration after the operational owner selects the system.
