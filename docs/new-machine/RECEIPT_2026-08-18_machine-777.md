# Migration receipt — machine "777" online (2026-08-18)

The working kit was restored on the new machine. Facts below are verified on that machine unless marked otherwise.

## Verified (evidence: executed on the machine)

- Repo cloned to `C:\Users\777\hea-lth`, HEAD `edd35a2` == origin/main (no Codex commits since the kit landed).
- `gh` authenticated as **The-new-ben**, scopes repo + workflow.
- Toolchain present: git, gh, node, npm, python 3.11. **Missing: PHP, Composer** (winget block in MIGRATION.md §1 still pending — PHP contract tests, PHPCS, PHPStan cannot run until then).
- Memory restored: 8 seed files copied to `%USERPROFILE%\.claude\projects\C--Users-777-hea-lth\memory\`.
- Node contract tests: **pass** (anatomy resolver, portal navigation).
- pytest: installed on the machine, **19/19 pass** (`tests/test_wordpress_pipeline.py`).
- Live site healthcheck (HTTP): `{"status":"ok","version":"0.18.0","deployment_id":"gh-41a341f3…"}` — matches PR #28, site healthy.

## Corrections made to the kit

- `SESSION_PROMPT.md`: working directory updated to `C:\Users\777\hea-lth`.
- `MIGRATION.md` §7: deploy secrets live in the GitHub **environment `production`**; the secret-set command now carries `--env production` (a repo-level set would be shadowed and silently ineffective).
- Memory seed `hea-lth-project-core.md`: machine-777 state appended.

## Open items (owner)

1. Install PHP + Composer (MIGRATION.md §1), reopen terminal, then `composer install` in `tooling/php-quality` and run the full battery.
2. Passwords were rotated ~2026-08-18, but `WP_APP_PASSWORD` in environment `production` was last set **2026-07-10** → re-set it (`gh secret set WP_APP_PASSWORD --repo The-new-ben/hea-lth-co-il --env production`) before the next deploy; the first deploy is the end-to-end proof.
3. Claude in Chrome extension for real-browser visual QA (in-app pane freezes animations).

## Unreviewed backlog

Codex's PRs 17–28 (supplier portal, brokerage ledger, equipment/RFQ marketplace, science graph) shipped without a Claude review pass. First strong session should start there.
