# Body-map index live — release cd29086 (theme 0.7.1, plugin 0.7.0)

The owner's vision shipped and live-verified: the model and the map are one
instrument. The map sits directly under the body (homepage hero + /anatomy/),
always rich, location-aware; body selections drive it; portal clients stand
out with disclosed premium pins.

Live verification chain (production):
- Map attached under the model (Leaflet vendored, OSM tiles loading).
- Rich layer: 630 real IL hospitals+clinics (OSM/ODbL attributed, 49KB baked
  dataset) + "סביבי" geolocation.
- Client #1: Dr. Keren Chaya Cohen (plastic & reconstructive, Ramat Aviv
  Medical Center) — premium H-pin; verified popup shows badge, specialty,
  address, call/site actions, and the commercial disclosure.
- Body→map: chest selection flew the map to the client and opened the popup
  (structure→region translation, same table as the selection card).
- Rotation locked horizontal-only (owner directive; documented 2-line freeze
  exception). Hand (54)/foot (52)/nose (7) now clickable — 37 structures.

Governance: map gate EXTENDED, not weakened — keyless leaflet-osm provider
requires the same origin + three reviews; tile source fixed in code; featured
providers sanitized (IL bounds, https-only, disclosure mandatory); google-key
path untouched. New contract test covers approve/reject paths (default
manifest approved; placeholder google key rejected; foreign origin rejected).

Same-day catches (QA law: verify the visible outcome):
1. Harness fatal — trailingslashit missing in preview mocks truncated the
   page below the hero; fixed by passing the full POI URL.
2. Direct model clicks emit structure ids, not resolver regions — the map
   spotlight needed the same translation table as the card (0.7.1).

Growth paths: more clients = JSON entries in the default map manifest (same
gate); richer layers = MoH GIS open data (licensed) instead of/alongside OSM;
products per part = store Phase A.
