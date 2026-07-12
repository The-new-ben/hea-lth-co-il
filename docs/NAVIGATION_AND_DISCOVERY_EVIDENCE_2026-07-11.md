# Navigation and discovery evidence, 2026-07-11

**Status:** Source implementation and automated interaction proof. Not a live
site change and not a substitute for Chrome visual or accessibility QA.

## Current research used for this slice

| Source | Signal used | Hea-lth decision |
| --- | --- | --- |
| [WordPress Theme Handbook, navigation menus](https://developer.wordpress.org/themes/classic-themes/functionality/navigation-menus/) | A classic theme should register menu locations and render them at the correct location. | Keep the theme menu locations registered. The presentation layer remains route-governed until real WordPress menu records and the migration plan are approved. |
| [Google helpful content guidance](https://developers.google.com/search/docs/fundamentals/creating-helpful-content) | Content should serve people rather than manipulate rankings. | Header labels describe visitor tasks in Hebrew. No SEO, lead, paid-placement, or internal operations vocabulary appears in public navigation. |
| [Clalit homepage](https://www.clalit.co.il/he/Pages/default.aspx) | Its public homepage exposes dense service access, care-provider discovery, digital-service paths, and editorial material. | Hea-lth provides a broad portal navigation model while remaining explicit that it is not a health-fund account or clinical-record system. |
| [RealSelf provider finder](https://www.realself.com/find) | Treatment, provider or practice search, location, and a documented verification proposition are central discovery inputs. | The route structure keeps treatment, directory, and professional paths prominent. Real verification status remains data-gated and is never manufactured in the theme. |

## Implemented source changes

1. Mobile navigation is removed from visual and keyboard navigation while it
   is closed. It receives `aria-hidden` and `inert` only in the mobile
   breakpoint, then those attributes are removed again on desktop.
2. Opening the mobile control exposes the navigation and moves focus to its
   first actionable item. Following a mobile link closes the menu.
3. The treatment and diagnostics mega menus can be opened by click or by
   `ArrowDown`. Keyboard focus moves into the revealed panel. `Escape` hides
   the panel and returns focus to its trigger.
4. A no-JavaScript navigation fallback retains the six high-value public
   routes: treatments, directory, diagnostics, wellness, guides, and
   professional onboarding.
5. No route is created ad hoc. All links resolve through the existing
   canonical or evidence-gated foundation registries.

## Automated proof

`tooling/tests/portal-navigation-contract-test.mjs` executes the real portal
navigation script against a small DOM fixture. It verifies closed and open
mobile state, focus movement, link-close behavior, keyboard mega-menu entry,
escape behavior, and removal of mobile-only attributes on a desktop change.

Source syntax, public-language checks, route-registry checks, and the complete
theme/plugin contract suite remain required before a packaging or release
review.

## Evidence still required

1. Chrome Profile 3 desktop and mobile screenshots of the current source.
2. Keyboard, touch, zoom, screen-reader, contrast, and reduced-motion checks
   in a real browser.
3. WordPress menu-record assignment and compatibility review against the
   approved legacy redirect map.
4. Live provider inventory and a verification methodology before real
   directory status, availability, reviews, or location details appear.
