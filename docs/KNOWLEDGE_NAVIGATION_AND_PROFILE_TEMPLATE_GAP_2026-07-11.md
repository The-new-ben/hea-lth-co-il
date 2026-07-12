# Knowledge navigation and verified profile template gap

Observed: 2026-07-11  
Scope: public portal information architecture and future provider or clinic pages  
Implementation state: source foundation complete, not live or release-approved

## Competitor evidence

Mayo Clinic separates its global navigation into care, a health library, medical-professional resources, and research or education. Its doctor finder accepts a doctor name, condition or procedure, and location, then offers browse alternatives. [Mayo Clinic home](https://www.mayoclinic.org/) and [Mayo doctor finder](https://www.mayoclinic.org/appointments/find-a-doctor)

The Clalit and Maccabi home pages did not return crawlable content to this research session because their bot-protection or fetch controls rejected the request. Existing local visual evidence remains a comparison reference, but this document does not claim a fresh DOM observation of either site.

## Decision

| Capability | Competitor signal | Hea-lth source change | Proof still required |
| --- | --- | --- | --- |
| Information-library navigation | Health content is a first-class navigation domain, not a footer-only link. | Added a third accessible mega menu for guides, glossary, anatomy discovery, and health technology. | Fresh desktop and mobile Chrome screenshots after a tab is attached. |
| Provider discovery | A professional finder combines name or clinical intent with location and alternate browsing paths. | Directory remains the canonical controlled search entry; profile pages link visitors back to its care-discovery flow. | Verified inventory, local search behaviour, and measured user testing. |
| Individual public profile | Trust depends on clear factual profile fields and a safe appointment path. | Added reusable provider and clinic templates that expose only verified public fields and never lead-routing data. | Explicit release decision to enable public CPT routes, privacy review, and populated verified records. |

## Source boundaries

- `single-hp_provider.php` and `single-hp_clinic.php` are dormant presentation shells. The core still registers those content types as non-public.
- The shared profile template returns a 404 for any record without `hp_public_state=verified`.
- It reads only city, languages, accessibility, last-verified date, public disclosure, public taxonomy terms, editorial content, and a public thumbnail.
- It does not read commercial status, route priority, sponsorship configuration, consent configuration, CRM fields, payments, contact-owner data, or private provider information.
- The page contains no intake form. Its CTA returns to the governed care-discovery route without soliciting medical information.

## Design intent

The profile visual system is deliberately quieter than the portal homepage: one clear identity block, factual tags, a visible verification state, optional disclosure, and an information boundary. This follows the competitor's information architecture without copying its prose or making endorsements that the underlying data cannot prove.

## Acceptance evidence

1. `profile-and-knowledge-navigation-contract-test.php` passes.
2. Full local theme and plugin contract suite passes.
3. The public CPT release remains disabled until the provider inventory and privacy gates have evidence.
4. A current Chrome desktop and mobile screenshot review must confirm navigation, heading wrap, RTL order, and 44px-plus touch actions before release approval.
