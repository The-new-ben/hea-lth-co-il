# Treatment template and editorial gate

Observed: 2026-07-11  
Status: source implemented, public treatment routing remains disabled

## Competitive signal

Mayo Clinic's tests and procedures index frames each topic around what it is, how it is done, preparation, risks, and results, and then supports discovery by topic. It also publishes a visible sponsorship policy. Hea-lth should match the expectation for source-led, structured clinical information while preserving the Israeli URL map and its own medical-review gate. [Mayo tests and procedures](https://www.mayoclinic.org/tests-procedures)

## Implementation decision

The new `single-hp_treatment.php` and shared treatment partial are dormant presentation templates. They do not turn the `hp_treatment` post type public. They require all of the following before any public rendering:

1. Post type is `hp_treatment`.
2. `hp_editorial_state` is approved.
3. `hp_last_reviewed` is present.
4. `hp_source_note` is present.
5. The record contains a nonempty editorial body.

If any condition fails, the route is returned as a 404 and no title, taxonomy, content, or commercial data is rendered.

## Public surface

- Visible evidence: review date and source note.
- Visible taxonomy: specialty, service type, and body region only when records exist.
- Visible next steps: care-discovery and directory navigation.
- Not visible: route priority, sponsorship state, consent configuration, recipient, CRM identifiers, pricing, availability, payment, and private provider data.
- No built-in form, booking promise, provider recommendation, treatment suitability claim, result claim, or fabricated FAQ.

## Evidence still needed for release

1. An approved Hebrew content brief and clinician-reviewed source pack for every treatment record.
2. A URL-by-URL migration decision based on Search Console, existing inventory, backlinks, and localized SERP research.
3. Visual Chrome review at desktop and mobile using genuine approved content, not placeholder clinical prose.
4. A transparent sponsored-content policy before any commercial promotion is attached to a treatment page.
