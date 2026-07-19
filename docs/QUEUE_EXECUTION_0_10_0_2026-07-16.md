# Queue execution 0.10.0 — pharmacies layer + per-pin engagement analytics (2026-07-16)

Owner instruction: "execute queue" under the standing deploy authorization. Items pulled from the recorded queue (0.8.0 floats + control-center phase 2, analytics first as the client #2 sales proof).

## Shipped (theme 0.10.0, plugin 0.10.0)

### 1. Map data: pharmacies layer (queued float, unblocked)
Overpass export finally succeeded (previous two attempts rate-limited). `healthcare-poi-il.json` now carries **986 POIs: 106 hospitals + 524 clinics + 356 pharmacies** (named, IL-bounds-validated, deduplicated, ODbL attribution unchanged). Pharmacies render as distinct sand-toned dots with "בית מרקחת" popups; the status line names all three kinds.

### 2. Per-pin engagement analytics (phase 2 item #1)
The numbers the owner shows a prospective client, with a hard privacy boundary:

- **Store** (`class-hea-lth-metrics.php`): monthly aggregate counters only, keyed `type:key`. Allowlisted types (`pin_view`, `pin_click`, `wa_open`), opaque bounded keys, 300-keys/month cap, autoload off. **No IP, user agent, cookie, or user lookup anywhere in the class — contract-enforced** (test greps for and forbids `REMOTE_ADDR`, `$_SERVER`, `$_COOKIE`, `wp_get_current_user`, …).
- **Beacon**: `POST /wp-json/hea-lth-platform/v1/metric` (204 on success, 400 on anything off-allowlist).
- **Collection**: `engagement.js` exposes `heaLthMetricBeacon` (sendBeacon, fetch-keepalive fallback, silent failure) and counts WhatsApp bar opens per page path; `care-map.js` counts featured-pin popup views and popup action clicks via a stable `metricId` (`pin-` + md5 prefix of the name) the map registry now attaches to each featured provider.
- **Reporting**: control center → Monetization renders this month + last month: per-pin views/clicks (joined back to client names) and WhatsApp opens by page.
- Honest limits, by design: counters are best-effort and directional (no locking, public beacon), suitable for product decisions and client reporting, not billing.

## Verification
28 contract files green (new `metrics-contract-test.php`: sanitization, round-trip counting, monthly cap, privacy greps, JS hook presence), PHPCS + PHPStan exit 0, pytest 19/19, render matrix 200/1main/1H1, browser smoke: 986-POI status line renders, beacon function present. Packages 0.10.0 dry_run_ok. Live checks after deploy recorded below.

## Not executed from the queue (need owner input or a dedicated round)
- Client self-serve logins + billing (product decisions: pricing, payment rail).
- 3D mesh-granularity re-export (heavy asset pipeline round; freeze-adjacent).
- Products catalog CPT + Product JSON-LD (needs a real catalog decision first).

## Note for agents
Local PHPStan may crash its parallel worker at the default 1024M memory limit on this machine; re-run with `--memory-limit=2G`. CI is unaffected.
