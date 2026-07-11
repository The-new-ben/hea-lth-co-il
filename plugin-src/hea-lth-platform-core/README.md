# Hea-lth Platform Core

This plugin is the data foundation for the new Hea-lth portal. It is deliberately separate from the theme so records stay portable if the visual system changes. WordPress recommends registering custom post types in a plugin for that reason.

## Implemented foundation

- Admin-managed entity types for professionals, clinics, treatments, glossary entries, and equipment or technologies.
- Controlled taxonomies for specialties, regions, service types, and body regions.
- Typed metadata for public verification state, city, languages, accessibility, last verification date, editorial state, review date, and source note.
- A read-only `hea-lth/v1/directory` endpoint that returns only entries that are both published and explicitly marked `verified`.
- A gated anatomy-model registry. Its public `hea-lth/v1/anatomy/model` endpoint returns no model path until written web and derivative rights, clinical review, GLB validation, visual QA, and performance QA are all approved.

## Deliberate boundaries

- No public profile route is registered yet. This avoids replacing powered URLs before the migration map and approved profile design exist.
- No inquiry intake endpoint exists. No health information, diagnosis, documents, payment, or account data is stored by this plugin.
- No 3D asset, vendor key, contract, or private source path is exposed by the public anatomy endpoint. The endpoint releases only an approved, public-safe runtime configuration.
- No provider is created by the code. A record needs public facts, verification, publication approval, and a responsible owner before it can be returned by the directory endpoint.
- Equipment is a data entity only. Product catalog, availability, checkout, fulfillment, and regulated-product controls need their own approved integration.

## Example read-only call after controlled activation

`GET /wp-json/hea-lth/v1/directory?specialty=plastic-surgery&region=tel-aviv&limit=12`

The endpoint accepts `specialty`, `region`, `service`, and `limit`. It does not accept write requests.

## Release gate

Do not package or activate this plugin until the WordPress staging environment, content ownership, provider verification process, URL migration plan, privacy review, and rollback plan are ready. The plugin is source only and has not changed the live site.
