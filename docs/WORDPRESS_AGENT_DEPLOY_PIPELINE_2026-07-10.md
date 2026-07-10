# Hea-lth WordPress agent deployment pipeline

**Status:** implemented locally; live credential connection and first canary remain.

## Outcome

This pipeline removes the manual WordPress plugin-update gate while preserving artifact integrity, rollback, auditability, and temporary-route cleanup.

```text
reviewed Git commit
  -> deterministic WordPress ZIP
  -> SHA-256 + metadata + reproducibility test
  -> GitHub Actions validation
  -> encrypted production credentials
  -> WordPress core REST authentication
  -> temporary allowlisted deployment bridge
  -> checksum-verified upload
  -> isolated backup + overwrite install
  -> external release identity healthcheck
  -> finalize or verified rollback
  -> delete temporary bridge and prove 404
```

## Production truth established

- GitHub repository: `The-new-ben/hea-lth-co-il`.
- Repository visibility: private.
- Default branch: `main`.
- GitHub access: authenticated repository administrator with Actions/workflow scope.
- GitHub Actions: enabled.
- GitHub Environment: `production` exists.
- Repository secret present: `WP_BASE_URL=https://hea-lth.co.il`.
- Secrets still required: `WP_USER`, `WP_APP_PASSWORD`.
- Live WordPress REST index: reachable.
- Live WordPress administrator UI version: `7.0.1`.
- Live active theme observed publicly: `hello-elementor`.
- Code Snippets REST namespace: currently absent.
- WordPress core plugin REST controller: exposed and suitable for authenticated one-time bootstrap.

The repository's `health-revenue` theme is therefore packaged and validated but not auto-activated. Automatic production deployment currently targets only `hea-lth-ops`.

## Files

| File | Responsibility |
|---|---|
| `.github/workflows/wordpress-deploy.yml` | CI, deterministic validation, artifact evidence, automatic `main` deployment |
| `deploy/wordpress-deploy.json` | allowlisted package definitions, version rules, health routes, size ceiling |
| `deploy/agentdeploy-route.php` | temporary authenticated WordPress bridge with preflight/run/rollback/finalize |
| `scripts/build-wordpress-package.py` | deterministic ZIP, version sync, SHA-256, metadata, ZIP safety validation |
| `scripts/deploy-wordpress.py` | authenticated bootstrap, upload, health verification, rollback, cleanup |
| `tests/test_wordpress_pipeline.py` | route rendering, multipart, bootstrap, release identity, rollback tests |
| `plugin-src/hea-lth-ops/` | governed operations plugin and independent health surface |

## Why each tool exists

| Tool | Why it is required |
|---|---|
| Git/GitHub | immutable source/release identity and audited change history |
| GitHub Actions | unattended deployment runner, encrypted secrets, concurrency, release evidence |
| WordPress Application Password | revocable site-specific API credential; avoids storing the interactive account password |
| WordPress core REST | authenticated bootstrap path using core authorization |
| Code Snippets | short-lived bridge creation without a one-time manual plugin upload |
| PHP `Plugin_Upgrader` / `Theme_Upgrader` | WordPress-native overwrite semantics and compatibility checks |
| SHA-256 | proves GitHub-built and WordPress-received packages are byte-identical |
| Deterministic ZIP builder | makes the same commit produce the same artifact and digest |
| Health endpoint | proves the expected component, semantic version, and deployment ID are serving |
| WP-CLI | local/staging diagnostics and recovery; not a replacement for the production API workflow |
| Composer | reproducible PHP dependency and QA tooling |
| Gitleaks | prevents accidental credentials from entering Git history |
| actionlint | validates GitHub Actions semantics before push |

## Local operator environment

| Component | Installed/verified state |
|---|---|
| PHP | 8.3.31 with OpenSSL, cURL, ZIP, mbstring, MySQL, Intl, GD, and Sodium configuration |
| Composer | 2.10.2; diagnose and HTTPS/security checks pass |
| WP-CLI | 2.12.0; release signature verified against the WP-CLI release key |
| GitHub CLI | authenticated as repository administrator with `repo` and `workflow` scopes |
| actionlint | 1.7.12; GitHub release SHA-256 verified |
| Gitleaks | 8.30.1; GitHub release SHA-256 verified |
| Python | deployment scripts compile and unit tests run locally |
| Codex WordPress deploy skill | installed and validated under `.codex/skills/wordpress-agent-deploy` |

## Security controls

