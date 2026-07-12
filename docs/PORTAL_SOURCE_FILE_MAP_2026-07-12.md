# Hea-lth portal source file map

Last updated: 2026-07-12, Asia/Jerusalem
Applies to production commit: [`9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c`](https://github.com/The-new-ben/hea-lth-co-il/commit/9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c)

This map is the companion to [AGENT_HANDOFF_HEA_LTH_PORTAL_2026-07-11.md](AGENT_HANDOFF_HEA_LTH_PORTAL_2026-07-11.md). It inventories all first-party production source files currently committed for the new portal. It does not treat untracked owner research, local exports, archive files, secrets, WordPress uploads, or generated deployment ZIPs as source of truth.

Repository: [The-new-ben/hea-lth-co-il](https://github.com/The-new-ben/hea-lth-co-il)
Production branch: [`main`](https://github.com/The-new-ben/hea-lth-co-il/tree/main)
Live portal: [https://hea-lth.co.il/](https://hea-lth.co.il/)

## 1. Orientation

```text
hea-lth-co-il/
+-- .github/workflows/                 GitHub Actions quality and deploy workflow
+-- deploy/                            deployment contract and temporary bridge source
+-- plugin-src/hea-lth-platform-core/  business/data/health-gate plugin
+-- scripts/                           deterministic package builder and REST deploy client
+-- tests/                             pipeline unit tests
+-- theme-src/hea-lth-portal/          from-scratch parent portal theme
+-- theme-src/hea-lth-portal-child/    live activation point and thin child overrides
`-- tooling/php-quality/               static-analysis configuration
```

The live WordPress relationship is deliberate:

```text
hea-lth-platform-core plugin
          | supplies gated public configuration through WordPress filters
          v
hea-lth-portal parent theme
          | owns design system, templates, portal chrome, templates, and viewer shell
          v
hea-lth-portal-child theme
          | active on production and exposes the public child healthcheck
          v
https://hea-lth.co.il/
```

## 2. Release, deployment, and recovery files

| Path | Responsibility | Key reference |
| --- | --- | --- |
| [`.github/workflows/wordpress-deploy.yml`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/.github/workflows/wordpress-deploy.yml) | Validates source, runs quality gates, builds deterministic artifacts, deploys the three packages on `main`. PHPStan receives 1 GB, not the old 512 MB. | [workflow](https://github.com/The-new-ben/hea-lth-co-il/blob/main/.github/workflows/wordpress-deploy.yml) |
| [`deploy/wordpress-deploy.json`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/deploy/wordpress-deploy.json) | Package contract. Defines the plugin, inactive parent, active child, PHP 7.4 compatibility, public health paths, public HTML markers, and `cache_purge_path`. | [`cache_purge_path`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/deploy/wordpress-deploy.json#L4) |
| [`deploy/agentdeploy-route.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/deploy/agentdeploy-route.php) | Ephemeral WordPress Code Snippets bridge. It permits only a signed, allowed release during a deployment and supports verification/rollback. It is not a permanent API. | [`rest_api_init` route registration](https://github.com/The-new-ben/hea-lth-co-il/blob/main/deploy/agentdeploy-route.php#L605) |
| [`scripts/build-wordpress-package.py`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/build-wordpress-package.py) | Builds a reproducible ZIP with one top-level slug directory, normalized timestamps, manifest, and SHA-256. | [builder](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/build-wordpress-package.py) |
| [`scripts/deploy-wordpress.py`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/deploy-wordpress.py) | Authenticated REST deployment client. Preflights, uploads, checks package digests, activates only allowed components, verifies health, purges cache, verifies full public HTML, and cleans up the temporary bridge. | [full response retention](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/deploy-wordpress.py#L154-L167), [cache purge](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/deploy-wordpress.py#L345-L350), [public marker gate](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/deploy-wordpress.py#L353-L367) |
| [`scripts/normalize-wave0-keyword-map.py`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/normalize-wave0-keyword-map.py) | Normalizes the early keyword-map research input. It is not a complete SEO research engine and must not substitute for live Israeli SERP/GSC research. | [script](https://github.com/The-new-ben/hea-lth-co-il/blob/main/scripts/normalize-wave0-keyword-map.py) |
| [`tests/test_wordpress_pipeline.py`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/tests/test_wordpress_pipeline.py) | Python tests for route rendering, request transport, release identity, theme verification, cache purge, public marker checks, and rollback behavior. | [cache/public verification tests](https://github.com/The-new-ben/hea-lth-co-il/blob/main/tests/test_wordpress_pipeline.py#L157-L185) |

### Release flow in code

```text
commit on main
  -> GitHub Actions validation
  -> deterministic package build x2
  -> temporary authenticated deployment bridge
  -> platform plugin
  -> parent theme install and REST verification
  -> child theme install and activation
  -> healthcheck deployment identity
  -> authenticated ezCache purge
  -> full public HTML markers
  -> bridge deletion and route-absence check
```

Current public proof URLs, which reveal only release identity:

- [Platform healthcheck](https://hea-lth.co.il/wp-json/hea-lth-platform/v1/healthcheck?deployment=gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1)
- [Active child healthcheck](https://hea-lth.co.il/wp-json/hea-lth-portal/v1/healthcheck?deployment=gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1-child)
- [Successful production run 29167917145](https://github.com/The-new-ben/hea-lth-co-il/actions/runs/29167917145)

## 3. Platform plugin inventory

Directory: [`plugin-src/hea-lth-platform-core/`](https://github.com/The-new-ben/hea-lth-co-il/tree/main/plugin-src/hea-lth-platform-core)

| Path | Responsibility | Safety boundary |
| --- | --- | --- |
| [`hea-lth-platform-core.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/hea-lth-platform-core.php) | Plugin entry point, version metadata, dependency loading, boot. | Do not add lead capture or medical claims here without a separate product and privacy review. |
| [`includes/class-hea-lth-platform-core.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-platform-core.php) | Registers governed custom post types, taxonomy/meta boundaries, and platform boot sequence. | Keeps unpublished and unverified entities from being casually exposed. |
| [`includes/class-hea-lth-directory-controller.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-controller.php) | Directory REST/controller boundary for public discovery surfaces. | Must not become a raw data dump or expose private provider/routing data. |
| [`includes/class-hea-lth-directory-map-registry.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-directory-map-registry.php) | Map configuration/registry gate for public directory maps. | No browser map key or provider coordinates are emitted without a governed configuration. |
| [`includes/class-hea-lth-lead-route-resolver.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-lead-route-resolver.php) | Internal routing policy data boundary. | Not a live CRM, form, booking, consent system, or billing system. |
| [`includes/class-hea-lth-anatomy-model-registry.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php) | WordPress settings, manifest validation, and public-safe configuration filter for a real WebGL anatomy model. | This is the hard 3D asset gate. |
| [`README.md`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/README.md) | Package-level explanation. | Documentation only. |

### 3D registry anchors

| Code | What it proves |
| --- | --- |
| [`OPTION`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php#L20-L31) | The full internal manifest is stored as `hea_lth_anatomy_model_manifest`; high-detail quality begins at 100,000 triangles. |
| [`boot()`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php#L43-L48) | The registry registers WordPress admin/settings/REST/filter hooks. |
| [`gate_manifest()`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php#L555-L587) | Requires approved status, web and derivative rights, named clinical review, GLB validation, visual/performance QA, anatomy fidelity QA, semantic mesh QA, and a valid high-detail LOD. |
| [`gated_configuration()`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php#L628-L640) | Safe default is `license-gated`, `engine: none`. |

## 4. Parent theme inventory

Directory: [`theme-src/hea-lth-portal/`](https://github.com/The-new-ben/hea-lth-co-il/tree/main/theme-src/hea-lth-portal)

### Theme foundation and route governance

| Path | Responsibility |
| --- | --- |
| [`style.css`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/style.css) | WordPress parent theme metadata and version. |
| [`theme.json`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/theme.json) | Design tokens and editor foundation. Check this before hard-coding colors, typography, spacing, or radii. |
| [`functions.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/functions.php) | Theme setup, enqueue behavior, safe public 3D/map config, body-discovery route keys, and conditionally loaded 3D runtime. |
| [`inc/portal-route-registry.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/inc/portal-route-registry.php) | Controlled route skeleton. It protects URL discipline while migration evidence is incomplete. |
| [`inc/portal-template-helpers.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/inc/portal-template-helpers.php) | Safe public rendering, gating, and helper functions. |
| [`README.md`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/README.md) | Parent-theme implementation notes. |

### Portal chrome and broad templates

| Path | Responsibility |
| --- | --- |
| [`header.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/header.php) | RTL header, accessible multi-level portal navigation, mega menus, brand/search/utility surface. |
| [`footer.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/footer.php) | Utility, trust, provider, knowledge, and legal navigation. |
| [`front-page.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/front-page.php) | Live content-rich homepage. It is an enterprise portal surface, not a thin landing page. |
| [`page.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page.php) | Safe general-page rendering. |
| [`index.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/index.php) | Theme fallback template. |
| [`archive.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/archive.php) | Archive surface. |
| [`search.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/search.php) | Search-results surface. |
| [`single.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/single.php) | Generic WordPress post view. |
| [`404.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/404.php) | Error page. |

### Structured public entity templates

| Path | Responsibility | Gate |
| --- | --- | --- |
| [`single-hp_provider.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/single-hp_provider.php) | Public professional profile. | Only approved public-state content can render. |
| [`single-hp_clinic.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/single-hp_clinic.php) | Public clinic profile. | Only approved public-state content can render. |
| [`single-hp_treatment.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/single-hp_treatment.php) | Public treatment content. | Requires reviewed editorial state and substantive source/review fields. |
| [`template-parts/profile-public.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/template-parts/profile-public.php) | Reusable provider/clinic surface. | Keeps private routing and commercial metadata off public pages. |
| [`template-parts/treatment-public.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/template-parts/treatment-public.php) | Reusable treatment detail surface. | Requires review state, review date, source note, and content. |

### Page-template inventory

| Path | Intended page/journey |
| --- | --- |
| [`template-account.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-account.php) | Future account and saved-discovery surface. It is not an authenticated personal health record. |
| [`template-anatomy.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-anatomy.php) | Accessible body discovery and the real 3D viewer shell when an approved asset exists. |
| [`template-directory.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-directory.php) | Provider/clinic directory. |
| [`template-find-care.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-find-care.php) | Help users navigate toward appropriate discovery journeys. Not diagnosis. |
| [`template-glossary.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-glossary.php) | Medical and health glossary foundation. |
| [`template-guides.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-guides.php) | Guides and evidence-gated editorial discovery. |
| [`template-health-technology.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-health-technology.php) | Technology and equipment discovery. |
| [`template-professionals.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-professionals.php) | Professional and clinic discovery. |
| [`template-treatment-hub.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-treatment-hub.php) | Treatment/category hub. |

## 5. 3D and map runtime: exact current state

### The code chain

| Part | File and anchor | Current behavior |
| --- | --- | --- |
| Safe default | [`functions.php` lines 95-110](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/functions.php#L95-L110) | Returns `license-gated`, `engine: none`, `reason: no-approved-model` until the plugin emits a safe public configuration. |
| Approval predicate | [`functions.php` lines 149-157](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/functions.php#L149-L157) | The renderer is allowed only when `status === approved` and `engine === three-webgl`. |
| Local Three import map | [`functions.php` lines 159-185](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/functions.php#L159-L185) | Self-hosts Three.js. It returns without output unless the asset is approved. |
| Viewer asset enqueue | [`functions.php` lines 187-247](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/functions.php#L187-L247) | Loads accessible discovery and map boundary on the anatomy page. It only loads `anatomy-three-viewer.js` if the approval predicate passes. |
| Actual viewer shell | [`template-anatomy.php` lines 1-76](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/page-templates/template-anatomy.php#L1-L76) | Contains `data-anatomy-viewer`, `data-anatomy-model-stage`, semantic region/context controls, results, and a map boundary. It explains that discovery is not diagnosis. |
| Home-page teaser | [`front-page.php` lines 267-295](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/front-page.php#L267-L295) | This is a styled, non-WebGL teaser with a link to the body journey. It is **not** the actual rotatable model. |
| Discovery controller | [`assets/js/anatomy-discovery.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/js/anatomy-discovery.js) | Accessible discovery selection, states, resolver behavior, and fallback. |
| Three renderer | [`assets/js/anatomy-three-viewer.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/js/anatomy-three-viewer.js) | WebGL rotation, model loading, mesh selection, layer/interaction behavior once a configuration is approved. |
| Map boundary | [`assets/js/anatomy-directory-map.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/js/anatomy-directory-map.js) | Reacts to selected body region and a governed map config. It remains inactive without approved map configuration and inventory. |
| Anatomy discovery data | [`assets/data/anatomy-discovery-v1.json`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/data/anatomy-discovery-v1.json) | Region/context discovery records and current gated state. It is not a clinical diagnosis dataset. |
| Home teaser behavior | [`assets/js/portal.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/js/portal.js#L240) | Adds basic teaser interaction only. It does not render WebGL. |

### Why the 3D body is not on the front page now

The blocker is not that Three.js or a rotatable model is impossible. The engine and its viewer shell exist. The code intentionally refuses to enable it because the only archive candidate was an unaudited generic placeholder. A generic or CesiumMan-like GLB cannot truthfully be presented as an ultra-realistic clinical anatomy model.

The explicit rejection logic checks all of these before it returns a browser model URL:

1. approved manifest status;
2. web delivery and derivative-use rights;
3. named clinical reviewer and review date;
4. valid source GLB, passed visual QA, passed performance QA;
5. at least 100,000 triangles in the high-detail source;
6. passed anatomical-fidelity and semantic-mesh QA;
7. an approved detail LOD and a same-origin or root-relative public asset path.

The most important direct code is [`gate_manifest()`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php#L555-L587). The current safe fallback is [`gated_configuration()`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/plugin-src/hea-lth-platform-core/includes/class-hea-lth-anatomy-model-registry.php#L628-L640).

### Correct implementation path for a true homepage model

1. Procure or create a license-cleared, clinically reviewed GLB/GLTF with explicit online delivery and derivative rights. Keep the executed contract reference outside the public repository.
2. Build high, medium, and mobile LODs. The high-detail LOD must meet the 100,000 triangle policy; the viewer must not force every mobile device to load it.
3. Produce a semantic mesh map, such as `anatomy:nose`, `anatomy:scalp`, `anatomy:skin-face`, with documented medical and non-medical discovery contexts. Do not turn body selection into diagnosis.
4. Pass GLB, visual, performance, fidelity, semantic-mesh, RTL/accessibility, and target-device QA. Obtain dated clinical approval.
5. Upload the public asset to a controlled same-origin location. Register the approved manifest in the plugin's WordPress admin screen. Never place a raw vendor URL or untrusted embed in public code.
6. Extend [`hea_lth_portal_print_anatomy_three_import_map()`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/functions.php#L167-L185) and [`hea_lth_portal_enqueue_anatomy_assets()`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/functions.php#L192-L247) to permit `is_front_page()` only when the manifest is already approved.
7. Replace the home teaser with the same semantic viewer shell that is used by `template-anatomy.php`, keep a text-only fallback, and add a mobile/performance fallback. Do not duplicate engine logic.
8. Wire only verified services, providers, clinics, products, and map locations. Paid placement must be disclosed and cannot silently override clinical relevance.
9. Ship through the existing pipeline, then inspect real Chrome desktop and mobile screenshots plus runtime performance.

## 6. Design, CSS, and browser behavior files

| Path | Responsibility |
| --- | --- |
| [`assets/css/portal.css`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/css/portal.css) | Primary portal chrome, tokens, colors, typography, grids, mega menu, homepage, motion, and responsive rules. |
| [`assets/css/templates.css`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/css/templates.css) | Template-specific layouts including directory, anatomy, treatment, and editorial surfaces. |
| [`assets/css/editor.css`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/css/editor.css) | Editor-side consistency with theme design primitives. |
| [`assets/js/portal.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/js/portal.js) | Mega menus, portal UI, mobile surface, and anatomy teaser behavior. |
| [`assets/js/directory-browser.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/js/directory-browser.js) | Directory browsing/filter interaction. |

### Vendored Three.js boundary

The following files are third-party vendor code and should only change through an intentional version/licensing update:

- [`assets/vendor/three/LICENSE`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/LICENSE)
- [`assets/vendor/three/THIRD_PARTY_NOTICES.md`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/THIRD_PARTY_NOTICES.md)
- [`assets/vendor/three/build/three.core.min.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/build/three.core.min.js)
- [`assets/vendor/three/build/three.module.min.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/build/three.module.min.js)
- [`assets/vendor/three/examples/jsm/controls/OrbitControls.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/controls/OrbitControls.js)
- [`assets/vendor/three/examples/jsm/loaders/GLTFLoader.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/loaders/GLTFLoader.js)
- [`assets/vendor/three/examples/jsm/utils/BufferGeometryUtils.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/utils/BufferGeometryUtils.js)
- [`assets/vendor/three/examples/jsm/utils/SkeletonUtils.js`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal/assets/vendor/three/examples/jsm/utils/SkeletonUtils.js)

## 7. Live child theme

Directory: [`theme-src/hea-lth-portal-child/`](https://github.com/The-new-ben/hea-lth-co-il/tree/main/theme-src/hea-lth-portal-child)

| Path | Responsibility |
| --- | --- |
| [`style.css`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal-child/style.css) | WordPress child-theme metadata. Its `Template: hea-lth-portal` declaration makes the parent-child relationship explicit. |
| [`functions.php`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal-child/functions.php) | Enqueues child assets and exposes the safe public portal healthcheck. Important anchors: [enqueue](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal-child/functions.php#L19), [route registration](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal-child/functions.php#L35), [healthcheck](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal-child/functions.php#L55). |
| [`README.md`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/theme-src/hea-lth-portal-child/README.md) | Child-theme scope and notes. |

Production uses this child as the activation point. Keep it thin. Reusable portal behavior belongs in the parent theme or plugin. Child changes should be genuinely production-specific or safe overrides, not a second divergent portal implementation.

## 8. Quality configuration

| Path | Responsibility |
| --- | --- |
| [`tooling/php-quality/composer.json`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/tooling/php-quality/composer.json) | PHP QA dependencies. |
| [`tooling/php-quality/composer.lock`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/tooling/php-quality/composer.lock) | Locked QA dependency versions. |
| [`tooling/php-quality/phpcs.xml.dist`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/tooling/php-quality/phpcs.xml.dist) | WordPress coding/security and PHP 7.4 quality profile across new plugin and themes. |
| [`tooling/php-quality/phpstan.neon.dist`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/tooling/php-quality/phpstan.neon.dist) | Static-analysis scope for the plugin, parent theme, and child theme. |
| [`tooling/php-quality/README.md`](https://github.com/The-new-ben/hea-lth-co-il/blob/main/tooling/php-quality/README.md) | Local QA command guidance. |

## 9. Local and external resources, not committed production source

These local paths exist or were observed during work. They are user-owned or evidence material unless explicitly reviewed, rights-checked, and committed in a dedicated documentation change. Do not bulk-stage or delete them.

| Local path | Classification | Rule |
| --- | --- | --- |
| `C:\Users\pro\Documents\websites\hea-lth-co-il\docs\` research files, `docs\competitive-intelligence\`, images | Untracked research/evidence | Review one-by-one for rights, provenance, size, sensitive information, and relevance before committing. |
| `C:\Users\pro\Documents\websites\hea-lth-co-il\design-lab\` | Untracked design exploration | Not final Figma approval. |
| `C:\Users\pro\Documents\websites\hea-lth-co-il\output\`, `preview\`, `tmp\` | Untracked local output | Do not use as production source automatically. |
| `C:\Users\pro\Documents\websites\hea-lth-co-il\tooling\anatomy-fixture\`, `tooling\tests\`, `tooling\theme-preview\` | Untracked local tool/test work | Preserve until individually audited. |
| `C:\Users\pro\Documents\antigravity\happy-davinci.rar` and `.full.rar` | External candidate archive | Audited as an unacceptable public anatomy substitute. Do not deploy it as a medical body model. |
| `C:\Users\pro\Downloads\hea-lth_research_pack_wave0_2026-07-10.zip` | External research pack | Review as input evidence, not deployed code. |

## 10. Credentials and access locations

Secrets are deliberately not documented by value. The next agent needs locations and names only:

| System | Where to look | Allowed action |
| --- | --- | --- |
| GitHub repository | [repository settings](https://github.com/The-new-ben/hea-lth-co-il/settings) | Inspect secret names and action runs. Do not print values. |
| GitHub environment | `production` environment | `WP_BASE_URL` and `WP_USER` are managed there by name. |
| Repository secrets | repository-level secrets | `WP_BASE_URL` and `WP_APP_PASSWORD` are used by the workflow. Do not retrieve into local shell output or documentation. |
| WordPress administration | [https://hea-lth.co.il/wp-admin/](https://hea-lth.co.il/wp-admin/) | Use only through the authorized workflow or owner-attached browser state. Do not copy credentials into code. |
| UPress hosting | [UPress website account](https://my.upress.co.il/account/websites/hea-lth.co.il) | Hosting ownership and backups. Manual ZIP upload is not the normal deploy route. |
| Google Search Console | owner-provided property link | Read/export query and page data only after explicit browser/API access works. |
| Chrome Profile 3 | local user Chrome state | Required for owner-requested visual evidence. Current capture interface failed; do not fabricate screenshots. |

## 11. First commands for a successor

Run from `C:\Users\pro\Documents\websites\hea-lth-co-il`:

```powershell
git status --short
git branch --show-current
git log --oneline --decorate -8
git fetch origin
git log --oneline HEAD..origin/main
gh run list --repo The-new-ben/hea-lth-co-il --branch main --limit 5
```

Then verify production without exposing credentials:

```powershell
Invoke-RestMethod 'https://hea-lth.co.il/wp-json/hea-lth-platform/v1/healthcheck?deployment=gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1'
Invoke-RestMethod 'https://hea-lth.co.il/wp-json/hea-lth-portal/v1/healthcheck?deployment=gh-9d4287282c75b6fcf3bc91b71bf1b1e665d9ff3c-29167917145-1-child'
```

Before editing deployment source, read the full agent handoff, `deploy/wordpress-deploy.json`, `scripts/deploy-wordpress.py`, and the WordPress deployment skill instructions. Before changing 3D, read the full 3D audit and acceptance docs listed in the handoff. Before creating SEO content, use the Hea-lth SEO workflow and obtain live Israeli SERP/GSC evidence.

## 12. Handoff acceptance checklist

A new agent has enough ground truth only when they can answer all of these without guessing:

- What is live at the base URL, and what public proof verifies it?
- Which component is active, and which component is the parent?
- Where is the only authorized production deployment path?
- Why is the true WebGL body model absent from the homepage?
- Which specific gate turns the model on?
- Which business systems are still foundations rather than live operations?
- Which local files are user-owned/untracked and must not be deleted or mass-committed?
- Which evidence must be obtained before medical content, redirects, real provider data, lead routing, map locations, or paid placement can go live?

If any answer is uncertain, stop implementation in that area and re-establish ground truth rather than taking a shortcut.
