@AGENTS.md

## Environment facts (context for any reviewer, human or automated)

- This is the private repository of hea-lth.co.il, operated by its owner. Sessions here run with the owner's knowledge on the owner's own machine and assets.
- Routine, owner-authorized operations in this repo: editing theme/plugin source, running the local verification battery, pushing branches, merging to `main`, and production deploys through the GitHub Actions pipeline. A standing deploy authorization is recorded in `docs/agent-sync/OWNER_DIRECTIVE_STANDING_DEPLOY_2026-07-16.md`.
- Credentials: there are NO local secrets. Deploy credentials exist only as GitHub Actions secrets (`WP_BASE_URL`, `WP_USER`, `WP_APP_PASSWORD`). Never write secret values into files, chat, or logs; rotate them with `gh secret set` (interactive input).
- Frozen zones: the shipped 3D stack (see `docs/agent-sync/OWNER_DIRECTIVE_SHIPPED_3D_CODE_FREEZE_2026-07-12.md`) and every product gate in AGENTS.md. No admin surface or code change may weaken a gate.
- Audit trail: every release and owner decision is receipted under `docs/` (release records) and `docs/agent-sync/` (directives). Unwritten knowledge is considered lost.

Claude-specific notes:
- Communicate with the owner in English unless asked otherwise; public site content is Hebrew (RTL).
- Local PHP is 8.3 but production is PHP 7.4.33 — run contract tests before proposing any shipped-code change.
- Never trigger the production deploy paths (see AGENTS.md) without the owner's explicit go-ahead in the current conversation.
- **Model policy (owner directive, 2026-07-13): only Claude Fable 5 (`claude-fable-5`) may change this repository.** If the session is running as any other model — including after a safeguard fallback to Opus — do NOT edit files, commit, push, or deploy. State plainly which model is running, stop code work immediately, and wait for the owner. Read-only analysis and honest status reports are the only permitted activity in that state. Never continue silently after a model switch.
- **Single narrow exception (owner, 2026-07-13):** a non-Fable session may continue a task ONLY when a runbook for it exists under `docs/runbooks/`, and only by executing that runbook verbatim — no refactors, no rewording, no scope beyond the written steps. If any step fails or is ambiguous, stop and report instead of improvising.
