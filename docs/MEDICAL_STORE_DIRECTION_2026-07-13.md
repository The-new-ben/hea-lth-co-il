# Medical-equipment store — direction memo (2026-07-13)

Owner floated: keep WooCommerce and open a store for medical appliances.

## Recommendation: yes — as a governed phase, and it completes the flagship

A curated home-health equipment store is a natural extension of the portal and
of the 3D body engine: the resolver already returns *services* per body part;
*products* become a second entry kind (knee region → braces; skin region →
clinically-reviewed devices). This is exactly the "click a part → services and
products" vision, and none of the Israeli health portals do it well.

WooCommerce stays active (its assets are already trimmed off portal pages, so
it costs visitors nothing until the store opens).

## Non-negotiables before the first product goes live

1. **Regulatory (אמ"ר):** medical devices marketed in Israel require Ministry
   of Health AMAR registration; verify each product's registration and
   category (consumer vs. prescription). No unregistered devices, ever.
2. **Consumer law:** Israeli distance-selling rules (14-day cancellation,
   full price + shipping disclosure, invoice).
3. **Trust architecture:** real supplier agreements, real stock, real prices —
   the same "no invented data" law as providers. Product pages carry sources
   and never make medical claims beyond the manufacturer's registered
   intended use.
4. **Separation:** commerce never touches the YMYL editorial gates; guides may
   link to a product category, products may link to guides, but reviews/claims
   stay governed.

## Suggested phase plan

- Phase A (owner): choose the first 10–20 SKUs + supplier(s); confirm AMAR
  status; decide payments (Isracard/Bit gateway) and fulfilment.
- Phase B (build): store design in the portal design system, product schema
  (JSON-LD Product), resolver "products" entries per body region, checkout QA.
- Phase C (launch): soft launch behind a menu item, measure, then surface in
  the 3D panel.

Blocked on Phase A owner inputs; no store work ships before them.
