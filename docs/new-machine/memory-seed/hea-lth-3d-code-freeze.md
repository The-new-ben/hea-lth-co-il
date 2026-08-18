---
name: hea-lth-3d-code-freeze
description: "Owner directive 2026-07-12 — do not change the shipped 3D integration code (commits 2206dd0→034ce22); additive-only, minimal evidence-backed fixes only"
metadata: 
  node_type: memory
  type: feedback
  originSessionId: 7ccc047b-ff12-4ac2-971a-b814129a3007
---

Owner directive (2026-07-12, verbatim): "avoid changing code fable 5 wrote." Interpreted and recorded with the owner-visible commit series since git does not tag model authorship: the live 3D stack from commits `2206dd0` → `87cf42b` → `034ce22` (viewer JS, DRACOLoader + decoder, GLBs, manifests, registry default-manifest fallback, contract test, plus the anatomy blocks inside functions.php / front-page.php / portal.css / theme-preview harness) is **frozen**.

**Why:** The owner trusts this code as verified and live on hea-lth.co.il (release 034ce22) and fears churn — especially from model switches (Fable 5 → Opus safeguard swaps) or Codex — breaking the flagship 3D feature. Stability beats stylistic improvement.

**How to apply:** Never refactor, rewrite, restyle, or "improve" the protected files/sections. New features go in new files or appended blocks. Defect fixes require a demonstrated defect (rendered evidence or failing test) and the minimum diff, reported to the owner. The parked perf option (preview LOD on homepage) touches protected code — do it only if the owner explicitly asks. Full protected list: `docs/agent-sync/OWNER_DIRECTIVE_SHIPPED_3D_CODE_FREEZE_2026-07-12.md` in the repo; also bound on all agents via AGENTS.md. Related: [[hea-lth-3d-gate]], [[god-mode-owner-standard]].
