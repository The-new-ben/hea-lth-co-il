# Hea-lth Portal: full agent handoff

Last updated: 2026-07-11, Asia/Jerusalem

This is the current source-of-truth handoff for a new agent joining the Hea-lth project. Read it fully before changing code, content, WordPress, GitHub, SEO strategy, design, tracking, or provider data.

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

| Area | State | Evidence |
| --- | --- | --- |
| Current public site | Live | `https://hea-lth.co.il/` returns HTTP 200. |
| Current active theme | Live | Public page currently loads `/wp-content/themes/hello-elementor`. |
| New platform plugin | Live and independently verified | `hea-lth-platform-core` v0.1.0 is active. Public health route returned `status: ok` for deployment `gh-59479910eabef85205eaba0d1a0df07faf8cc21e-29166607077-1`. |
| New parent theme | Installed, inactive | The GitHub release job installed `hea-lth-portal` v0.1.0 and verified it through authenticated WordPress theme REST. |
| New child theme | Not installed or activated yet | The first activation attempt stopped before installation because its release manifest incorrectly required PHP 8.1 while production runs PHP 7.4.33. |
| Temporary deployment bridge cleanup | Proven for the failed child attempt | The temporary Code Snippets route was deleted and its REST route confirmed absent. |
| CI validation | Proven for commit `5947991` | GitHub Actions run `29166607077` completed build, lint, static analysis, secret scan, reproducible builds, and dry runs successfully. |

### What is not live yet

| Area | State | Required before claiming success |
| --- | --- | --- |
| From-scratch portal theme | In progress | Correct PHP compatibility manifest, successful child deployment, activation, public health verification, visual QA, and screenshot evidence. |
| Full design approval | Evidence-gated | Figma quota was exhausted. Local source design exists but is not a final approved Figma system. |
| SEO program | Foundation and evidence-gated | Research documents and seed keyword map exist, but no comprehensive Israeli keyword database, SERP pack, final keyword-to-URL map, redirect map, or publishing calendar is approved. |
| Lead routing and monetization | Foundation | Plugin has governed data model and routing boundaries, but there is no live intake form, consent workflow, CRM integration, SLA, attribution, billing, or production routing automation. |
| Verified provider marketplace | Foundation | Data types and protected templates exist. There are no populated verified providers, clinics, contracts, reviews, schedules, or commercial inventory. |
| Commerce | Not started | No catalog, stock, checkout, payment, fulfillment, return, or regulated-product controls exist. |
| 3D human body experience | Engine foundation only | Three.js interaction code exists. No approved high-fidelity medical asset, no approved anatomy mappings, no public provider map data, and no commercial map key are released. |
| Analytics and experiments | Not started or unknown | GSC access was shown by the owner, but data extraction, GA4, event taxonomy, dashboards, conversion reporting, and CRM attribution are not proven in repository artifacts. |

## 4. Active red alerts

1. **Production theme activation is blocked by a manifest compatibility error.**
   - Impact: the new portal is not visible. The current live homepage remains Hello Elementor.
   - Root cause: the child theme's deployment configuration was set to `requires_php: 8.1`; the production WordPress preflight reported PHP `7.4.33`.
   - Containment: the child package was not installed, no active theme changed, temporary bridge was deleted. The platform plugin and inactive parent theme remain installed.
   - Corrective source change already made locally: `deploy/wordpress-deploy.json` now sets the child `requires_php` to `7.4`.
   - Required next proof: commit, push, CI run, child installation and activation, `/wp-json/hea-lth-portal/v1/healthcheck?deployment=<new-id>`, public homepage visual check.

2. **Lead routing is not operational.**
   - The historic business problem is that leads were not routed properly.
   - Current plugin intentionally does not collect health data, leads, payment data, or contact forms. This is safe but means it does not solve the operational leak yet.
   - Required next product work: consent-first intake design, CRM choice, endpoint contract, qualification fields, provider eligibility, queue/SLA, fallback owner, audit log, attribution, reporting, and test matrix.

3. **Organic migration is not ready.**
   - Legacy URLs have value. The new theme uses a controlled route registry and sends visual navigation to known legacy skeleton paths, but that is not a redirect or migration plan.
   - Do not remove URLs, publish replacement content, or create broad redirects without a crawl, GSC metrics, backlink inspection, canonical decisions, and post-launch monitoring.

