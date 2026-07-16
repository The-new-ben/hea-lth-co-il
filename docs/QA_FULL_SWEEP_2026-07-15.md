# Full visual QA sweep — every live page, with screenshots (2026-07-15)

24 captures (20 pages desktop 1440 + 4 mobile 660) of production. Key evidence
committed under docs/qa-evidence/2026-07-15/; full set in session scratchpad.

## CRITICAL found → fixed → deployed → verified (theme 0.5.2, release 67d848d)

**The accessibility panel was visibly stuck OPEN on every page for every
visitor.** Root cause: `.hp-a11y__panel { display: grid }` overrides the HTML
`hidden` attribute (author styles beat the UA `[hidden]{display:none}` rule) —
the JS toggled the attribute correctly, so property-based checks passed while
the panel stayed painted. This was the owner-reported "stuck / overflowing".
Fix: `.hp-a11y__panel[hidden] { display: none; }`. Verified live by COMPUTED
style (none↔grid across toggle) and by fresh captures on both viewports.
Lesson recorded: visibility QA must assert computed style / paint, never DOM
properties alone.

## Per-page verdicts (production)

| Page | Verdict |
|---|---|
| Home desktop+mobile | ✓ hero body+resolver, sections, tools, footer; panel closed post-fix |
| /anatomy/ | ✓ full resolver UI, honest map gate, text alternative, boundary card |
| Hubs (diagnostics +3 children, wellness +1, private-medicine) | ✓ hero, intro+boundary, 4-path grid, standards band |
| /guides/, /glossary/ | ✓ honest empty states; thin until first approved content (known front) |
| /find-care/ | ✓ rich: 5 path cards, 4-step band, boundaries |
| /health-technology/, /treatments/, /professionals/ | ✓ template families render correctly |
| /about/, /editorial-policy/, /privacy/, /terms/, /accessibility/ | ✓ content pages render, statement complete |
| /contact/, /profile-update/, /account/ | ✓ render + noindex verified earlier |
| Search results | ✓ H1 "תוצאות עבור:" renders |
| 404 | ✓ designed recovery page (home + index links) |
| All 22 provisioned routes | ✓ 200, one main, one H1 |

## Open floats (ranked)

1. **[MED] Dark-void stage without WebGL/while loading** (home + /anatomy/):
   headless/no-GPU shows a large empty dark circle. Add a static render poster
   behind the canvas (assets exist in docs/3d-evidence + tmp renders) — also
   improves LCP perception. Touches non-frozen hero wrapper CSS only.
2. **[MED] Content thinness** on guides/glossary/hubs until the first reviewed
   guides land — the named next front (money keywords through the editorial gate).
3. **[LOW] 404 could carry a search box** (currently links only).
4. **[LOW] Capture pipeline**: mobile headless shots must use 660px width (the
   ≤680 breakpoint) — 390px triggers the RTL phantom-clip artifact.

## Capture-pipeline lessons (for every future QA)

- Mobile visual captures: 660px window; measurements: in-pane after resize+reload.
- WebGL never renders headless (no GPU) — model absence in captures is an
  artifact; verify the viewer in-pane (computed/ready events) instead.
- Assert computed style for visibility, not element properties.
