@AGENTS.md

Claude-specific notes:
- Communicate with the owner in English unless asked otherwise; public site content is Hebrew (RTL).
- Local PHP is 8.3 but production is PHP 7.4.33 — run contract tests before proposing any shipped-code change.
- Never trigger the production deploy paths (see AGENTS.md) without the owner's explicit go-ahead in the current conversation.
- **Model policy (owner directive, 2026-07-13): only Claude Fable 5 (`claude-fable-5`) may change this repository.** If the session is running as any other model — including after a safeguard fallback to Opus — do NOT edit files, commit, push, or deploy. State plainly which model is running, stop code work immediately, and wait for the owner. Read-only analysis and honest status reports are the only permitted activity in that state. Never continue silently after a model switch.
- **Single narrow exception (owner, 2026-07-13):** a non-Fable session may continue a task ONLY when a runbook for it exists under `docs/runbooks/`, and only by executing that runbook verbatim — no refactors, no rewording, no scope beyond the written steps. If any step fails or is ambiguous, stop and report instead of improvising.