4. **3D launch asset is not approved.**
   - Do not represent the current viewer as an ultra-real medical model. It is a gated technical foundation only.

5. **Medical content cannot be mass-produced yet.**
   - Full articles need real Israeli SERP research, competitor research, expert-review ownership, dated primary sources, keyword-to-URL allocation, and anti-cannibalization rules.

## 5. URLs, GitHub, WordPress, and environments

### Production endpoints

| Purpose | URL or identifier | Notes |
| --- | --- | --- |
| Public site | `https://hea-lth.co.il/` | Current public homepage still uses Hello Elementor until the child activation is successful. |
| WordPress REST root | `https://hea-lth.co.il/wp-json/` | Read-only inspection verified it is reachable. |
| Core plugins REST route | `/wp-json/wp/v2/plugins` | Available; requires authenticated WordPress deployment identity for mutation. |
| Core themes REST route | `/wp-json/wp/v2/themes` | Available; used for authenticated inactive-parent verification. |
| Platform health | `/wp-json/hea-lth-platform/v1/healthcheck` | Safe public release identity only. No provider, lead, medical, or credential data. |
| Child theme health after activation | `/wp-json/hea-lth-portal/v1/healthcheck` | Exists only when the child theme is active. |
| UPress account | `https://my.upress.co.il/account/websites/hea-lth.co.il` | Owner controls hosting. Do not use manual ZIP upload as a substitute for the governed deploy pipeline. |

### Repository

| Item | Value |
| --- | --- |
| Local working directory | `C:\Users\pro\Documents\websites\hea-lth-co-il` |
| Git remote | `https://github.com/The-new-ben/hea-lth-co-il.git` |
| Production branch | `main` |
| Current development branch | `codex/portal-production` |
| Latest pushed production commit at handoff | `e847a47fe6d6b6c4220043d94eff281c01f75d59` |
| First portal deployment run | `https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29166607077` |
| First portal deployment result | Failed only at child-theme PHP compatibility preflight. Plugin and parent steps succeeded. |
| Second portal CI run | `https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167134652` stopped before production deployment when PHPStan exhausted the workflow's 512 MB limit. |

### GitHub access and credentials

Never print, paste, commit, screenshot, or request raw secret values in chat, shell output, logs, docs, or source control.

| System | Current known access | What the next agent may do |
| --- | --- | --- |
| GitHub CLI | Authenticated as `The-new-ben`; usable scopes included `repo` and `workflow` | Read repository, push approved source, inspect workflow runs and secrets by name only. |
| GitHub environment | `production` exists | It has `WP_BASE_URL` and `WP_USER` environment secrets. |
| Repository secrets | `WP_BASE_URL` and `WP_APP_PASSWORD` were listed by name | Workflow currently resolves the values. Do not expose or rotate them casually. Recommended later hardening: move `WP_APP_PASSWORD` into the `production` environment and remove duplicate repository-level secrets only after a successful verified release. |
| WordPress deployment identity | Authenticated in GitHub Action as WordPress user ID `1`, with `update_plugins` | The workflow also needs `install_plugins`, `update_themes`, `install_themes`, and `switch_themes` for a fresh theme install and activation. The bridge will report exact missing capabilities if absent. |
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
- The temp Code Snippets bridge is deleted in `finally` and the route is rechecked for 404.

### Important pipeline status at handoff

- GitHub run `29166607077` proves that the deploy identity and host filesystem work.
- Plugin deployment succeeded and was independently verified on public WordPress REST.
- Parent theme install succeeded and was independently verified using authenticated Themes REST.
- Child preflight rejected `requires_php: 8.1` because the host reports `7.4.33`; no child files were installed and no theme changed.
- The local corrective edit sets the child manifest requirement to `7.4`. It must be committed and pushed in a new run. Do not re-run the old commit.

Update after the first correction attempt:

- Commit `e847a47` corrected the child requirement and expanded portal-wide PHPCS and PHPStan coverage.
- GitHub Actions run `29167134652` stopped before production deployment because the newly expanded PHPStan scan exceeded the workflow's previous 512 MB memory limit. The local scan passed at 1 GB.
- The corrective workflow edit must use `--memory-limit=1G`. Do not reduce the portal scan back to the legacy plugin-only scope.

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

