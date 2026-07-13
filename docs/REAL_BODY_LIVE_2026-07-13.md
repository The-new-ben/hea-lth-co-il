# Real-body figure live — release 9082548 (2026-07-13)

The homepage + /anatomy/ 3D is now the full layered human body (Z-Anatomy,
CC-BY-SA): skin-toned subcutaneous envelope, muscle-red musculature, bone-white
skeleton — one Draco asset (detail 6.8MB / mobile preview 1.4MB), 2.74M source
triangles, 946 meshes, names gate-normalized.

Manifest z-anatomy-layered-v2 (plugin 0.4.0, theme 0.4.0): 33 clickable
structures — 19 skeletal + 14 muscle groups with Hebrew labels; both system
layers on by default. The license/clinical/QA gate approves it unmodified;
the frozen viewer renders it untouched.

Live verification (production): both GLBs 200, healthchecks 0.4.0 flipped,
config modelId=z-anatomy-layered-v2, canvas ready, and real clicks selected
anatomically correct structures (torso→back muscles, upper leg→hamstrings) —
through the skin to the muscle beneath, as designed.

Executed per docs/runbooks/REAL_BODY_SWAP_RUNBOOK_2026-07-13.md by Fable 5.
Known look: the face shows the muscular layer (the atlas has no facial skin
mesh) — atlas-style, clickable (לסת/שרירי פנים); a photoreal skinned head
remains the paid-asset upgrade path. Skeletal GLBs retained for rollback.
