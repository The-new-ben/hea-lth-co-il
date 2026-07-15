# Session concurrency rule (owner-flagged incident, 2026-07-15)

Two Claude sessions worked the same repo in parallel today, sharing the single
local checkout. Result was benign only by luck: session A ("complete all")
started unknowingly on session B's branch (`claude/body-explorer-a11y`),
committed a docs file onto it, and swept B's uncommitted accessibility-widget
edits into its own release (now live — nothing lost). B's branch ended up
fully contained in main; B's staged GLB exports were moved to
`tmp/z-anatomy/staged-layers/`.

## Binding rules for every agent session (Claude, Codex, any)

1. **One active code session per repo at a time.** A session that intends to
   edit files MUST first check `git branch --show-current` + `git status`; if
   the tree is dirty or on another session's branch, STOP and report instead
   of working over it.
2. **Announce intent** in `docs/agent-sync/` (or at minimum in the commit
   trail) before multi-file work; check for another session's announcement.
3. **Never leave the shared checkout dirty or on a feature branch** at the end
   of a turn: commit + push your branch, then return the checkout to `main`.
4. **Deploy rights belong to one session per day** — the one the owner is
   actively driving. Parallel sessions are read-only advisors.
5. Uncommitted work left in the tree is fair game to be swept into the active
   session's commit — that is what rule 3 prevents.

## Disposition of session B's proposals (so nothing is dropped)

- Accessibility widget with visible "נגישות" label — **already live** (0.5.0).
- CC0/free skin-envelope over the anatomy + face fix — good idea, adopted as
  `docs/runbooks/SKIN_ENVELOPE_PROTOTYPE_RUNBOOK_2026-07-15.md` (gate-first).
- Homepage services index/map + sourced medical/aesthetic catalog — overlaps
  the live resolver and the store direction memo; blocked on owner Phase A
  inputs (`docs/MEDICAL_STORE_DIRECTION_2026-07-13.md`), not on code.