The new production source is committed on `main` through `5947991`. A follow-up corrective commit is needed for:

- child manifest PHP compatibility from 8.1 to 7.4;
- broadened portal quality configuration;
- security and PHPStan fixes made after the failed run.

Untracked research and design assets should be reviewed and selectively committed in an explicit documentation branch later. Do not bulk-add all of `docs/`, `output/`, `preview/`, `design-lab/`, or `tmp/` without checking rights, relevance, size, privacy, and whether the material is a source artifact versus a local export.

## 13. How to resume the blocked live release

Follow this exact sequence. Do not skip package or health verification.

1. Finish the current PHPStan fixes and run PHPCS and PHPStan again.
2. Confirm `deploy/wordpress-deploy.json` has `hea-lth-portal-child` manifest `requires_php` set to `7.4`.
3. Run the deterministic package build twice for all three packages and compare SHA-256 values.
4. Run all pipeline tests, actionlint, PHP syntax lint, PHPCS, and PHPStan.
5. Commit only the corrective source and quality configuration files. Do not add unrelated untracked research.
6. Fetch `origin/main`, ensure the branch can fast-forward or rebase without overwriting user work, and push the correction to `main`.
7. Monitor the new GitHub Action run. The production workflow order is plugin, parent theme, child theme.
8. Confirm the child deployment has a new deployment ID and succeeds.
9. Verify independently:
   - `https://hea-lth.co.il/wp-json/hea-lth-platform/v1/healthcheck?deployment=<plugin-id>` if plugin changed;
   - `https://hea-lth.co.il/wp-json/hea-lth-portal/v1/healthcheck?deployment=<child-id>`;
   - public homepage returns HTTP 200;
   - public HTML loads `/wp-content/themes/hea-lth-portal/` parent assets and `/wp-content/themes/hea-lth-portal-child/style.css`;
   - navigation and homepage have no PHP warnings, fatal error, broken CSS, or missing assets;
   - temporary `agentdeploy/v1` route is absent after cleanup.
10. Use actual Chrome Profile 3 screenshots after the owner attaches it. Capture desktop and mobile evidence of the homepage, mega menus, and one important internal template. Do not use the in-app browser as a substitute when the user explicitly asks for Chrome.

## 14. Immediate three priorities after the theme is live

1. **Live visual and technical verification**
   - Proof: Chrome screenshots, responsive checks, public HTML and asset checks, menu keyboard interaction, page-template checks, status/health proof, error log check if available.
   - Owner outcome: the owner can actually see the new portal and judge it.

2. **SEO migration and data foundation**
   - Proof: full legacy crawl, GSC export, indexability map, redirect/canonical decisions, keyword universe, Israeli SERP evidence, final keyword-to-URL map, content inventory, and monitoring dashboard.
   - Owner outcome: protect existing traffic while building a credible route to difficult premium queries.

3. **Lead and revenue operating design**
   - Proof: approved offer model, provider verification policy, consent-first intake prototype, CRM decision, routing matrix, SLA, ownership, attribution specification, and testable non-sensitive lead flow.
   - Owner outcome: revenue and service quality are engineered before high-value traffic is sent into the system.

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

## 18. Definition of success for this phase

This phase is complete only when all of the following are evidenced:

- `hea-lth-portal` parent and `hea-lth-portal-child` child deploy successfully to production;
- the child theme is active and public pages render the new portal, not Hello Elementor;
- the deployment bridge is always cleaned up and returns 404 afterward;
- package build and digest verification pass;
- PHP 7.4 compatibility is proven against the live host;
- PHPCS security/compatibility and PHPStan pass for new portal source;
- desktop and mobile visual evidence is captured in Chrome Profile 3;
- header, mega menus, homepage, treatment hub, directory, guides, professional/account discovery, and gated body-map entry are visually checked;
- no fake clinical, provider, price, availability, review, or map data appears;
- new architecture does not destroy legacy URL equity;
- next-phase SEO migration, lead operations, analytics, medical editorial, provider marketplace, and 3D release gates have named owners and evidence requirements.

End of handoff.
