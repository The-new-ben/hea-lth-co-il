# Visual direction and competitor gates

Status: source-design direction for the rebuild. This does not authorize a live theme change, publication, or a claim that the present local preview has passed visual approval.

## What the screenshots showed

The captured side-by-side screenshots are stored in `output/visual-regression/2026-07-11/`:

| Benchmark | What is genuinely useful | What Hea-lth must not copy | Hea-lth response |
| --- | --- | --- | --- |
| [Clalit](https://www.clalit.co.il/he/Pages/default.aspx) | Broad task discovery, dense service navigation, direct patient paths | Mixed visual hierarchy, clutter, and a utility-first visual language | A large portal information architecture with calmer hierarchy, clear task labels, and one prominent search-and-discovery surface. |
| [RealSelf](https://www.realself.com/) | Procedures, provider discovery, photos, reviews, Q&A, and visible explanation of trust | Unverified local equivalents, imported reviews, or its visual identity | An Israeli treatment, provider, guide, glossary, equipment, and care-navigation system. Every trust datum stays gated by a real record and stated methodology. |
| [Zygote Body](https://www.zygotebody.com/) | Immediate affordance for body exploration and layered anatomy | Embedding its model or presenting a placeholder as the required clinical experience | A self-hosted interaction runtime with an explicit license and clinical-release gate before any public 3D model is delivered. |

## Design correction for the next homepage slice

The current local screenshot establishes a real gap. The dark hero has enough structure, but the display headline is too large, the generic medical cross fails as a distinctive identity, and the orbital diagram does not explain a real visitor action quickly enough.

The next source slice changes those three things:

1. **Identity:** a restrained `H` monogram and wordmark replaces the generic medical cross. It is a working digital mark, not a substitute for the eventual approved master-brand vector.
2. **Task-first hero:** the illustration becomes an interactive care navigator with explicit paths for treatment, provider, testing, and equipment. It makes the public action visible without exposing invented doctors, prices, availability, or reviews.
3. **Reading hierarchy:** the Hebrew headline steps down in scale and gains usable line length. The decision surface becomes the visual focus, while the rest of the large homepage can carry its information architecture below the fold.

## Non-negotiable visual rules

- No generic AI-generated doctor imagery, invented testimonials, ratings, outcome photos, prices, or stock-looking clinical claims.
- No external 3D asset is shown before the model registry clears license, clinical, visual, GLB, and performance gates.
- Use a clear, keyboard-visible focus state; do not hide important information behind an overlay. This follows the NHS guidance to make key tasks visible and avoid blocking overlays. [NHS England guidance](https://www.england.nhs.uk/long-read/creating-a-highly-usable-and-accessible-gp-website-for-patients/)
- Provider marketplace value must be visible before asking for personal information. Trust, matching, and accessibility are core marketplace concerns. [Sharetribe marketplace principles](https://www.sharetribe.com/academy/online-marketplace-design-principles/)
- Copy must use the visitor's task language and direct them to a stable, approved route. It must never expose SEO or monetization vocabulary to the public.

## Screenshot gate for every visual slice

1. Capture the local source preview in Chrome at desktop and mobile widths.
2. Capture the relevant competitor source in the same session where technically possible.
3. Produce a labelled side-by-side image with viewport, date, and whether the Hea-lth source is preview-only or live.
4. Record visual gaps, not only wins.
5. Do not call a slice complete until the source, accessibility checks, and screenshot evidence agree.

## Freshness note, 2026-07-11

The saved comparison images are a pre-correction baseline. The homepage no
longer draws mock map roads or provider pins. It now presents a transparent
map-release message until real, display-approved locations and a restricted
map configuration are supplied. The next Chrome capture must show this exact
state before any visual claim is renewed.

## Required before brand-final release

- Approve an original master logo in vector formats with ownership and usage terms.
- Verify Hebrew webfont licensing and self-host selected production font files after performance review.
- Capture desktop, tablet, and mobile visual QA against the approved Figma file.
- Confirm contrast, keyboard sequence, menu behavior, and reduced-motion behavior.
