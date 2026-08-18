# New-machine migration runbook — hea-lth.co.il working kit

Everything the project needs travels in this repository. Local-only pieces (PHPCS vendor, node_modules, dist builds) regenerate from committed manifests. Secrets never travel: they live only as GitHub Actions secrets and are re-entered where needed.

Follow the steps in order. Each block is copy-paste ready.

## 1. Install the toolchain (PowerShell, admin once)

```bash
winget install --id Git.Git -e; winget install --id GitHub.cli -e; winget install --id OpenJS.NodeJS.LTS -e; winget install --id Python.Python.3.11 -e
```

**PHP + Composer: do NOT use winget** — the `PHP.PHP.8.3` manifest chronically 404s (each PHP patch archives the previous zip; hit live 2026-08-18). After cloning (step 3), run the script that reads the current build + sha256 from php.net's own releases.json, verifies both downloads, installs to `%USERPROFILE%\tools\php-8.3`, adds user PATH, and rebuilds the quality vendor (making step 4 unnecessary):

```bash
powershell -ExecutionPolicy Bypass -File docs/new-machine/install-php-toolchain.ps1
```

Close and reopen the terminal after installing, then verify:

```bash
git --version && gh --version && php -v && node -v && python --version && composer --version
```

## 2. Authenticate GitHub

```bash
gh auth login
```

Choose github.com → HTTPS → login with browser, as account `The-new-ben`. This is the only credential the machine holds.

## 3. Clone the repository

Prefer the same path as before so tooling paths and the memory folder name match:

```bash
gh repo clone The-new-ben/hea-lth-co-il "C:/Users/pro/Documents/websites/hea-lth-co-il" && cd "C:/Users/pro/Documents/websites/hea-lth-co-il"
```

If the username on the new machine is not `pro`, clone to the equivalent path under your profile; everything still works, only the memory folder name (step 5) will differ.

## 4. Rebuild the local-only pieces

```bash
cd tooling/php-quality && composer install --no-interaction --prefer-dist && cd ../..
```

Optional (only needed to run the axe audit locally; CI runs it anyway):

```bash
npm ci --prefix tooling/web-quality --ignore-scripts
```

## 5. Install Claude Code and restore memory

1. Install Claude Code (desktop app or `npm install -g @anthropic-ai/claude-code`), sign in, and open ONE session in the project folder — this creates the per-project memory directory.
2. Then run this to copy the seeded memory in (PowerShell):

```bash
powershell -Command "$dest = Get-ChildItem \"$env:USERPROFILE\.claude\projects\" -Directory | Where-Object { $_.Name -like '*hea-lth*' } | Select-Object -First 1; if ($dest) { New-Item -ItemType Directory -Force \"$($dest.FullName)\memory\" | Out-Null; Copy-Item 'docs/new-machine/memory-seed/*.md' \"$($dest.FullName)\memory\" -Force; Write-Host ('Memory restored to ' + $dest.FullName) } else { Write-Host 'Open Claude Code once in the project folder first.' }"
```

## 6. Verify the machine can develop (the battery)

All must pass before any code work:

```bash
for t in tooling/tests/*.php; do php "$t" || break; done
```

```bash
for t in tooling/tests/*.mjs; do node "$t" || break; done
```

```bash
python -m pytest tests/test_wordpress_pipeline.py -q
```

```bash
php tooling/php-quality/vendor/bin/phpcs --standard=tooling/php-quality/phpcs.xml.dist -q
```

```bash
php tooling/php-quality/vendor/bin/phpstan analyse -c tooling/php-quality/phpstan.neon.dist --no-progress --memory-limit=2G
```

## 7. Rotated passwords — what actually needs updating

- **GitHub login**: handled by step 2 on this machine.
- **WordPress application password** (used by the deploy pipeline): if it was rotated, the pipeline's secret must be updated once or the next deploy fails at authentication. The deploy secrets live in the GitHub **environment `production`** (a plain repo-level `gh secret set` would NOT take effect — environment secrets override it). Run and paste the new value when prompted (it is never stored in the repo):

```bash
gh secret set WP_APP_PASSWORD --repo The-new-ben/hea-lth-co-il --env production
```

  Same command pattern for `WP_USER` / `WP_BASE_URL` if those changed. Inspect current state with `gh secret list --repo The-new-ben/hea-lth-co-il --env production` (the dates show when each was last set). The next real deploy verifies them end to end.
- **wp-admin password**: only needed for the browser; the control center lives at wp-admin → Hea-lth.

## 8. Open the working session

Open Claude Code in the project folder, make sure the model is `claude-fable-5` (owner policy: no other model may change this repo), and paste the session prompt from `docs/new-machine/SESSION_PROMPT.md`.

Notes:
- Project skills auto-load from `.claude/skills/` (god-mode, wordpress-agent-deploy). Useful built-in commands: `/code-review` (and `/code-review ultra` for the cloud multi-agent review), `/verify`, `/simplify`, `/security-review`, `/loop`, `/schedule`.
- `.claude/settings.json` (committed) carries the tool allowlist; approve it once when asked.
- For visual QA in a real browser, install the Claude in Chrome extension; the in-app browser pane suspends animations (documented in the memory files).
