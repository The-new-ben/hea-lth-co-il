# Premium engagement round — release 043abea (theme 0.6.2, plugin 0.6.0)

Owner brief: visible click→info on the model, premium natural copy, premium
verticals first, map, a11y placement, smart WhatsApp button.

Live-verified end-to-end (production):
- **Click→info**: selecting a body part shows a card with its Hebrew name +
  "מידע ושירותים לאזור" that drives the resolver services panel (verified:
  thigh → "ירך קדמית" → orthopedics/rehab services + guides).
- **Premium verticals**: new chest region (aesthetics-first) + lips context;
  anatomy:lips structure (34 total). Resolver contract passes (5 regions).
- **Copy**: "שירות רפואי פרימיום — לחצו על כל חלק במודל וקבלו את המידע סביבו".
- **A11y widget**: mid-left edge, panel opens beside it; screenshot-verified.
- **WhatsApp consult bar**: broad brand pill, wa.me/972525101555 with Hebrew
  prefill carrying page title + URL; bottom-center mobile / bottom-right
  desktop; screenshot-verified no stacking.
- **Map**: stays gated by design — needs the owner's domain-restricted Google
  Maps key + verified provider locations. Unlock steps in this doc's source
  conversation; no second map mechanism was built.

Two same-day patch lessons (both from verifying user-visible outcomes):
1. Script order: viewer config injects after engagement.js → lazy init (0.6.1).
2. The public gate flattens structure labels to a single `label` field —
   support both shapes (0.6.2). QA rule reinforced: verify the visible card,
   not the event.

Generator hardened: idempotent re-runs (a re-run had silently duplicated all
muscle groups against the committed base — caught by count check, fixed).
