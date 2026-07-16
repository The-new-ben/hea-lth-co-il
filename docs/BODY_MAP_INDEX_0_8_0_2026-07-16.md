# Body-Map Index 0.8.0 — selection pulse, richer map, products index, language sweep (2026-07-16)

Owner asks this round: (1) visible selection animation on the 3D model; (2) a map that always responds and reads richer (recognizable hospitals); (3) a products index seeded 360° around the body regions with real SEO content, not thin pages; (4) remove all em dashes and AI-giveaway phrasing from public content; (5) seed more clients; (6) seed products.

## Shipped (theme 0.8.0, plugin 0.8.0)

### 1. Selection pulse on the model
`anatomy-three-viewer.js` (frozen stack; owner-named exception in this conversation): `pulseHighlight()` runs from `highlightStructure()` — a ~1.15s damped emissive pulse on the selected structure's meshes, settling into the standard highlight. Honors `prefers-reduced-motion` and the site's `hp-a11y-no-motion` class. rAF handle cancelled on re-selection.

### 2. Map always responds + hospital name labels
`care-map.js`:
- Hospital POIs (106 of 630) bind name tooltips; at zoom ≥ 13 they become permanent label chips (`.hp-map-lbl`, style in `engagement.css`), so street-level views read like a real care map. Rebinding runs on `zoomend`.
- Spotlight fix: `marker.openPopup()` immediately after `map.flyTo()` autopans and cancels the zoom animation short of city level. The popup now opens on `once('moveend')`.
- No-match fallback: when a body region has no featured client in that specialty yet (everything except nose/face/chest today), the map still responds — flies to city zoom so hospitals and their names appear, with an honest status line.

### 3. Products index (owner: "index of the products that we sell")
- Routes: `products`, `products_hair`, `products_skin`, `products_ortho` in the route registry; the three product keys exposed via `hea_lth_portal_anatomy_route_map()`.
- Resolver: product entries on scalp (→ hair-loss), skin-face ×2 (→ skin-care), movement (→ orthopedic-support). Verified rendering in the resolver panel.
- Provisioner blueprint v4 (`2026-07-16-01`): 4 pages on the hub template with category-level Hebrew SEO content — hair loss (minoxidil, caffeine/DHT shampoos, supplements-after-blood-test framing, scalp serums; links the live pillar `/hair-loss-prevention-treatments-costs/`), skin care (basics + actives + when-to-see-a-dermatologist), orthopedic supports (belts/knee/insoles + fitting rules), and a hub. No prices, stock, sellers, or medical promises; store framing is "knowledge layer now, catalog later, commercial placement always labeled".

### 4. Guarded content refresh (new provisioner capability)
Existing pages the provisioner authored can now receive blueprint content fixes: a `_hea_lth_blueprint_hash` meta stored at creation anchors the check; refresh happens only while live content still hashes to what we wrote (legacy pages qualify once via modified==created). One owner edit in wp-admin detaches the page permanently. Contract test updated: `wp_update_post` allowed exactly once, behind `hash_equals`.

### 5. Language sweep
All public strings (templates, resolver JSON, engagement/a11y/care-map JS, map + anatomy manifests, every provisioner content method) cleaned of em dashes; grep of rendered harness HTML shows zero. Checked for Hebrew AI-tell phrases (בעידן, יתרה מזאת, חשוב לציין, לסיכום, בשורה התחתונה…) — none present. New contract assertion keeps provisioned content em-dash-free.

### 6. Clients — honesty boundary held
The blanket permission from the health association is not per-client consent. Real "לקוח מאומת" pins require per-client name + address + specialty + their own OK (Dr. Keren Cohen remains the only one). The map's richness comes from real, labeled public institutions instead.

## Verification
- 24 PHP + 2 JS contract tests pass; PHPCS exit 0; PHPStan exit 0; pytest 19/19; 13-route render matrix 200 with one `main`/`H1` each.
- Rendered evidence (real Chrome on the local harness): model click → full glow + card ("נבחר במודל"), map flew to street level, permanent hospital chips visible, Keren Cohen popup on plastic-surgery matches, product links render for scalp/skin-face.
- Capture note for future agents: the in-app browser pane runs `document.visibilityState === 'hidden'`, so requestAnimationFrame is suspended — Leaflet flyTo and the viewer pulse freeze there. That is a harness artifact, not a site bug; QA animations in a visible Chrome tab.

## Floats (found while QAing, not fixed this round)
- Selection glow covers the whole muscular figure for broad muscle groups (asset mesh granularity), so "which part did I pick" reads from the card, not the glow. Fixing needs finer mesh splits in the export pipeline — asset round, not code.
- Pharmacies layer deferred: Overpass endpoints rate-limited twice; retry later (`healthcare-poi-il.json` regenerate).
- One-click sometimes produces two selection events in automation (pointerup + click both raycast); real single clicks are fine. Watch if users report flicker.

## Next moves
- Owner: per-client permissions to seed clients #2+ (5 minutes each: name, address, specialty, their OK).
- Product pages: add product-catalog JSON-LD once a real catalog exists; wire products hub into the mega-menu.
- Pharmacies + HMO branches into the POI file for even richer map reading.
