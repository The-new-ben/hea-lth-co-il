# Hea-lth Portal: full agent handoff

Last updated: 2026-07-12, Asia/Jerusalem

This is the current source-of-truth handoff for a new agent joining the Hea-lth project. Read it fully before changing code, content, WordPress, GitHub, SEO strategy, design, tracking, or provider data.

> **Verified release snapshot, 2026-07-12.** The from-scratch `hea-lth-portal-child` theme is now active on the public homepage. The latest production run is [GitHub Actions run 29167917145](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167917145), completed successfully at commit [`9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c`](https://github.com/The-new-ben/hea-lth-co-il/commit/9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c). A fresh public request to `https://hea-lth.co.il/` returned HTTP 200, the parent and child asset markers, one H1, the portal mega-menu marker, no Hello Elementor marker, and no PHP fatal/parse/critical-error marker. Statements below that describe the child activation as blocked are historical and are superseded by this snapshot and the release chronology in section 3.

For a complete first-party source inventory with file-level responsibilities and GitHub links, read [PORTAL_SOURCE_FILE_MAP_2026-07-12.md](PORTAL_SOURCE_FILE_MAP_2026-07-12.md) after this document. It deliberately lists source and vendor boundaries without copying credentials or generated release ZIPs into documentation.

## 1. Mission and non-negotiable product standard

Hea-lth is not a small lead-generation landing page. The intended business is an Israeli premium-health platform that can later expand internationally. It must help consumers discover, understand, compare, and safely progress toward care while creating a high-quality commercial and operational platform for verified professionals, clinics, hospitals, technologies, equipment suppliers, and premium services.

The commercial pyramid is intentional:

1. High-value conversion journeys at the top: plastic surgery, aesthetic medicine, hair restoration, premium diagnostics, private specialists, second opinions, wellness, premium health services, eligible medical technology, and later regulated commerce or referral journeys.
2. Broad high-trust discovery beneath it: treatments, specialties, clinical concepts, glossary entries, guides, news, research explainers, technologies, equipment, provider discovery, and location-aware discovery.
3. Enterprise operations underneath: provider onboarding, verification, availability, lead qualification, consent, routing, CRM integration, attribution, revenue reconciliation, editorial review, medical review, analytics, compliance, and release controls.

The target is to become a trusted, premium, information-rich portal rather than a thin SEO shell. Do not describe the project as "done", "best", or "ahead of competitors" without dated comparative evidence.

## 2. Product, language, and public-copy rules

- Primary market and language: Israel, Hebrew, RTL.
- Primary audiences: people researching a medical condition, procedure, physician, clinic, diagnostic service, technology, health equipment, or premium wellness service; verified professionals and clinics considering participation; future commerce and operations users.
- Public pages must speak to people, not to search engines. Never expose internal phrases such as "money page", "SEO intent", "cluster", "conversion funnel", "lead routing", or "pillar" in visitor-facing copy.
- Do not use generic AI wording, empty superlatives, fake testimonials, invented physician claims, invented prices, fake availability, placeholder providers, or pseudo-medical authority.
- Do not use em dashes or en dashes in public copy. Prefer normal Hebrew punctuation and short, natural sentences.
- Medical content must be evidence-gated. Do not write or publish bulk long-form medical articles locally. The owner explicitly wants full articles generated through their real ChatGPT Pro Extended research workflow after SERP, competitor, source, author, reviewer, and URL governance are ready.
- Do not create URLs casually. Existing SEO equity must be preserved until the migration map, redirect plan, source content equivalence, and monitoring plan are approved.
- Do not upload the Happy Davinci or CesiumMan GLB asset as an "ultra realistic" body model. The current 3D engine is real, but the asset is not approved for clinical, visual, or licensing release.

## 3. Current business and delivery reality

### What is live and proven

| Area | State | Evidence as of 2026-07-12 |
| --- | --- | --- |
| Public portal homepage | Live | `https://hea-lth.co.il/` returned HTTP 200 on a fresh normal URL request. The response contained both `/wp-content/themes/hea-lth-portal/` and `/wp-content/themes/hea-lth-portal-child/style.css`, contained `hp-mega`, contained exactly one H1, and did not contain `hello-elementor`, `Fatal error`, `Parse error`, or `There has been a critical error`. |
| Active production child theme | Live and release-verified | `hea-lth-portal-child` v0.1.0 was activated through the governed pipeline. Public child health route returned `status: ok` for deployment `gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1-child`. |
| Parent theme | Live as the active child theme's parent | `hea-lth-portal` v0.1.0 was installed and independently verified through authenticated WordPress Themes REST before child activation. |
| Platform plugin | Live and release-verified | `hea-lth-platform-core` v0.1.0 public health route returned `status: ok` for deployment `gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1`. |
| GitHub to WordPress delivery | Validated and live | [Run 29167917145](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167917145) completed successfully after validation, deterministic package builds, governed deployment, health checks, cache purge, and full public-HTML marker verification. |
| Cache release gate | Validated and live | The pipeline now sends authenticated `DELETE /wp-json/ezcache/v1/cache` after a release, then retains and verifies the complete public HTML response rather than a truncated fragment. |
| Temporary deployment bridge cleanup | Proven | The ephemeral Code Snippets route is deleted in a `finally` path and checked for absence after each release. |

### What exists but is not yet operational or approved

| Area | State | What this actually means |
| --- | --- | --- |
| Full design approval | Evidence-gated | A real WordPress portal theme is now live, but there is no approved final Figma library, high-end logo system, design review record, or Chrome Profile 3 screenshot pack. Figma Starter write capacity was exhausted at the last check. |
| SEO program | Foundation and evidence-gated | Research documents and a seed keyword map exist locally, but not a comprehensive Israeli keyword database, dated SERP pack, approved keyword-to-canonical-URL map, redirect map, content calendar, or production monitoring dashboard. |
| Lead routing and monetization | Foundation | The plugin establishes data and routing boundaries only. There is no live consent-first intake, CRM integration, SLA, attribution, billing, real provider capacity inventory, or routing automation. The historic lead-loss problem is therefore not solved. |
| Verified provider marketplace | Foundation | Custom content types and guarded public templates exist. There are no published verified provider, clinic, hospital, equipment, review, availability, contract, or commercial inventory records. |
| Commerce | Not started | There is no product catalog, regulated-product review process, checkout, payment, fulfillment, refund, or referral reconciliation implementation. |
| 3D human body experience | Engine foundation, asset-gated | Three.js, model-stage, rotation/layer/mesh-selection code, accessible controls, a discovery resolver, and map boundary exist. No licensed, clinically reviewed, quality-validated public anatomy GLB/GLTF manifest has passed the gate, so real WebGL is deliberately not sent to visitors. The homepage currently has a non-WebGL teaser, not a real rotating body. |
| Analytics and experimentation | Not started or unknown | GSC access was shown by the owner, but no repository evidence proves a GSC export, GA4 access, event taxonomy, warehouse, conversion reporting, dashboards, or CRM attribution. |

### Release chronology and why the final state is trustworthy

| Milestone | Result | Evidence |
| --- | --- | --- |
| `c3defd8` initial portal/pipeline release | Partial success | The plugin and parent theme proved WordPress identity, filesystem access, and deployment architecture. The child preflight safely refused PHP 8.1 on a PHP 7.4.33 host. |
| `e847a47` compatibility and quality correction | Source corrected | Child deployment manifest changed to PHP 7.4 and portal-wide static checks were expanded. |
| `29167134652` CI memory failure | Contained | PHPStan exceeded the former 512 MB CI limit before production deployment. No live package was released by that failed run. |
| `e2f291f` CI correction | Source corrected | PHPStan memory allocated at 1 GB without reducing the portal scan. |
| `29167190760` first successful child activation | Live but cache-masked | The child theme activated successfully. Public cache still served older HTML at the bare URL. |
| `e7c3cab` cache release control | Source corrected | Cache purge and public-theme marker verification were added. |
| `29167751632` incomplete public verification | Contained | Public verification incorrectly read only the first 4,000 characters. The run detected incomplete proof and rolled the child update back. It did not falsely claim success. |
| `9d42872` final verification correction | Source corrected | Successful HTML responses are now kept in full; only error payload text is truncated for safe diagnostics. |
| `29167917145` final production release | Successful and current | Child theme and plugin health routes returned expected release IDs; cache purge and full HTML marker verification passed. |

### Enterprise scorecard at handoff

This is a maturity assessment, not a percentage-complete claim. It keeps a successful theme release from being mistaken for a completed business.

| Dimension | State | Evidence and gap |
| --- | --- | --- |
| Business strategy and economics | Foundation | Commercial pyramid and intended revenue models are documented, but no approved offer, pricing, contracts, or unit economics exist. |
| Information architecture and taxonomy | In progress | Portal routes, templates, entities, mega menus, and discovery areas are implemented; formal migration and taxonomy governance remain incomplete. |
| SEO evidence and governance | Evidence-gated | Seed documents exist locally, but no full GSC/SEMrush export, dated Israeli SERP database, final canonical/redirect map, or production measurement is proven. |
| Experience and design system | Evidence-gated | Token-driven portal theme is live; final Figma library, brand/logo approval, comparative design evidence, and Chrome screenshot acceptance are missing. |
| Technical platform and delivery | Live | Versioned source, CI, deterministic packages, governed WordPress release, health routes, rollback/cleanup behavior, cache purge, and public marker verification are proven. |
| Medical content operation | Foundation | Template safety gates exist, but author/reviewer roster, evidence workflow, content calendar, and published reviewed corpus do not. |
| Monetization and lead operations | Foundation | Data boundary exists; consent capture, CRM, routing, attribution, SLA, billing, and reconciliation do not. |
| Provider marketplace | Foundation | Entities and templates exist; credentialed providers, availability, agreements, reviews, and operations do not. |
| Commerce and premium products | Not started | No regulated catalog, checkout/referral, fulfillment, returns, or payment operations. |
| Product intelligence and advanced interfaces | Foundation | Discovery architecture and gated 3D/map runtime exist; approved model, inventory, evaluation, and safety proof do not. |
| Data, analytics, and experimentation | Not started or unknown | No confirmed event taxonomy, GA4/GSC export pipeline, dashboard, data warehouse, or experiment framework. |
| Trust, legal, privacy, and clinical safety | Foundation | Conservative defaults prevent fake public claims/data; formal policies, consent design, reviewer ownership, disclosures, and legal review remain required. |
| Operating model and roadmap | Foundation | Handoff, research material, release proof, and priorities exist; named cross-functional owners, decision log, budget, and operating cadence are not yet confirmed. |

## 4. Active red alerts

1. **Lead routing is not operational.**
   - Impact: the original business leak, unowned or incorrectly routed leads, is not yet solved by the new visual portal.
   - Containment: no live health-data intake or routing is pretending to work. The current code does not collect leads or sensitive health data.
   - Required proof: consent-first intake design, CRM selection and data-processing review, routing matrix, provider eligibility/capacity, fallback owner, SLA, audit trail, attribution, and end-to-end test cases.

2. **Organic migration is not ready.**
   - Impact: historical URLs may hold visibility and backlink equity.
   - Containment: the theme uses controlled route keys and does not bulk-remove URLs or make a blanket redirect/noindex decision.
   - Required proof: crawl, GSC page/query export, backlink inspection, canonical and redirect decision log, content-equivalence checks, and post-launch monitoring.

3. **The ultra-realistic 3D anatomy asset is not approved.**
   - Impact: a real rotatable human model cannot responsibly be placed on the homepage yet.
   - Containment: the site renders an honest discovery teaser and an accessible non-3D resolver. It does not show a generic CesiumMan or Happy Davinci placeholder as medical anatomy.
   - Required proof: explicit license and derivative/web delivery rights, named clinical reviewer and date, GLB validation, visual/performance QA, semantic mesh IDs, high-detail LOD of at least 100,000 triangles, and same-origin public asset path.

4. **Medical content cannot be mass-produced yet.**
   - Impact: publishing unsupported long-form health material risks YMYL quality, trust, and cannibalization.
   - Containment: no bulk 3,000 to 5,000 word articles were written or published by the local agent.
   - Required proof: Israeli SERP evidence, competitor evidence, one primary intent per canonical URL, source/reviewer ownership, medical review, revision dates, and an approved ChatGPT Pro Extended research-to-draft workflow.

5. **Chrome visual evidence is incomplete.**
   - Impact: the release has functional/public-HTML proof but not yet the owner-required desktop/mobile Chrome Profile 3 screenshot evidence.
   - Evidence: the physical Chrome window was found, but the desktop capture interface failed with `SetIsBorderRequired failed: No such interface supported (0x80004002)`. The Chrome extension was also unavailable.
   - Required proof: capture the live homepage, a mega menu, and an internal template in actual Chrome Profile 3 after the capture interface is healthy. Do not substitute a synthetic image as evidence.

## 5. URLs, GitHub, WordPress, and environments

### Production endpoints

| Purpose | URL or identifier | Notes |
| --- | --- | --- |
| Public site | `https://hea-lth.co.il/` | New `hea-lth-portal-child` is active and public. The normal URL was verified with parent and child asset markers after cache purge. |
| WordPress REST root | `https://hea-lth.co.il/wp-json/` | Read-only inspection verified it is reachable. |
| Core plugins REST route | `/wp-json/wp/v2/plugins` | Available; requires authenticated WordPress deployment identity for mutation. |
| Core themes REST route | `/wp-json/wp/v2/themes` | Available; used for authenticated inactive-parent verification. |
| Platform health | `/wp-json/hea-lth-platform/v1/healthcheck` | Safe public release identity only. Current proof: [`gh-...29167917145-1`](https://hea-lth.co.il/wp-json/hea-lth-platform/v1/healthcheck?deployment=gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1). No provider, lead, medical, or credential data. |
| Child theme health | `/wp-json/hea-lth-portal/v1/healthcheck` | The active child theme exposes safe release identity. Current proof: [`gh-...29167917145-1-child`](https://hea-lth.co.il/wp-json/hea-lth-portal/v1/healthcheck?deployment=gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1-child). |
| UPress account | `https://my.upress.co.il/account/websites/hea-lth.co.il` | Owner controls hosting. Do not use manual ZIP upload as a substitute for the governed deploy pipeline. |

### Repository

| Item | Value |
| --- | --- |
| Local working directory | `C:\Users\pro\Documents\websites\hea-lth-co-il` |
| Git remote | `https://github.com/The-new-ben/hea-lth-co-il.git` |
| Production branch | `main` |
| Current development branch | `codex/portal-production` |
| Latest pushed production commit at handoff | [`9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c`](https://github.com/The-new-ben/hea-lth-co-il/commit/9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c) |
| First portal deployment run | `https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29166607077` |
| First portal deployment result | Failed only at child-theme PHP compatibility preflight. Plugin and parent steps succeeded. |
| Second portal CI run | [`29167134652`](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167134652) stopped before production deployment when PHPStan exhausted the workflow's 512 MB limit. |
| Current successful production run | [`29167917145`](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167917145) completed successfully with public child-theme verification. |

### GitHub access and credentials

Never print, paste, commit, screenshot, or request raw secret values in chat, shell output, logs, docs, or source control.

| System | Current known access | What the next agent may do |
| --- | --- | --- |
| GitHub CLI | Authenticated as `The-new-ben`; usable scopes included `repo` and `workflow` | Read repository, push approved source, inspect workflow runs and secrets by name only. |
| GitHub environment | `production` exists | It has `WP_BASE_URL` and `WP_USER` environment secrets. |
| Repository secrets | `WP_BASE_URL` and `WP_APP_PASSWORD` were listed by name | Workflow currently resolves the values. Do not expose or rotate them casually. Recommended later hardening: move `WP_APP_PASSWORD` into the `production` environment and remove duplicate repository-level secrets only after a successful verified release. |
| WordPress deployment identity | Authenticated in GitHub Action as WordPress user ID `1` | It has successfully completed plugin installation/update, parent-theme install/update, child-theme install/update, and child-theme activation. The bridge will report exact missing capabilities if a future host change removes them. |
| WordPress Application Password | Exists in GitHub secret storage | Never retrieve into local command output. Use the GitHub workflow. |
| Chrome | User asked for Chrome Profile 3 | The Codex Chrome extension was unavailable at the last check. Do not substitute the in-app browser when the owner specifically requests Chrome state. Ask the owner to attach a logged-in Profile 3 tab if a browser-session task requires it. |

### Secrets handling rule

If an agent needs a missing secret or permission, use this exact format and stop only at the required owner action:

```text
BLOCKER: <system and failed gate>
IMPACT: <what is not live>
NEEDED: <role, secret name, or hosting setting>
OWNER ACTION: <precise navigation or one safe action>
VERIFY: <read-only proof after it is resolved>
```

Do not bypass a missing secret by publishing a package, adding a permanent backdoor, disabling checksum checks, storing credentials in a file, or doing a manual unverified upload.

## 6. Deployment pipeline architecture

The project now uses a checksum-verified GitHub-to-WordPress pipeline, not FTP or ad hoc ZIP uploads.

### Source files

| File or directory | Responsibility |
| --- | --- |
| `deploy/wordpress-deploy.json` | Package inventory, slugs, source directories, version parsing, activation intent, health routes, and manifest compatibility values. |
| `scripts/build-wordpress-package.py` | Deterministic ZIP builder. Enforces one top-level slug directory, version identity, normalized timestamps, manifest, SHA-256, and safe paths. |
| `scripts/deploy-wordpress.py` | Authenticated WordPress REST orchestrator. Builds temporary bridge, preflights, uploads, verifies checksum, rolls back on failed gates, verifies health, finalizes, deletes the temporary route, and proves cleanup. |
| `deploy/agentdeploy-route.php` | Temporary Code Snippets payload. It is generated per deployment with a high-entropy one-time token and a package allowlist. It is not a permanent public API. |
| `.github/workflows/wordpress-deploy.yml` | GitHub Actions validation and main-branch deployment workflow. |
| `tests/test_wordpress_pipeline.py` | Unit tests for route rendering, basic-auth transport handling, release identity validation, rollback, theme verification, and package configuration. |

### Packages and deployment order

1. `hea-lth-platform-core` plugin
   - Source: `plugin-src/hea-lth-platform-core`
   - Main file: `hea-lth-platform-core.php`
   - Active after deployment.
   - Health: `/wp-json/hea-lth-platform/v1/healthcheck`.

2. `hea-lth-portal` parent theme
   - Source: `theme-src/hea-lth-portal`
   - Installed before the child theme.
   - Intended to remain inactive. It is independently verified through authenticated `GET /wp-json/wp/v2/themes/hea-lth-portal?context=edit`.

3. `hea-lth-portal-child` child theme
   - Source: `theme-src/hea-lth-portal-child`
   - WordPress `Template: hea-lth-portal` relationship.
   - This is the deliberate production activation point.
   - Health after activation: `/wp-json/hea-lth-portal/v1/healthcheck`.

### Safety behavior

- Every package builds twice with matching SHA-256 before deployment.
- The pipeline runs secret scanning, Python compilation, PHP lint, PHPCS security and PHP compatibility checks, PHPStan, npm and Composer audits, workflow lint, dry run, package validation, and artifacts.
- The bridge requires WordPress authentication and its ephemeral token.
- The bridge checks the package kind, allowlisted slug, upload size, direct filesystem access, WordPress capability, SHA-256, version, and replay-safe deployment ID.
- Existing package files are copied to an isolated WordPress backup directory before overwrite.
- Plugin and theme activation failures restore the prior package and, for a theme change, switch back to the recorded previous stylesheet.
- The child theme is the only activation-authorized theme slug in the generated route. This is deliberate because the owner expressly authorized live activation of the new portal theme.
- A theme package with an empty health route is verified by authenticated WordPress Themes REST, not merely by installer output.
- The active child theme has a public health route that confirms component slug, version, and persisted release deployment ID.
- `cache_purge_path` is configured as `/wp-json/ezcache/v1/cache`. The deploy client calls it with authenticated `DELETE` and then checks public theme asset markers before it finalizes the child release.
- The temp Code Snippets bridge is deleted in `finally` and the route is rechecked for 404.

### Important pipeline status at handoff

- GitHub run [`29166607077`](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29166607077) first proved the deploy identity and host filesystem work. It safely stopped the child when PHP 8.1 conflicted with the PHP 7.4.33 host.
- Commit [`e847a47`](https://github.com/The-new-ben/hea-lth-co-il/commit/e847a47) set the child requirement to PHP 7.4 and expanded portal-wide PHPCS/PHPStan coverage.
- GitHub run [`29167134652`](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167134652) exposed the 512 MB PHPStan limit before any production deployment. Commit [`e2f291f`](https://github.com/The-new-ben/hea-lth-co-il/commit/e2f291f) raised CI to `--memory-limit=1G` without shrinking the scan.
- GitHub run [`29167190760`](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167190760) successfully installed and activated the child theme. It exposed the independent public-cache issue rather than a WordPress/theme defect.
- Commit [`e7c3cab`](https://github.com/The-new-ben/hea-lth-co-il/commit/e7c3cab) made cache purge and public asset markers mandatory. Commit [`9d42872`](https://github.com/The-new-ben/hea-lth-co-il/commit/9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c) corrected verification to inspect full successful HTML.
- GitHub run [`29167917145`](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167917145) is the current successful production release. Do not re-run old commits merely to reproduce history.

## 7. New WordPress implementation

### Parent theme: `theme-src/hea-lth-portal`

The parent theme is a from-scratch presentation system. It is not a patch on the old site.

Main parts:

| Path | Purpose |
| --- | --- |
| `style.css` | Theme metadata, version `0.1.0`, WordPress compatibility declaration. |
| `theme.json` | Design tokens and editor-level foundation. |
| `functions.php` | Theme setup, assets, safe anatomy and map config filters, SEO-safe output boundaries. |
| `header.php` | RTL premium header, primary navigation, multiple accessible mega menus, service discovery and knowledge discovery. |
| `footer.php` | Portal utility, trust, knowledge, provider, and legal navigation. |
| `front-page.php` | Rich portal homepage. It is intentionally content-dense rather than a small landing page. |
| `page-templates/` | Account, anatomy, directory, find-care, glossary, guides, health technology, professional, and treatment-hub layouts. |
| `single-hp_provider.php`, `single-hp_clinic.php` | Protected public profile templates. Only verified public state renders. |
| `single-hp_treatment.php` | Protected treatment template. Renders only reviewed editorial records. |
| `template-parts/profile-public.php` | Verified profile detail surface. It does not expose private routing or commercial meta. |
| `template-parts/treatment-public.php` | Editorial treatment detail surface. It requires a review state, review date, source note, and substantive content. |
| `inc/portal-route-registry.php` | Controlled route registry. Avoids casually inventing public URLs. |
| `inc/portal-template-helpers.php` | Governed helpers for filters, content gates, and public presentation. |
| `assets/css/portal.css` | Token-driven design system and portal chrome. |
| `assets/css/templates.css` | Template-level responsive styling. |
| `assets/js/portal.js` | Accessible multi-mega-menu and portal interactions. |
| `assets/js/anatomy-*` | Gated Three.js anatomy/discovery/map runtime. |
| `assets/vendor/three/` | Vendored Three.js dependencies with included notices. |

Design implementation details:

- The public font stack currently uses Noto Sans Hebrew and Noto Serif Hebrew from Google Fonts.
- Theme assets are token-driven. Do not hard-code ad hoc colors or type styles before checking `theme.json`, `portal.css`, and the component pattern.
- The child theme is intentionally thin. It only enqueues production child overrides after parent styles. Future production-specific changes belong there when they must not alter the reusable parent.
- No public fake doctors, clinics, products, booking slots, ratings, reviews, prices, or claims are hard-coded.

### Platform plugin: `plugin-src/hea-lth-platform-core`

The plugin owns governed data modeling, not public lead capture.

It defines controlled post types and terms for:

- `hp_provider`
- `hp_clinic`
- `hp_treatment`
- `hp_glossary`
- `hp_equipment`
- related specialty, region, service-type, and body-region taxonomies

It includes:

- public-state and verification metadata;
- editorial state, review date, and source note metadata;
- provider and clinic directory controller;
- anatomy manifest registry;
- map registry;
- lead-routing configuration and internal health view;
- public health route for release verification.

Deliberate boundaries:

- No public provider is created by code.
- No public inquiry intake endpoint exists.
- No visitor health information, diagnosis, documents, payment, contact, or CRM data is stored.
- No sponsored placement sorting is forced into a public response.
- No unverified map location or commercial map key is exposed.
- No anatomy asset is released without licensing, clinical review, visual QA, performance QA, and manifest approval.

## 8. Design and advanced-interface state

### Current visual direction

The local parent theme implements a premium Hebrew portal approach rather than copying Clalit or a generic clinic template. It includes a content-rich home, multiple mega menus, editorial and provider entry points, specialty and treatment discovery, technology areas, account and professional surfaces, and a future body-map gateway.

The owner has repeatedly asked for:

- very large, content-rich homepage;
- menus and mega menus at enterprise scale;
- doctors, clinics, hospitals, guides, news, articles, medical equipment, suppliers, technologies, and premium services;
- premium but credible visual language, not gold-on-black cliches and not cheap AI cards;
- a design system, good font selection, high-end logo work, and no low-cost generic result;
- side-by-side evidence against Israeli competitors such as Clalit and leading international health sites;
- no automatic/simple AI-generated visual design; use serious visual assets and clear asset rights;
- 3D human body interaction comparable to a product showroom, but clinically and legally legitimate.

### Figma and Lovable

- Lovable is approved for design exploration and research only. Do not use Lovable to build production code or create platform dependency.
- Figma is the preferred approved design system and component source. At the last check, the Figma Starter MCP quota was exhausted, so no additional Figma write should be claimed until access is available.
- Existing local output and design documents are not an approved final Figma design. Do not call them final visual approval.

### 3D human body engine

Implemented foundation:

- Three.js is vendored, not loaded from a mutable CDN.
- Viewer architecture supports rotation, layering, mesh selection, anatomy-to-discovery mapping, and routing toward treatments, professionals, clinics, gear, and later maps.
- Browser configuration defaults to `license-gated` and `no-approved-model`.

Still required:

- a licensed high-fidelity human anatomy asset with web delivery and derivative rights;
- clinical review owner and date;
- visual QA and performance QA on target devices;
- semantic mesh IDs and body-region mapping;
- user-facing safety copy and scope boundary;
- approved provider, clinic, equipment, and map inventory;
- commercial map provider contract, browser-key restrictions, origin restrictions, and disclosure;
- mobile and accessibility evaluation;
- measured performance budget.

Relevant local audit documents include:

- `docs/HAPPY_DAVINCI_FULL_ARCHIVE_AUDIT_2026-07-11.md`
- `docs/HEA_LTH_3D_ENGINE_RUNTIME_AND_RELEASE_GATE_V1_2026-07-11.md`
- `docs/HEA_LTH_ULTRA_REAL_3D_ANATOMY_ACCEPTANCE_AND_VENDOR_SHORTLIST_V1_2026-07-11.md`

## 9. SEO, content, and competitor work

### Owner mandate

The owner does not want a long-tail-only or "back door" strategy. The strategy must compete directly for important Israeli health, premium care, aesthetic medicine, plastic surgery, hair restoration, wellness, diagnostics, private medicine, equipment, and provider-discovery demand while using broad authoritative knowledge to support commercial category pages.

### Required SEO operating model

1. Start with a full crawl and legacy URL inventory.
2. Export Google Search Console query, page, country, device, date, CTR, impressions, clicks, and position data.
3. Gather Israeli keyword data from SEMrush or an equivalent live tool.
4. Research local Google SERPs for every important cluster. Capture titles, descriptions, page types, People Also Ask, suggested queries, rich results, local pack, video or image signals, domain patterns, and content format.
5. Collect direct competitor pages and observable language patterns from Israel and internationally.
6. Allocate one primary query intent to one canonical URL. Record secondary terms, SERP type, recommended title, content format, internal-link parent, conversion role, reviewer, and release status.
7. Detect and prevent cannibalization before content production.
8. Create content briefs, not bulk articles, in the repository. The owner wants final long-form articles generated through a researched ChatGPT Pro workflow rather than burning local agent context.
9. Require sources, author/reviewer identity, risk language, last-review date, structured data plan, and editorial review before publication.
10. Measure indexation, rankings, CTR, engagement, conversions, and revenue or qualified-lead outcomes after publication.

### Existing local research material

These files exist locally and should be reviewed, but many are currently untracked. Do not assume they are in GitHub until explicitly committed.

- `docs/SEO_MASTER_PLAN_2026-07-10.md`
- `docs/KEYWORD_URL_MAP_SEED_2026-07-10.csv`
- `docs/COMPETITOR_CAPABILITY_GAP_MAP_2026-07-10.md`
- `docs/COMPETITOR_HOME_DISCOVERY_AND_REVIEWED_CONTENT_2026-07-11.md`
- `docs/CHATGPT_DEEP_RESEARCH_MEGA_PROMPT_2026-07-10.md`
- `docs/THREED_AND_PROVIDER_COMPETITIVE_BENCHMARK_2026-07-11.md`
- `docs/URL_CANONICAL_DECISIONS_2026-07-11.md`
- `docs/REVIEWED_EDITORIAL_FEED_GATE_2026-07-11.md`
- `docs/TREATMENT_TEMPLATE_AND_EDITORIAL_GATE_2026-07-11.md`

### Important SEO safeguards already in code

- Existing canonical route skeleton is kept as a controlled registry.
- Planned URLs are marked evidence-gated and are not treated as automatic live SEO destinations.
- Treatment pages are not public merely because a custom post exists.
- Provider profiles are not public merely because a custom post exists.
- Avoid a blanket noindex or redirect strategy until actual crawl/GSC evidence says what each URL needs.

## 10. Monetization, CRM, lead routing, and marketplace plan

The owner wants real monetization, not a decorative directory. The intended model may combine paid provider participation, verified listings, premium placement with transparent disclosure, qualified lead referral, booking integration, diagnostic or technology referrals, sponsorship, commerce or affiliate referral where lawful, and provider tools.

No final revenue model is approved. A new agent must not create biased rankings, undisclosed ads, or a lead form that collects sensitive data before the following are designed and approved:

| Required capability | Minimum decision or proof |
| --- | --- |
| Offer design | Who pays, what they receive, pricing logic, contract, cancellation, and prohibited claims. |
| Provider eligibility | Credentialing, verification source, review cadence, geography, availability, insurance or private-care rules, and owner. |
| Directory fairness | Organic relevance rules, editorial distinction, paid-placement disclosure, audit trail, and appeal process. |
| Lead capture | Purpose limitation, minimum fields, consent language, no unnecessary health data, accessibility, retention, and subject access path. |
| Routing | Eligibility, capacity, priority policy, fallback owner, duplicate prevention, timeouts, consent, audit log, and alerting. |
| CRM | Explicit choice between HubSpot, Monday, or another system with data-processing terms, field mapping, roles, API boundaries, and sync ownership. |
| Attribution | UTM and source schema, event taxonomy, call/form/booking IDs, provider outcome feedback, reconciliation, and revenue reporting. |
| Marketplace operations | Onboarding, profile updates, review moderation, availability, dispute process, billing, retention, and success metrics. |

The existing routing code is a safe internal configuration foundation only. It must not be presented as a live CRM or working lead engine.

## 11. Quality gates and current local checks

The following have been run locally during this handoff work:

- PHP syntax lint across all new theme and platform PHP files.
- Python compilation of build and deploy scripts.
- `tests/test_wordpress_pipeline.py`.
- `actionlint` for the GitHub workflow.
- Deterministic build twice per package.
- `deploy-wordpress.py --dry-run` for all packages.
- PHPCS using the updated release profile across `plugin-src/hea-lth-platform-core`, `theme-src/hea-lth-portal`, and `theme-src/hea-lth-portal-child`.
- The PHPCS profile now focuses on PHP 7.4 compatibility and WordPress security for all new portal PHP, not only the old plugin.
- PHPStan across the new plugin and themes. It initially identified redundant checks in route and term-template code; those checks were removed or narrowed, and PHPStan now passes without ignore comments or a baseline.

## 12. Current local working-tree status

The repository is intentionally not a clean single-purpose checkout. It contains substantial untracked owner research, screenshots, design experiments, local preview tooling, and output folders. Treat those files as user-owned unless you created them and can prove scope.

Do not run:

- `git reset --hard`
- `git clean -fd`
- broad delete commands
- checkout/restore commands that discard untracked work
- destructive moves across unrelated folders

The new production source is committed on `main` through `9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c`. The activation, PHP compatibility, PHPStan memory, cache purge, and full public-HTML verification corrections are already included in that published lineage.

Untracked research and design assets should be reviewed and selectively committed in an explicit documentation branch later. Do not bulk-add all of `docs/`, `output/`, `preview/`, `design-lab/`, or `tmp/` without checking rights, relevance, size, privacy, and whether the material is a source artifact versus a local export.

## 13. How to verify a future live release

The initial release is no longer blocked. Use this sequence for every future portal, plugin, or asset deployment.

1. Change only governed source files. Do not bulk-add owner research, screenshots, temporary exports, or unknown assets.
2. Run deterministic package builds twice, pipeline unit tests, actionlint, PHP syntax lint, PHPCS, PHPStan, and a package dry run.
3. Push only after the local gates pass. The main-branch workflow deploys in this order: platform plugin, inactive parent theme, active child theme.
4. Record the new workflow run URL and deployment IDs. Check the relevant public health routes.
5. Verify the normal public homepage, without a cache-busting query string, includes both parent and child asset markers, the expected portal marker, and no PHP error markers.
6. Confirm the temporary `agentdeploy/v1` route is absent after cleanup.
7. Capture desktop and mobile evidence in actual Chrome Profile 3: home, open mega menu, and one internal template. Current Chrome capture is an unresolved evidence gap, not a reason to weaken the release gates.
8. For URL/content changes, add redirect/canonical and GSC checks before release. For 3D, add the separate asset manifest, clinical, quality, license, and performance review before release.

## 14. Immediate three priorities after the theme is live

1. **Live visual, accessibility, and performance verification**
   - Proof: Chrome Profile 3 desktop/mobile screenshots, keyboard menu behavior, responsive checks, Lighthouse/Core Web Vitals baseline, template visual QA, and error-log check if available.
   - Owner outcome: the owner can see and judge the actual public portal, not only a release log.

2. **SEO migration and evidence foundation**
   - Proof: full legacy crawl, GSC export, indexability map, redirect/canonical decisions, Israeli keyword universe, dated local SERP evidence, final one-intent-per-URL map, content inventory, and monitoring dashboard.
   - Owner outcome: protect existing organic value while building a credible route to difficult premium queries.

3. **Lead and revenue operating design**
   - Proof: approved offer model, provider verification policy, consent-first intake prototype, CRM decision, routing matrix, SLA, ownership, attribution specification, and testable non-sensitive lead flow.
   - Owner outcome: the portal can make, route, and measure revenue without compromising trust.

## 15. Recommended specialist skills and tool usage

Use the smallest relevant skill. Announce when a skill changes work direction.

| Need | Relevant skill or tool |
| --- | --- |
| Enterprise status, priorities, red alerts | `hea-lth-owner-operator` |
| Israeli keyword/SERP/URL governance | `hea-lth-seo-operator`, then `seo` or `seo-page` as relevant |
| WordPress deploy, rollback, GitHub Actions | `wordpress-agent-deploy` |
| Theme chrome and WordPress design integration | `premium-wordpress-site-chrome` |
| Figma design system or code conversion | Figma skills, only after quota/access is verified |
| Chrome state/login/screenshot work | `chrome:control-chrome`; use the user's attached Profile 3 only |
| Real browser automation outside Chrome state | `playwright` only when the task does not require the user's Chrome profile |
| Sales/CRM/provider workflow | `sales` index and relevant sales skills |
| Security review | `security-best-practices`, `security-threat-model` only when explicitly requested |
| GitHub repository and CI inspection | `github:github`, `github:gh-fix-ci`, `github:yeet` as appropriate |

## 16. Agent behavior rules

1. Start from ground truth. Inspect the live site, current commit, workflow run, and relevant document before claiming status.
2. Keep the owner informed in concise commentary at least every 60 seconds during long work.
3. Never claim a deploy succeeded only because a script said "installed". Require independent health and public verification.
4. Never claim the whole portal is live because one plugin or one theme file deployed.
5. Preserve existing URL equity. No careless URL deletion, content overwrite, noindex, or redirects.
6. Do not publish medical content, public providers, ratings, equipment claims, maps, or prices without approved evidence and owners.
7. Do not reveal secrets, App Passwords, access tokens, WordPress usernames, session data, or the contents of screenshots containing credentials.
8. Do not use Lovable to build the production website.
9. Do not substitute a fake 3D anatomy asset or map integration for the real approval gates.
10. Do not spend the whole project building tools. Tie every technical task back to a user, revenue, trust, SEO, operational, or release outcome.
11. Use `apply_patch` for source edits. Do not overwrite unrelated files with shell redirection.
12. Preserve the user's untracked work. Stage and commit only intentional files.

## 17. Useful safe commands

These commands do not print secret values.

```powershell
# Repository status and current branch
git status --short
git branch --show-current
git log --oneline --decorate -5

# Check GitHub workflow status
gh run list --repo The-new-ben/hea-lth-co-il --branch main --limit 5

# Read failed logs only
gh run view <run-id> --repo The-new-ben/hea-lth-co-il --log-failed

# List secret names only
gh secret list --repo The-new-ben/hea-lth-co-il
gh secret list --repo The-new-ben/hea-lth-co-il --env production

# Build deterministic packages twice, without a production write
$env:SOURCE_DATE_EPOCH = (git log -1 --format=%ct)
python scripts/build-wordpress-package.py --package hea-lth-platform-core
python scripts/build-wordpress-package.py --package hea-lth-portal
python scripts/build-wordpress-package.py --package hea-lth-portal-child
python scripts/deploy-wordpress.py --package hea-lth-platform-core --dry-run
python scripts/deploy-wordpress.py --package hea-lth-portal --dry-run
python scripts/deploy-wordpress.py --package hea-lth-portal-child --dry-run

# Public release check after a successful deployment
Invoke-RestMethod 'https://hea-lth.co.il/wp-json/hea-lth-platform/v1/healthcheck?deployment=<plugin-deployment-id>'
Invoke-RestMethod 'https://hea-lth.co.il/wp-json/hea-lth-portal/v1/healthcheck?deployment=<child-deployment-id>'
```

## 18. Phase-close status and definition of success

### Already evidenced

- `hea-lth-portal` parent and `hea-lth-portal-child` child deployed successfully to production;
- the child theme is active and the public normal URL renders portal assets, not Hello Elementor;
- deployment health routes contain the exact current release IDs;
- package build and digest verification, PHP 7.4 compatibility, PHPCS security/compatibility, PHPStan, cache purge, and complete public HTML verification passed in the successful production run;
- no fake clinical, provider, price, availability, review, or map data was added as a shortcut.

### Still required to close the visual release phase

- desktop and mobile visual evidence is captured in Chrome Profile 3;
- header, mega menus, homepage, treatment hub, directory, guides, professional/account discovery, and gated body-map entry are visually inspected;
- keyboard navigation, RTL behavior, responsive behavior, and core performance are recorded;
- release proof is entered in the project command center with dated screenshots.

### Explicitly out of scope for calling the portal enterprise complete

The following must remain separately governed: organic migration, full SEO evidence and content operation, lead operations, CRM and attribution, analytics, medical editorial governance, verified provider marketplace, commerce, 3D release, map integration, and legal/privacy safety. A green theme deployment is not evidence that any of these systems are live.

## 19. Copy/paste start message for a successor agent

```text
You are joining the Hea-lth.co.il enterprise portal project. Work in English.

First read these two source-of-truth files in full:
1. https://raw.githubusercontent.com/The-new-ben/hea-lth-co-il/main/docs/AGENT_HANDOFF_HEA_LTH_PORTAL_2026-07-11.md
2. https://raw.githubusercontent.com/The-new-ben/hea-lth-co-il/main/docs/PORTAL_SOURCE_FILE_MAP_2026-07-12.md

Ground truth:
- The live site is https://hea-lth.co.il/.
- Production is the active hea-lth-portal-child child theme over hea-lth-portal parent theme, with hea-lth-platform-core plugin.
- Current verified production commit: 9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c.
- Current verified successful production run: https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167917145.
- Do not expose, request, print, commit, or move secret values. GitHub secret names and locations are in the handoff.
- Do not delete or bulk-commit user-owned untracked files.
- Do not alter legacy URLs or mass-publish medical content without migration/SERP/GSC/reviewer evidence.
- Do not enable a generic placeholder GLB as medical anatomy. The WebGL engine is intentionally license/clinical/quality gated.
- Do not claim SEO, lead routing, CRM, provider marketplace, commerce, analytics, or the true 3D body are live. They are not.

Before any change, report: the exact user outcome, affected scorecard dimension, current proof, risks to URL equity/trust/privacy/performance, and acceptance evidence. Use the governed GitHub-to-WordPress pipeline for production code. Verify public health and full homepage markers after every release.
```

End of handoff.
