# Hea-lth Platform Core

This plugin is the data foundation for the new Hea-lth portal. It is deliberately separate from the theme so records stay portable if the visual system changes. WordPress recommends registering custom post types in a plugin for that reason.

## Implemented foundation

- Admin-managed entity types for professionals, clinics, treatments, glossary entries, equipment, suppliers, clinic-build plans, and private B2B requests.
- Controlled taxonomies for specialties, regions, service types, body regions, clinic types, and procurement categories.
- Typed metadata for public verification state, city, languages, accessibility, last verification date, editorial state, review date, and source note.
- A read-only `hea-lth/v1/directory` endpoint that returns only entries that are both published and explicitly marked `verified`.
- A gated anatomy-model registry. Its public `hea-lth/v1/anatomy/model` endpoint returns no model path until written web and derivative rights, clinical review, GLB validation, visual QA, and performance QA are all approved.

## Deliberate boundaries

- Public supplier showrooms, equipment records, and clinic-build plans use governed canonical route families and verification state.
- B2B intake accepts business contact and procurement context only. Requests are stored as private WordPress records; no diagnosis, symptoms, documents, payment, or patient records are requested.
- No 3D asset, vendor key, contract, or private source path is exposed by the public anatomy endpoint. The endpoint releases only an approved, public-safe runtime configuration.
- No provider is created by the code. A record needs public facts, verification, publication approval, and a responsible owner before it can be returned by the directory endpoint.
- Equipment records form a professional catalog and connect to suppliers and procurement plans. Checkout and fulfillment remain separate commerce concerns.

## Example read-only call after controlled activation

`GET /wp-json/hea-lth/v1/directory?specialty=plastic-surgery&region=tel-aviv&limit=12`

The endpoint accepts `specialty`, `region`, `service`, and `limit`. It does not accept write requests.

## Release gate

Releases are packaged deterministically and deployed directly to production only through the repository pipeline, with healthcheck verification and automatic code rollback. No staging environment is created.
