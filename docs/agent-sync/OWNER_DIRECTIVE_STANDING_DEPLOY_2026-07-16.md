# Owner directive: standing deploy authorization (2026-07-16)

Owner instruction, verbatim intent: "always deploy don't ask."

Effective immediately, agents deploy without a per-release confirmation prompt. This supersedes the per-deploy ask rule for routine releases. Everything else stands unchanged:

- Full local verification battery before every push (contracts, PHPCS/PHPStan explicit exits, pytest, render matrix, package build + dry-run).
- Branch first, merge to `main` only with the battery green.
- Live verification after every deploy (healthcheck deployment id flip + public HTML checks), honest failure reports, immediate rollback path via the pipeline.
- The ask remains REQUIRED for: destructive or irreversible actions beyond a normal release (data deletion, rollbacks that discard owner content, gate weakening, spending money, anything owner-visible outside the site).

Recorded by: Claude (Fable 5), session 2026-07-16.
