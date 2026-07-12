# Hea-lth build foundation status and release gates

Updated: 2026-07-11  
Scope: source foundation only. Nothing in this table is a live-site claim.

## Outcome status

| Dimension | State | Current evidence | What would prove the next state |
| --- | --- | --- | --- |
| Premium Hebrew design system | In progress | Token-based theme CSS, Hebrew typography, responsive templates, header, footer, rich homepage and local render matrix. | Fresh desktop and mobile Chrome review plus approved Figma source. |
| Homepage and core discovery | Validated locally | Homepage, menus, care navigator, guides gate, directory gate, anatomy teaser and professional route render from actual theme source. | Chrome screenshots and real usability observations. |
| Treatment and information templates | In progress | Treatment hub, care-discovery route, reviewed editorial template, glossary, future treatment detail shell, and contract tests. | Approved medical source packs and reviewed records. |
| Provider and clinic experience | In progress | Verified directory browser, future profile shells, fact-only public-profile contract, map release gate. | Verified inventory, public-profile release decision, privacy review, current browser evidence. |
| Personal account | Foundation | Session-aware entry page with no health-data collection or simulated account data. | Approved identity service, privacy model, retention policy, and account feature owner. |
| 3D anatomy | Evidence-gated | Self-hosted Three engine, semantic resolver, accessible fallback, model registry, local fixture tests. | Licensed asset provenance, clinical review, semantic mapping, performance and visual acceptance. |
| Lead-routing boundary | Validated locally | Internal non-PII resolver, recipient verification, capacity checks, consent and disclosure review fields, audit screen. | Approved consent-first intake, CRM integration, recipient SLA, retention and failure-handling proof. |
| SEO architecture | Foundation | Keyword-to-URL map, canonical route registry, content-feed review gates, no-cannibalization contracts. | Fresh GSC, Semrush, Israeli SERP evidence, migration map, and approved content briefs. |
| Deployment and live release | Not started for this slice | No activation, upload, push, pull, or live WordPress mutation occurred. | Approved artifact, visual acceptance, backup/rollback, deployment evidence, live verification. |

## Source and local evidence collected this session

- Added a third accessible mega menu for guides, glossary, anatomy discovery, and health technology.
- Added future verified provider and clinic profile templates. They expose only typed public facts and return 404 for an unverified record.
- Added `/find-care/` as a selectable WordPress template that routes visitors to approved information and directory destinations without a form or data transmission.
- Added future treatment detail templates. They render only after the shared editorial gate reports approved state, review date, source note, and nonempty body content.
- Added template contract tests for knowledge navigation, public profiles, care discovery, and treatment detail.
- Verified the local source matrix on twelve routes, each with HTTP 200, exactly one `main`, and exactly one `h1`. The two individual profile and treatment previews use visibly marked local-only fixtures, never publication data.
- Full local suite passed: 20 PHP contracts and 2 JavaScript interaction contracts.

## Red alerts and containment

| Alert | Impact | Containment | Missing proof |
| --- | --- | --- | --- |
| No current Chrome screenshots of the latest source | Design and mobile quality cannot be called validated. | No release is proposed. | Attach a Chrome Profile 3 tab to this task, then capture local desktop, tablet, and mobile comparison evidence. |
| Figma Starter quota unavailable | The source design has no approved editable Figma system yet. | Source uses tokens and reusable components; no design sign-off claim. | Figma quota or an authorized Figma workspace. |
| 3D asset rights and clinical provenance absent | Cannot deliver a public anatomical model. | Engine remains gated and no asset is packaged for release. | Supplier rights, attribution, clinical and semantic manifests, performance review. |
| No CRM or consent-first intake approval | No live lead handoff can safely begin. | Care discovery is navigation only; internal resolver receives no visitor data. | CRM owner, retention policy, consent language, recipient SLA, test environment. |
| Fresh GSC, Semrush and Israeli SERP evidence not captured in this task | Cannot claim keyword opportunity, ranking position, or URL migration safety. | URL registry preserves existing powered paths. | Browser access or exports and Wave 1 localized research record. |

## Next three priorities

1. Attach Chrome Profile 3 and capture fresh side-by-side local Hea-lth, Clalit and global benchmark evidence. Inspect all visible text, desktop/mobile menu states, card density, footer, account, directory, care-discovery, and anatomy paths.
2. Restore Figma access and create the token library plus large responsive screens from the actual source, not an AI-only mockup.
3. Use logged-in Google Search Console and Semrush to finish the first high-value Hebrew keyword and URL evidence pack before any content brief or redirect decision.

## Release prohibition

The source is not authorized for WordPress activation or deployment. Release remains blocked until the applicable visual, SEO, medical, data, 3D, lead, and deployment gates above have direct evidence.
