# Design elevation — round 1 (2026-07-12)

Owner directive: "be serious make website designed!!!" — this records the evidence-based
diagnosis, what shipped in `claude/design-system-v1`, and the owner decisions still open.

## Diagnosis (browser-verified, not guessed)

1. **Desktop renders as designed.** Live measurement at 1440px: hero h1 66.96px,
   two-column hero grid, both Hebrew families loaded. Earlier "shrunken typography"
   readings were a browser-pane artifact (resize without reload does not re-evaluate
   all media queries) — not a site bug.
2. **Mobile has no overflow.** At 390px: `scrollWidth === innerWidth === 390`.
   Headless-Chrome full-page captures of RTL pages show a false right-edge clip
   (scrollbar-side offset). Real-pane measurement is authoritative. Future agents:
   do not chase this phantom overflow.
3. **Real failures found:**
   - Fonts loaded from `fonts.googleapis.com` at runtime — violates the AGENTS.md
     no-third-party-CDN rule, GDPR exposure for a health property, render-blocking.
   - The reviewed-guides band rendered as a huge dark section holding one small
     dashed box (the empty state) — read as an abandoned wireframe.
   - Vertical rhythm too airy for the amount of live content (section padding to
     148px, tall min-heights) — "beautiful wireframe" feel.
   - Legacy plugins pollute every page: WooCommerce (4+ stylesheets + cart
     fragments) and pojo-accessibility (floating toolbar button + a font-resize
     CSS layer with rules up to `font-size: 266%`). Neither belongs to the portal.

## Shipped in this round (branch `claude/design-system-v1`)

- **Self-hosted variable webfonts** (SIL OFL): Noto Sans Hebrew 400–800 + Noto
  Serif Hebrew 400–600, Hebrew+Latin subsets, 4 woff2 files, ~80KB total, with
  `font-display: swap`, same-origin preloads, and license notices. The Google
  Fonts request and preconnects are removed. Verified in-browser: all four files
  load same-origin, zero third-party font requests.
- **Reviewed-guides empty state redesigned** into an editorial-standard panel:
  intro + the three real publication conditions (editorial approval, review date,
  visible source). Honest content — it describes the actual gate from
  `portal-template-helpers.php`; no fake counts or dates.
- **Rhythm tightening:** section padding `clamp(72px, 8.5vw, 118px)`, hero
  600px/76-90px, journal cards 330px.
- **Theme version 0.2.0** (style.css + constant) so `?ver=` cache-busts the CSS.
- **Contract test updated and strengthened**: the homepage design contract now
  asserts the self-hosted kit (4 valid woff2 binaries, both families declared)
  and **forbids** `fonts.googleapis.com` / `fonts.gstatic.com` in functions.php.

Verification: 21 PHP + 2 mjs contract tests pass; PHPCS + PHPStan clean;
pytest 19/19; render matrix 12/12 (200, one main, one h1); all three packages
rebuilt with fonts + GLBs inside; deploy dry-runs `dry_run_ok`.

Visual QA workflow established: headless Chrome CLI full-page captures
(desktop 1440 / mobile 390) — reliable where the browser-pane screenshot tool
times out on WebGL pages. Captures in the session scratchpad `design-qa/`.

## Owner decisions still open (floats)

1. **[High] Deactivate WooCommerce + pojo-accessibility** in wp-admin (2 min).
   Woo serves commerce CSS/JS sitewide with no shop; pojo duplicates
   accessibility the theme provides natively and floats a clashing blue button
   over the design. Both slow first paint on a YMYL property. wp-admin changes
   must then be recorded in `docs/agent-sync/` per AGENTS.md.
2. **[High] Site title/tagline** still the legacy "שירותי בריאות פרטיים בתיאום
   אישי - שירותי בריאות פרימיום" (browser tab + SERP brand). Decide the public
   brand line; set in wp-admin Settings → General.
3. **[Med] The 3D frozen-stack perf option** (preview LOD on homepage) remains
   parked per the freeze directive.
