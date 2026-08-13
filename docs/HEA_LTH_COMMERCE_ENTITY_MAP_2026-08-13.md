# Hea-lth commercial entity map — 2026-08-13

## Revenue architecture

Hea-lth is one connected marketplace, not a collection of unrelated blogs.

1. Biology and human systems establish topical authority.
2. Aging, skin, hair, body, metabolic health and wellness form consumer demand worlds.
3. Treatments and diagnostics answer solution intent.
4. Clinics and professionals answer provider-selection intent.
5. Equipment pages answer product and procurement intent.
6. Supplier showrooms answer company and catalog intent.
7. Clinic-build plans join equipment, software, services, finance, training and consumables into high-value B2B accounts.
8. Commerce converts qualified demand through leads, introductions, quotes, premium listings and later transactions.

## Canonical ownership

| Entity | Canonical pattern | Owns | Must not target |
|---|---|---|---|
| Scientific pillar | `/biology/{system-or-process}/` | Mechanism and evidence | Supplier or purchase intent |
| Consumer condition/goal | Existing mapped URL or reviewed new route | User problem and decision path | Individual machine brand |
| Treatment | Existing mapped URL or `/treatments/{slug}/` after review | Procedure comparison | Supplier company query |
| Equipment | `/medical-equipment/{supplier}-{product}/` | One machine/model | Broad treatment guide |
| Supplier | `/suppliers/{supplier}/` | Company, catalog and contact | Generic technology definition |
| Supplier index | `/suppliers/` | Discovery and comparison | Individual supplier name |
| Clinic build | `/professionals/clinic-build/{clinic-type}/` | Complete procurement bill | Patient treatment query |
| Consumer product | `/products/{category}/{product}/` or WooCommerce canonical | Retail offer | Professional equipment query |

One real-world entity has one indexable canonical URL. Hubs list entities but do not reproduce their full copy.

## Commercial ladder

- Free verified supplier identity.
- Paid enhanced showroom and catalog operations.
- Qualified lead fee or disclosed commission.
- Category sponsorship with explicit placement disclosure.
- Clinic-build procurement package across complementary suppliers.
- Data and performance reporting for supplier accounts.
- Commerce commission where checkout is suitable.

## Clinic-opening account map

A weight-management or aesthetic clinic may require more than a body-contouring machine:

- Consultation, consent and clinic-management software.
- Body composition and measurement systems.
- HIFEM/EMS, RF, cryolipolysis and complementary body platforms.
- Skin analysis and photography.
- Treatment beds, carts, lighting and room infrastructure.
- Sterilization, consumables and disposables.
- Staff training, protocols and equipment maintenance.
- Patient acquisition, financing and payment systems.
- Complementary wellness, nutrition and follow-up services.

Each requirement becomes a structured procurement category. Products attach to categories; categories attach to clinic types. This creates cross-sell without duplicating product pages.

## First production increment

- Public `hp_supplier` entity and `/suppliers/` marketplace.
- Premium supplier mini-site template.
- Canonical `hp_equipment` detail pages.
- Supplier-to-equipment relationship.
- Idempotent verified seed records for NUBWAY/Nicro and Galaxy.
- Twenty initial equipment records based on the suppliers' public catalogs.

No lead identity, brokerage agreement or private contact record belongs in the public content model.
