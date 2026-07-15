# Hea-lth Portal — Shared Agent Contract

This file is the single operating contract for every AI agent (Codex, Claude, or any other) working on hea-lth.co.il. Read it before changing anything. If chat instructions conflict with this file, the repository owner's latest explicit instruction wins; otherwise this file wins.

## Mission

Institute-grade Hebrew (RTL) private-health portal at https://hea-lth.co.il: trusted YMYL medical knowledge, a verified provider directory, an interactive 3D body-discovery engine, and consent-first commercial lead routing. Revenue comes from qualified private-care leads and verified provider listings. Credibility is the product: no invented medical facts, providers, reviews, statistics, or outcomes — ever.

## Source of truth

- GitHub: `The-new-ben/hea-lth-co-il` (private). Default branch: `main`.
- Local working copy used by both Codex and Claude: `C:\Users\pro\Documents\websites\hea-lth-co-il`.
- All project knowledge lives in the repo, not in chat history:
  - `docs/` — strategy, SEO, audits, decisions, handoffs (start with `docs/AGENT_HANDOFF_HEA_LTH_PORTAL_2026-07-11.md` and `docs/PORTAL_SOURCE_FILE_MAP_2026-07-12.md`).
  - `design-lab/` — design tokens, home concepts, and the 3D engine manifest contract (`design-lab/3d-human-engine/`).
  - `tooling/tests/` — the contract test suite. `tooling/theme-preview/` — local render harness with fixtures.
- If you learn something, decide something, or receive an owner decision: write it to a file under `docs/` and commit. Unwritten knowledge is considered lost.
- Cross-agent disclosure: write to `docs/agent-sync/` as `<AGENT>_<TOPIC>_<YYYY-MM-DD>.md`. Never put secret values there. Mark unknowns as `UNKNOWN` — do not guess.
- **One active code session per repo at a time** (owner incident, 2026-07-15): before editing, check `git status` + current branch; if the tree is dirty or on another session's branch — stop and report. End every turn with the checkout committed, pushed, and back on `main`. Full rule: `docs/agent-sync/SESSION_CONCURRENCY_RULE_2026-07-15.md`.

## Architecture

| Component | Source path | Live status |
|---|---|---|
| Parent theme | `theme-src/hea-lth-portal/` | Deployed (inactive parent) |
| Child theme (active) | `theme-src/hea-lth-portal-child/` | Live v0.1.0 |
| Platform plugin | `plugin-src/hea-lth-platform-core/` | Live v0.1.0 |
| Deploy manifest | `deploy/wordpress-deploy.manifest.json` | Governs packaging |
| Deploy client | `scripts/` (REST-based, GitHub Actions only) | Proven |
| CI/CD | `.github/workflows/wordpress-deploy.yml` | Proven, includes rollback + cache purge + public HTML verification |

Healthchecks: `/wp-json/hea-lth-portal/v1/healthcheck` (child theme) and `/wp-json/hea-lth-platform/v1/healthcheck` (plugin) — both return the deployment id of the running release.

## Production safety (critical)

- A push to `main` touching any of: `theme-src/**`, `plugin-src/**`, `deploy/**`, `scripts/**`, `tooling/php-quality/**`, `tooling/web-quality/**`, `tests/test_wordpress_pipeline.py`, `.gitleaks.toml`, or the workflow file **deploys to production**.
- `docs/**`, `design-lab/**`, `tooling/tests/**`, `tooling/theme-preview/**`, `AGENTS.md`, `CLAUDE.md` do **not** trigger deployment.
- The live host runs **PHP 7.4.33**. All shipped PHP (theme + plugin) must remain PHP 7.4 compatible: no enums, readonly properties, fibers, `never` type, first-class callables, or named arguments. Local PHP is 8.3 — never assume production matches it.
- Secrets exist only as GitHub Actions secrets (`WP_BASE_URL`, `WP_USER`, `WP_APP_PASSWORD`; environment `production`). Never write secret values into files, commits, chat output, or docs. Local secret storage for Codex lives outside this repo.
- WordPress admin manual changes must be documented in `docs/agent-sync/` — the repo cannot see them otherwise.

## Owner mode ("God Mode") — binding for every agent

