# "Complete all" round (2026-07-15) — no linked route left behind

Closes every remaining completable item:

1. **All remaining 404s die.** Blueprint v3 (plugin 0.5.0) provisions 14 more
   pages, parents-first with nested permalinks: hubs on the new
   template-hub.php (diagnostics + imaging/laboratory/second-opinion, wellness
   + prevention, private-medicine) and content pages (about, editorial-policy,
   privacy, terms, contact, professionals/profile-update, account). Thin
   holding pages (contact, profile-update, account) ship noindex until their
   product exists. /skin/ is intentionally NOT provisioned — a legacy 301
   already serves it.
2. **Homepage title residue fixed in code**: the Yoast homepage title template
   is replaced only when it still carries the legacy brand ("פרימיום"/"בתיאום
   אישי") → "Hea-lth — מרכז בחירה לרפואה פרטית".
3. **Shipping-path hygiene**: stray unwired muscular/visceral GLB exports moved
   out of theme assets to tmp staging (they would have silently entered the
   next package); staged as the future organs/muscle-only layers.
4. Contract test rewritten for the path blueprint: registry membership,
   parents-before-children ordering, template-or-content, required paths,
   content boundaries (no-medical-advice, source credit).

Content law kept: hubs carry honest intros + governed destination grids; no
invented medical facts; privacy/terms marked for legal review.

Owner items that remain (not completable by an agent, by design):
accessibility-coordinator contact, store Phase A (SKUs/suppliers/AMAR),
Elementor decision (legacy commercial pages may depend on it — do NOT
deactivate blindly), GSC/GA4 access, imagery licensing.
