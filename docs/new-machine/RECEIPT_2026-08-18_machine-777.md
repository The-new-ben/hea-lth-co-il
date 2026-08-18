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

---

## Addendum — same day, autonomous run (owner granted blanket go-ahead)

- **winget was a dead end for PHP**: manifest tops out at 8.3.32, which PHP pulled from downloads.php.net (even from archives); current is 8.3.33. Wrote `install-php-toolchain.ps1` (this folder) — resolves the live build + sha256 from php.net's releases.json, verifies both PHP and composer.phar checksums, installs to `%USERPROFILE%\tools\php-8.3` + user PATH, rebuilds the quality vendor. MIGRATION.md §1 updated; the winget PHP step is retired.
- **Installed & verified (eyes)**: PHP 8.3.33 (ZTS vs16 x64), Composer 2.10.2, PHPCS 3.13.6 + WPCS 3.4.1 + PHPCompatibility, PHPStan 2.2.5 + WP stubs.
- **Full battery GREEN on machine 777**: PHP contract tests **34/34**; node contract tests pass; pytest **19/19**; PHPCS **exit 0**; PHPStan **exit 0** (`--memory-limit=2G`). The machine is fully development-capable.
- **Live visual evidence (looked at, not just captured)**: `docs/qa-evidence/live-0180-home-desktop-2026-08-18.png`, `live-0180-home-mobile660-2026-08-18.png`, `live-0180-anatomy-desktop-2026-08-18.png` — RTL nav, hero, 986-institution care map with pins, path cards, WhatsApp bar, a11y button, editorial disclaimer all present on live 0.18.0. Dark 3D stage in captures = documented headless-WebGL artifact, not a bug.
- Still owner-only: `WP_APP_PASSWORD` re-set (env `production`) — next deploy fails auth until then if the app password was rotated.
- In progress: first Claude review pass over the Codex B2B layer (PRs 17–28).