Operate as the responsible owner, not a task executor. Full standard: `.claude/skills/god-mode/SKILL.md`. Non-negotiables: benchmark every change against the category's world best (BioDigital/Complete Anatomy for 3D; Mayo, Cleveland, Healthline for content; RealSelf for marketplace; Clalit/Maccabi for Israeli RTL UX) and never ship below them; QA everything you touch (RTL, 375px mobile, keyboard, edge states) and **float** every below-standard finding with severity, even outside your task; claims require rendered evidence — no unverified success reports; judge each change through product, UX, SEO, performance, and monetization/trust lenses; open sessions with a position check and close them with shipped + floats + benchmark delta + next moves.

## Deployment

Follow `.claude/skills/wordpress-agent-deploy/SKILL.md` — the deploy law for this repo. Summary: only the GitHub Actions pipeline deploys; agents verify locally (tests + package build + `deploy-wordpress.py --dry-run`), push to `main`, and confirm the public `deployment_id` flipped. A no-code rerun (`gh workflow run wordpress-deploy.yml`) needs the owner's explicit confirmation in the current conversation.

## Verification before any code push

Run from the repo root; all must pass:

- PHP contract tests (22): `foreach ($t in Get-ChildItem tooling/tests/*.php) { php $t.FullName }` — or loop with bash: `for t in tooling/tests/*.php; do php "$t" || break; done`
- JS/module contract tests: `for t in tooling/tests/*.mjs; do node "$t" || break; done`
- Pipeline tests: `python -m pytest tests/test_wordpress_pipeline.py -q`
- Local render matrix: serve `tooling/theme-preview/index.php` (php -S) and confirm every route returns 200 with exactly one `main` and one `H1`.
- Shipped-code static gates (match CI): PHPCS + PHPStan configs under `tooling/php-quality/`.

## Non-negotiable product gates

1. **3D anatomy** — the public WebGL viewer loads only when the plugin manifest passes every gate in `plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php`: manifest `status=approved`; license with `webDeliveryAllowed`, `derivativeUseAllowed`, real owner + contract reference; clinical review approved with named owner + date; GLB valid; visual, performance, anatomical-fidelity, and semantic-mesh QA passed; a detail LOD ≥ 100,000 triangles; asset served same-origin. Do not weaken the gate; make the asset pass it. The manifest schema and skeleton generator live in `design-lab/3d-human-engine/`.
2. **Editorial** — guides, glossary, and single posts render publicly only with approved review state, review date, and visible source (`theme-src/hea-lth-portal/inc/portal-template-helpers.php`).
3. **Providers** — only `verified` entities render publicly; no premium visual priority without labeled disclosure.
4. **Leads** — consent-first; routes must pass the lead-route audit (verified recipient, capacity, consent version, audit date, commercial disclosure). No CRM data or personal health information in this repo, ever.
5. **Directory map** — third-party maps load only through the plugin gate with a domain- and API-restricted key. No keys in source.
6. **Language** — public Hebrew follows `docs/DESIGN_DIRECTION_AND_PUBLIC_LANGUAGE_V1_2026-07-10.md`: calm, factual, no medical promises, no urgency manipulation.

## Content & SEO law

- Pillar–spoke architecture. Pillars and the keyword→URL map: `docs/SEO_MASTER_PLAN_2026-07-10.md` + `docs/KEYWORD_URL_MAP_SEED_2026-07-10.csv` (contract-tested by `tooling/tests/keyword-url-map-contract-test.php`).
- URL structure and canonical rules: `docs/URL_CANONICAL_DECISIONS_2026-07-11.md`. Never create a public URL outside the route registry (`theme-src/hea-lth-portal/inc/portal-route-registry.php`).
- Every public health claim needs a source and a review date. YMYL standards apply to all indexable content.

## Working conventions

- Branches: `codex/<topic>` (Codex), `claude/<topic>` (Claude). Code changes go through a branch + CI; docs-only changes may push straight to `main`.
- Conventional commits: `feat:`, `fix:`, `docs:`, `test:`, `chore:`.
- Prefer extending the existing registries/gates over new parallel mechanisms. One mega-menu, one route registry, one deploy pipeline.
- Do not add third-party CDNs at runtime; vendor assets into the theme (three.js is already vendored under `theme-src/hea-lth-portal/assets/vendor/three/`, r185).
- **Shipped 3D code freeze (owner directive, 2026-07-12):** the live 3D stack from commits `2206dd0`→`034ce22` must not be refactored, rewritten, or restyled by any agent. Additive work and evidence-backed minimal defect fixes only; anything else needs the owner's explicit instruction in the current conversation. Protected file list: `docs/agent-sync/OWNER_DIRECTIVE_SHIPPED_3D_CODE_FREEZE_2026-07-12.md`.
