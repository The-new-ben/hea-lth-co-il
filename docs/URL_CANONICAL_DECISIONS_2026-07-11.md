# Canonical route decisions, homepage foundation

Status: implementation decision for the new theme source. No redirect, publish, deletion, or live WordPress change is authorized by this file.

## Why this exists

The new portal has to preserve powered URLs while it grows. A premium homepage cannot point a visitor at a convenient but unverified route, particularly where a commercial keyword already has an existing page. Each high-intent link below is tied to the initial keyword map and the current public inventory.

## Approved routes used by the new theme

| User intent | Canonical route in the new theme | Source of truth status | Reason |
| --- | --- | --- | --- |
| רפואה אסתטית | `/aesthetic-medicine-treatments/` | existing, keep-improve | This is the recorded live pillar for the commercial-local intent. |
| ניתוחים פלסטיים | `/plastic-surgery-consultation/` | existing, keep-improve | This is the recorded live pillar for the broad commercial-local intent. |
| השתלת שיער | `/hair-transplant-consultation/` | existing, keep-improve | This is the recorded live pillar for the mixed-commercial intent. |
| ניתוח אף from a discovery surface | `/plastic-surgery-consultation/` | existing fallback | The planned `/nose-surgery/` URL is a `new` URL with decision `hold`; it must not be exposed until its audit and migration gates pass. |
| רופאים ומרפאות | `/doctor-clinic-index/` | existing, keep-improve | This is the recorded directory hub. |

## Routes deliberately not exposed as final pillars

| Route | Decision | Guardrail |
| --- | --- | --- |
| `/nose-surgery/` | Hold | Do not create, link as a final canonical target, or request indexing until legacy audit, Hebrew SERP comparison, medical review, internal-link plan, and redirect decision are approved. |
| `/plastic-surgery/rhinoplasty/` | Do not use in the new source | It was a source-level invented nested path, not the approved target in the keyword map. |
| `/hair-and-scalp/` | Do not use for the השתלת שיער money-intent link | It was a source-level broad path, not the approved existing pillar in the map. |

## Implementation rule

High-intent template links call `hea_lth_portal_route( $key )` from `inc/portal-route-registry.php`. The registry only contains approved existing pages. An unknown key returns the homepage in production and issues a development warning in `WP_DEBUG`; that makes an accidental new route visible without placing visitors into a dead or competing URL.

Directory filter states, including anatomy-derived `body_region` parameters, are user-interface states rather than new landing pages. The directory template sets `noindex, follow` whenever one of its controlled filters is present, so `/doctor-clinic-index/` retains canonical crawl ownership.

## Evidence to refresh before release

1. Export Search Console landing-page and query data for all listed paths.
2. Crawl the live URL inventory, including canonical tags, response codes, and internal-link sources.
3. Re-run Hebrew Google results for each primary keyword from an Israeli location.
4. Validate each target against the planned keyword map and a medical/editorial review record.
5. Only then decide redirects, canonical tags, and publication order.
