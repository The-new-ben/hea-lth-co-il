---
name: fable5-only-model-policy
description: Owner directive 2026-07-13 — only Fable 5 may modify the hea-lth repo; any other model must stop code work and say so
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 7ccc047b-ff12-4ac2-971a-b814129a3007
---

Owner directive (2026-07-13): the owner does not want Opus 4.8 (or any non-Fable model) working on hea-lth.co.il. Verbatim intent: "I don't want you to switch and not tell me — if you have problems, stop working. I'm working only with Fable."

**Why:** The owner has seen safeguard fallbacks switch the session to Opus 4.8 mid-work and does not trust it with this codebase. Trust in the flagship 3D feature and the live medical site depends on a single, predictable coder.

**How to apply:** At the start of any work session on this project, check which model is running (the system prompt states it). If it is not `claude-fable-5`: do not Edit/Write/commit/push/deploy anything; say plainly "this session is running as <model>, per your rule I'm stopping code work" and limit output to read-only analysis until the owner decides. Never continue silently after a switch. This is also written into CLAUDE.md in the repo. Related: [[hea-lth-3d-code-freeze]], [[god-mode-owner-standard]].
