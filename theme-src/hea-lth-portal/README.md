# Hea-lth Portal theme

This is the clean, standalone source theme for the Hea-lth portal rebuild. It does not modify the legacy theme, change the active UPress theme, migrate content, create redirects, or send data to a third party.

## What this source owns

- Visual system, header, footer, page templates, responsive behavior, and editor tokens.
- Public presentation of pages, guides, treatment hubs, directory paths, professional pages, account states, and the anatomy discovery shell.
- Accessible text alternatives for every interactive portal path.

## What must remain outside the theme

- Provider records, credential verification, ranking logic, reviews, consent, analytics, payments, product catalog, map data, and commercial operations.
- Medical claims, author and reviewer records, source ledgers, and publication approval.
- Redirects and old URL disposition.

## 3D human gate

The anatomy screen includes a semantic discovery controller, an accessible fallback, and a self-hosted Three.js renderer that is loaded only after the anatomy-model registry releases an approved public configuration. A real model can only be connected after written rights for web delivery, clinical review, model inspection, performance testing, and accessibility verification. The contract for that process lives in `design-lab/3d-human-engine/` outside this theme. The renderer has no production human asset in this repository.

The renderer's vendored dependencies are documented in `assets/vendor/three/THIRD_PARTY_NOTICES.md`. The local technical GLB used during proof work is deliberately outside this theme and cannot be shipped by the theme.

## Release boundary

Before this theme can be packaged or activated, it needs a WordPress visual review, RTL and mobile screenshots, an accessibility pass, content and provider data ownership, a URL migration map, an inquiry-handling proof, and an approved rollback plan. Do not point the deploy pipeline at this directory until those gates are signed off.
