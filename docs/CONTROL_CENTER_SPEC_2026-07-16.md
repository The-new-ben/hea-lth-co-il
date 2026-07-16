# Hea-lth Control Center — full 360° specification (2026-07-16)

Owner ask: "wire it to cms, make control center for the 3D + map + index + content, deep control on every aspect and monetization, all 360, full spec."

## 1. Product definition

One wp-admin surface (menu **Hea-lth**, `manage_options` only) that governs the entire body-map-index chain: what the 3D model resolves to, what the map shows, who the paying clients are, what content ships, and how leads convert. The center is a **governed cockpit, not a bypass**: every write lands in an option that the existing safety gates re-validate, so no owner action (or compromised admin session) can put unlicensed models, unlabeled paid placement, or unreviewed routes in front of visitors.

Implementation: `plugin-src/hea-lth-platform-core/includes/class-hea-lth-control-center.php` (plugin 0.9.0), theme handshake in `functions.php` (`hea_lth_portal_anatomy_discovery_url()` + `hea_lth_anatomy_discovery_url` filter, theme 0.9.0).

## 2. Tabs and their contracts (v1 — shipped)

### Overview
Status board: model gate state + clickable-structure count, map state + client count, index override counts, page states (blueprint-managed / owner-edited / missing), WhatsApp channel state, platform version. Each row links to its tab.

### Map & clients (monetization core)
- **Featured clients CRUD**: name, specialty (bounded list: plastic-surgery, aesthetic-medicine, hair-transplant, orthopedics, dermatology), address, phone, https website, lat/lon, badge (default "לקוח מאומת"), verified-at date, remove checkbox; add-row button (vanilla JS clone).
- **Default map view**: center lat/lon + zoom (7–16).
- Writes compose a full manifest into the existing gated option (`hea_lth_directory_map_manifest`). The registry sanitizer re-validates everything (IL bounding box, https-only links, bounded fields); invalid saves keep the previous safe state. `allowedOrigin` is bound to `home_url()` in code — never typed.
- **The commercial disclosure is not a form field.** Every saved pin gets the fixed label "שיבוץ מסחרי, פרופיל לקוח של Hea-lth". Contract-tested: the source must not contain a disclosure input.

### Body index
- Per region/context: every shipped resolver entry with a show/hide checkbox; add-entry rows (kind, label, route key from a **dropdown of the controlled route map only**).
- Stored in `hea_lth_resolver_overrides`; served by REST `GET /wp-json/hea-lth-platform/v1/anatomy-discovery`, which merges overrides into the shipped dataset via the pure `merge_resolver_overrides()` — unknown route keys are dropped (fail closed), shipped structure is immutable beyond visibility + appends.
- The theme asks the plugin for the dataset URL through the `hea_lth_anatomy_discovery_url` filter; with zero overrides the static shipped file keeps serving (zero regression, zero extra requests).

### Content & pages
- Blueprint table: every provisioned page with state — Blueprint-managed (hash matches: receives release content updates), Owner-edited (protected forever), Template-driven, Missing — plus Edit/View links.
- "Run page provisioning now" button: creates missing pages + refreshes blueprint-managed content on demand (deletes the blueprint version option and re-runs). Owner-edited pages are never overwritten (hash guard in the provisioner).

### Monetization
- **WhatsApp lead channel**: business number (empty = bar off sitewide); message prefill always carries the source page.
- **Featured-client inventory**: live pins / 20 capacity, disclosure policy statement, link to CRUD.
- **Lead-routing audit**: read-only view of the existing per-route audit (verified recipient, capacity, consent version, audit date, commercial disclosure). Routes activate only when their audit passes — unchanged.
- **Products index**: pointer into Body index (which regions surface products) + Content (the guide pages).

### 3D model
Read-only gate transparency: approved/gated state with machine reason, model id/version, structure/layer counts, LOD table with triangle counts. Deliberately **no switch can bypass** the license/clinical/QA manifest gate; changing the model = shipping a new manifest through the same gates. Freeze notice included.

## 3. Security model

- Capability: `manage_options` on every render and every handler (contract-tested ≥5 occurrences).
- CSRF: `wp_nonce_field` + `check_admin_referer` per form/handler (contract-tested ≥4 each).
- Input: every POST field individually sanitized, then re-validated by the domain gate (map manifest → registry sanitizer; index → allowlist + bounded fields; WhatsApp → digits, 9–15).
- Output: all escaped (`esc_html`/`esc_attr`/`esc_url`).
- The center never calls `wp_update_post`/`wp_delete_post` (contract-tested): content editing stays in the WP editor with its own permissions and history.
- No secrets rendered; the REST endpoint serves only public resolver data.

## 4. Data model (options)

| Option | Shape | Consumer |
|---|---|---|
| `hea_lth_directory_map_manifest` (existing) | Full map manifest JSON incl. `featuredProviders[]`, new optional `view{lat,lon,zoom}` | Map registry gate → `care-map.js` |
| `hea_lth_resolver_overrides` (new) | `{disabled: {region: {ctx: [entryIdx]}}, added: [{region, context, kind, label, routeKey}]}` (≤60 added) | Discovery endpoint merge |
| `hea_lth_whatsapp_number` (existing) | digits string | Theme consult bar |
| `hea_lth_provisioned_pages_blueprint` (existing) | version string | Provisioner |

## 5. Monetization flows (as wired today)

1. **Client onboarding (minutes, no deploy):** get the client's consent + details → Map & clients tab → add row → save → the pin is live with badge + fixed disclosure, spotlighted when visitors click matching body regions. Capacity 20 pins.
2. **Lead capture:** WhatsApp bar (page-context prefill) on every page; per-route lead routing stays audit-gated.
3. **Products:** body regions → product guide pages (SEO content); catalog/commerce is phase 2.

## 6. Phase 2 roadmap (specified, not built)

- **Client self-serve**: per-client login (custom role) to edit their own pin within the same gate; uploads (logo/photo) with review queue.
- **Billing**: pin subscription records (start/end, plan), auto-expiry of unpaid pins, invoice export. Stripe/GreenInvoice integration decision needed.
- **Analytics**: per-pin impressions/clicks/WhatsApp-opens (privacy-clean counters, no PHI), monthly client report.
- **Index depth**: per-entry custom query filters (bounded keys), reordering, A/B labels.
- **Products catalog**: product CPT with schema.org/Product JSON-LD, affiliate/seller links with disclosure, WooCommerce bridge when the store opens.
- **Map layers**: pharmacies + HMO branches (Overpass re-export), layer toggles per audience.
- **Roles**: a dedicated `hea_lth_manager` capability so staff can manage clients without full admin.

## 7. Verification

- New contract test `tooling/tests/control-center-contract-test.php`: merge logic (hide/allowlist/drop/no-op), capability/nonce counts, gated-option routing, hardcoded disclosure, no direct post mutation, theme handshake presence.
- Full battery green (27 contracts, PHPCS/PHPStan exit 0, pytest, 13-route render matrix).
- Live verification after deploy: healthcheck flip, `/wp-json/hea-lth-platform/v1/anatomy-discovery` returns the shipped dataset (200), public pages unchanged with zero overrides.

## 8. Explicit non-goals (law)

- No gate weakening from any admin surface (model licensing, disclosure, route control).
- No CRM/PHI storage in this repo or its options.
- No unlabeled paid placement, ever.
