---
name: wordpress-agent-deploy
description: The only lawful way to ship code to the live hea-lth.co.il WordPress — build, verify, deploy via the GitHub Actions pipeline, confirm public state, and recover. Use whenever installing, updating, deploying, hotfixing, or rolling back anything on the live site.
---

# Hea-lth WordPress deploy law

This repo already has a proven zero-manual-click deploy pipeline (live release `9d42872`, run 29167917145). Agents use it; they do not reinvent it, and they never deploy from a local machine with pasted credentials.

## Architecture (one line)

Reviewed code on `main` → GitHub Actions (`.github/workflows/wordpress-deploy.yml`) builds deterministic packages (`scripts/build-wordpress-package.py`, config `deploy/wordpress-deploy.json`) → `scripts/deploy-wordpress.py` authenticates with the Application Password from GitHub secrets, bootstraps Code Snippets if needed, installs a TEMPORARY token-gated REST route from `deploy/agentdeploy-route.php`, uploads + installs each package with backup, activates, purges ezCache (`/wp-json/ezcache/v1/cache`), verifies healthchecks + full public HTML markers, deletes the route, and rolls back on failure.

## How an agent ships

1. Branch (`codex/*` or `claude/*`), keep shipped PHP **7.4-compatible** (host runs PHP 7.4.33).
2. Local verification, all green, no skips:
   - `for t in tooling/tests/*.php; do php "$t" || break; done` and the `.mjs` tests with node
   - `python -m pytest tests/test_wordpress_pipeline.py -q`
   - `python scripts/build-wordpress-package.py --package <name>` for each changed package
   - `python scripts/deploy-wordpress.py --package <name> --dry-run` (no credentials needed)
3. Merge/push to `main`. Deploy triggers ONLY when these paths change: `theme-src/**`, `plugin-src/**`, `deploy/**`, `scripts/**`, `tooling/php-quality/**`, `tooling/web-quality/**`, `tests/test_wordpress_pipeline.py`, `.gitleaks.toml`, the workflow file. `docs/**`, `design-lab/**`, `tooling/tests/**`, `.claude/**` never deploy.
4. Full-pipeline rerun without a code change: `gh workflow run wordpress-deploy.yml --ref main` — **requires the owner's explicit confirmation in the current conversation** before firing.
5. Verify independently after every deploy (truth is the GET, not the pipeline log):
   - `https://hea-lth.co.il/wp-json/hea-lth-portal/v1/healthcheck` and `/wp-json/hea-lth-platform/v1/healthcheck` → `deployment_id` must match the new run
   - Homepage 200; child/parent theme asset markers present in the FULL HTML (never a truncated slice); old markup absent

## Hard rules (each one has caused a real outage somewhere)

1. Secrets (`WP_BASE_URL`, `WP_USER`, `WP_APP_PASSWORD`) live only in GitHub Actions. Never in files, chat, commits, or local env for a live run.
2. Never leave a privileged route or snippet active — the client creates and deletes per deploy; if a run dies mid-way, confirm the route 404s.
3. No custom HTTP headers and no HTML request bodies against the UPress origin — the WAF 403s before PHP runs (HTML error body = WAF, JSON = WordPress).
4. WordPress core `POST /wp/v2/plugins` installs wordpress.org slugs only — it can never install our packages; do not "try" it.
5. Version bumps: header + constant together; assets enqueue with the version constant, never hardcoded `?ver=`.
6. Zips only via the canonical builder (forward slashes; Windows ad-hoc zips corrupt paths silently).
7. A same-version reinstall is allowed (overwrite install), but a version DOWNGRADE is a rollback — use the pipeline's rollback state, don't hand-roll.
8. Emergency (site 500, REST dead): UPress file manager → rename the offending plugin/theme folder `.off` → site returns; then fix in Git and redeploy properly. Mirror any emergency hand-edit back into Git immediately.

## Reference

Deep cross-project handbook (nad-lan lineage, temp-route pattern rationale): `docs/references/WORDPRESS_AGENT_DEPLOY_PIPELINE_CROSS_PROJECT_HANDOFF_2026-07-10.md`. Where that document and this skill disagree, this skill and `AGENTS.md` win on this repo.
