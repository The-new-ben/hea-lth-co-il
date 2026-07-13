# Round 3 status + benchmark delta (2026-07-13)

Live release **438d793**. Everything below is verified against the live site or
a rendered capture, not asserted.

## Shipped and live this round

1. **Interactive 3D body is above the fold.** The homepage hero is now the
   interactive body when a model is approved: 3D viewer + region/context
   controls + service result cards. Verified live: `hp-hero--anatomy` present,
   canvas 1259×959 rendering (137 bone-white sample points → model visible),
   click "nose & breathing" → contexts → real service links
   (`/plastic-surgery-consultation/`, `/doctor-clinic-index/?specialty=…`).
2. **Sitewide 404s fixed** (prior round, verified again): the 7 foundation pages
   (anatomy, guides, glossary, find-care, health-technology, professionals,
   treatments) all return 200. The 3D "full experience" (/anatomy/) no longer
   404s.
3. **Brand favicon** now served from the theme; the legacy 2024
   `cropped-health-online` site icon is overridden. Verified: the live `<link
   rel="icon">` tags point at the theme brand mark.
4. **SEO layer**: Organization + WebSite JSON-LD (with SearchAction) and Open
   Graph / Twitter meta. Verified live (2 JSON-LD blocks, og:title, SearchAction).

## Benchmark delta (see COMPETITIVE_BENCHMARK_2026-07-13.md for evidence)

| Dimension | Leaders | Before today | After today |
|---|---|---|---|
| 3D placement | Above fold, dominant | Mid-page, small | **Above the fold, hero** ✓ |
| Click → services | BioDigital: part → conditions | Anatomy page only | **On the homepage hero** ✓ |
| Portal pages | Deep IA | Many 404 | **7 pages live (200)** ✓ |
| Favicon | Brand | Legacy 2024 icon | **Brand mark** ✓ |
| SEO schema | Rich JSON-LD | Minimal | **Org + WebSite + OG** ✓ |
| 3D realism | Real body w/ skin | Skeleton | **Still skeleton** — muscle figure exported, next |
| Imagery | Real photos everywhere | None | **Still none** — needs licensing decision |
| Menu depth | Diseases/drugs/procedures/calculators | Thin | Not yet expanded |

## The "real body, not skeleton" — status

Exported the Z-Anatomy **muscular (écorché) full-body figure** through the
proven pipeline: detail 4.9 MB / preview 1.96 MB GLB, complete human form
(head, torso, arms, hands with fingers, legs, feet) — rendered and confirmed.
It is NOT live yet: swapping requires gate-safe mesh-name normalization, a new
structure map for click-to-identify, flesh material + lighting tuning, LOD size
optimization, and gate verification. Doing it half-way (broken clicks, heavy
homepage, gate rejection) would be worse than the current working skeleton, so
it is the next dedicated, verified step — not a rushed swap. Assets staged in
`tmp/z-anatomy/muscular-out/` (gitignored source).

## Owner questions answered

- **"What is the blue accessibility icon?"** The WordPress plugin
  **pojo-accessibility** (installed on the old site, still active) renders its
  own floating toolbar button on every page. The theme does not ship it.
- **"Favicon not connected"** — fixed (brand mark live).
- **"3D only on front page and low / full experience 404"** — 3D is now above
  the fold on the homepage AND on /anatomy/ (which no longer 404s).
- **"Click a part → services/products"** — built and verified on the homepage
  hero; the full region→context→services + verified-map lives on /anatomy/.

## Owner actions still needed (wp-admin, ~5 min)

1. **Deactivate WooCommerce** (loads commerce CSS/JS sitewide, no shop) and
   **pojo-accessibility** (the blue button; theme a11y is native).
2. **Site title/tagline** still the legacy "שירותי בריאות פרימיום" — set the
   real brand line in Settings → General.
   (Both must then be recorded in docs/agent-sync/ per AGENTS.md.)

## Next build priorities

1. **Real-body muscle asset** live on the homepage (task queued, asset ready).
2. **Imagery system** — needs an owner licensing call (stock vs. commissioned);
   until then, no fake photos of fake people.
3. **Mega-menu depth** — expand to the real taxonomy now that routes resolve.
4. **Keyword/SEO** — the hard money-keyword plan from the existing SEO master
   plan, now that pages exist to target them.
