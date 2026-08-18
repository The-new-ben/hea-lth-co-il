---
name: hea-lth-visual-qa-pipeline
description: "How to capture reliable design screenshots on this PC, and two known capture artifacts that mimic site bugs"
metadata: 
  node_type: memory
  type: project
  originSessionId: 7ccc047b-ff12-4ac2-971a-b814129a3007
---

Reliable visual QA on this PC: headless Chrome CLI — `"/c/Program Files/Google/Chrome/Application/chrome.exe" --headless=new --disable-gpu --hide-scrollbars --window-size=W,H --screenshot=out.png URL`. Full-page captures at any viewport, works when the in-app browser pane's screenshot tool times out (it hangs whenever a live WebGL context exists on the page; killing the context via `WEBGL_lose_context` sometimes unblocks one shot).

**Two artifacts that look like site bugs but are not:**
1. Pane resize without reload does not re-evaluate all media queries — computed styles can show stale mobile values (e.g. hero h1 42.4px at 1440px width). Always reload after `resize_window` before measuring.
2. Headless full-page captures of RTL pages show a phantom right-edge clip (scrollbar-side offset) at mobile widths. The real check is in-pane: `document.scrollingElement.scrollWidth === innerWidth`. Verified 2026-07-12: the live site has zero overflow at 390px; do not chase the clip.

**Why:** Both artifacts cost real diagnosis time and nearly caused a false "mobile is broken" fix.
**How to apply:** Headless CLI for pixels, in-pane JS for measurements; never trust a single capture path for a layout verdict.

**Additions (2026-07-15):** (3) Mobile headless captures work clean at **660px width** (still inside the ≤680 mobile breakpoint) — use 660, never 390, for mobile screenshots. (4) **Visibility QA must assert computed style/paint, not DOM properties** — the a11y panel's `display:grid` silently defeated the `hidden` attribute and the panel was visibly stuck open sitewide while `panel.hidden` toggled true/false correctly (fixed in theme 0.5.2 by `.hp-a11y__panel[hidden]{display:none}`). (5) WebGL never renders in headless (no GPU) — a dark empty stage in captures is an artifact, verify the viewer in-pane instead. Related: [[hea-lth-project-core]], [[hea-lth-3d-code-freeze]].

**Additions (2026-07-16):** (6) The in-app browser pane runs with `document.visibilityState === 'hidden'`, so **requestAnimationFrame never ticks there** — Leaflet `flyTo`/zoom animations and the viewer's selection pulse freeze forever and DOM assertions after them read stale (looked exactly like "flyTo does nothing"). Animation QA needs a visible tab: use claude-in-chrome (real Chrome) against the local harness. (7) For the harness, serve the repo root (`php -S 127.0.0.1:PORT -t .` + `/tooling/theme-preview/index.php?page=...`), never `php -S ... tooling/theme-preview/index.php` — router mode swallows every static asset URL and returns the HTML page for JS/JSON requests (all responses same byte size is the tell). Also: stale `php.exe` listeners survive `taskkill //IM` from Git Bash; kill via PowerShell `Stop-Process -Name php -Force` and re-check `netstat` before trusting a port. (8) The harness mirrors production `window.heaLthAnatomyRoutes` in TWO hardcoded arrays inside `tooling/theme-preview/index.php` (anatomy + home blocks) — any new route key added to `hea_lth_portal_anatomy_route_map()` must be added there too, or resolver entries silently drop their links only in local QA.
