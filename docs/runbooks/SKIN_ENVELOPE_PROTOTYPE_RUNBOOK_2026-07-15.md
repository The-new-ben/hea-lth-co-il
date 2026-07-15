# Runbook: full skin envelope prototype (face included) — gate-first

Goal: upgrade the live layered figure so the outermost layer is a complete,
natural human skin (including the face, which Z-Anatomy lacks), sourced from a
free/CC0 full-body mesh aligned over the existing anatomy.

Execution rules are identical to the real-body runbook: Fable 5 preferred;
a fallback session may only execute verbatim; frozen files stay frozen; the
manifest gate is never weakened; nothing deploys without the owner's explicit
go-ahead in-conversation.

## Steps

1. **Source + license first.** Candidate mesh must have a verifiable free
   license permitting web delivery and derivatives (CC0 preferred). Record
   source URL, license text, and provenance in the manifest license block —
   the gate requires real owner + sourceUrl; "found on the internet" fails.
2. **Prototype BEFORE integration.** Import over
   `tmp/z-anatomy/layered-v2` anatomy in Blender; scale/align to the existing
   figure (1.70m). Produce front + face renders as evidence. Owner sees the
   prototype renders before any pipeline work.
3. On approval: add a `skin-envelope` layer (kind `surface`,
   defaultVisible true) via a new combined export (extend
   `export_layered_figure_v2.py`; skin material HL_Skin). Keep muscular +
   skeletal layers unchanged. Skin meshes stay OUT of structure meshIds so
   clicks keep passing through to mapped anatomy.
4. Normalize names → promote GLBs (versioned filenames) → regenerate manifest
   (bump modelId to `z-anatomy-layered-v3`, update license block to include
   the skin source attribution) → gate contract test must pass unmodified.
5. Full battery + harness click-through + mobile weight check (preview LOD
   budget ≤2MB) → branch → owner deploy approval → live verification.
