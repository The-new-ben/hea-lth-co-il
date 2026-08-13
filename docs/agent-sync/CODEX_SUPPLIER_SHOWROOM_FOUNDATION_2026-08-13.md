# Codex session: supplier showroom foundation

- Date: 2026-08-13
- Branch: `codex/supplier-showroom-foundation`
- Scope: public supplier entity, premium showroom templates, canonical equipment pages, verified seed catalogs, route registry and contract tests.
- Deployment: direct production through the repository GitHub Actions pipeline. No staging environment is created.
- Privacy: no buyer lead data, brokerage terms or private CRM data is stored in the repository.
- URL decision: supplier company intent lives at `/suppliers/{slug}/`; machine intent lives once at `/medical-equipment/{supplier}-{product}/`.