- GitHub workflow permission is `contents: read`.
- Third-party Actions are pinned to full commit SHAs.
- Production runs are serialized and an in-flight release is never cancelled.
- The private ZIP is uploaded directly; it is not published at a mutable raw-branch URL.
- WordPress requires both the Application Password identity and a high-entropy one-run token.
- Package slugs and package kinds are allowlisted.
- New versus existing plugins/themes require the corresponding install/update capability.
- WordPress direct filesystem mode is mandatory for unattended mutation.
- Uploaded bytes are rejected unless SHA-256 matches.
- Existing package files are copied to an isolated deployment-ID backup before overwrite.
- Theme activation is blocked in the generic automatic route.
- Health verification checks component, version, and deployment ID.
- Failed or ambiguous run requests trigger rollback attempts.
- Rollback verifies target presence and prior version; the client then verifies the public health surface or route absence.
- The temporary snippet is deleted in a `finally` path and its route must return 404.
- Logs never include passwords, tokens, or Authorization headers.

## GitHub configuration

Required encrypted secrets:

| Name | Scope | Value |
|---|---|---|
| `WP_BASE_URL` | repository or `production` environment | `https://hea-lth.co.il` |
| `WP_USER` | preferably `production` environment | dedicated WordPress deployment administrator username |
| `WP_APP_PASSWORD` | preferably `production` environment | site-specific Application Password |

The Application Password should be named `hea-lth-github-deploy` and belong to a dedicated deployment identity when practical. Never store the interactive WordPress password.

## First connection sequence

1. Log in to `https://hea-lth.co.il/wp-admin/` as an administrator.
2. Open the deployment user's profile and create the site-specific Application Password after the required security confirmation.
3. Store username and Application Password directly as encrypted GitHub secrets; do not paste them into files or chat.
4. Validate authenticated `/wp-json/wp/v2/users/me` and `update_plugins` capability.
5. Push the pipeline branch and run CI without production promotion.
6. Merge the approved pipeline to `main`.
7. GitHub Actions builds the `hea-lth-ops` ZIP, bootstraps Code Snippets if absent, creates the temporary route, deploys version `0.1.0`, verifies release identity, and removes the route.
8. Confirm `/wp-json/hea-lth-ops/v1/healthcheck` and confirm `/wp-json/agentdeploy/v1/preflight` returns 404.
9. Exercise a safe rollback canary before using the same transport for revenue or lead-routing changes.

## Commands

Build and validate locally:

```powershell
$env:SOURCE_DATE_EPOCH = git log -1 --format=%ct
python scripts/build-wordpress-package.py --package hea-lth-ops
python scripts/deploy-wordpress.py --package hea-lth-ops --dry-run
python -m unittest -v tests/test_wordpress_pipeline.py
php -l deploy/agentdeploy-route.php
php -l plugin-src/hea-lth-ops/hea-lth-ops.php
```

Live deploy from a trusted operator shell, if intentionally needed:

```powershell
$env:WP_BASE_URL = 'https://hea-lth.co.il'
$env:WP_USER = '<deployment-user>'
$env:WP_APP_PASSWORD = '<application-password>'
python scripts/deploy-wordpress.py --package hea-lth-ops --bootstrap-code-snippets --deployment-id 'manual-<commit-sha>'
```

Prefer GitHub Actions so the release identity and logs remain centralized. Never write the password into shell history.

## Failure and rollback contract

| Failed gate | Automatic behavior | Success criterion before retry |
|---|---|---|
| Authentication/capability | no mutation | dedicated identity authenticated with required capabilities |
| Bootstrap | no route or package mutation | Code Snippets active and namespace reachable |
| Preflight | no package mutation | direct filesystem and upload capacity verified |
| Upload/checksum | reject package | local and received SHA-256 match |
| Install/activation/version | route restores backup | previous target/version and activation state restored |
| Health identity | route restores backup | restored health/version or first-install route absence verified |
| Finalize | rollback attempt | known serving version and no ambiguous backup state |
| Route cleanup | deployment marked failed | temporary snippet removed and bridge returns 404 |

## Exact open blocker

```text
BLOCKER: Live WordPress deployment identity is not yet connected to GitHub.
NEEDED: Administrator login plus one site-specific WordPress Application Password.
WHY: It authorizes core REST plugin bootstrap and the temporary deployment bridge.
OWNER ACTION: Sign in to the open hea-lth.co.il WordPress administrator tab; at credential creation, confirm the named Application Password.
VERIFY: /wp-json/wp/v2/users/me returns the deployment user with update_plugins, then the encrypted WP_USER and WP_APP_PASSWORD secrets appear by name in GitHub.
```

No insecure workaround is accepted for this blocker.
