# Homepage discovery and reviewed-content competitor note

## Evidence reviewed on 2026-07-11

### RealSelf

The current [RealSelf homepage](https://www.realself.com/) exposes a clear discovery stack: Procedures, Find a Provider, Photos, Reviews, and Q&A. It pairs that stack with procedure cards, provider discovery, reviews, answers, consumer editorial, a commercial product module, and professional acquisition paths such as claim profile and advertise.

Its current trust section separates real doctors, real reviews, real results, and real answers. It says doctor licences are vetted, reviews are vetted, and patient photos are shared by qualified doctors. Those are operating claims backed by a mature moderation and verification system, not just decorative homepage cards.

## Implication for Hea-lth

Hea-lth should compete on the same visitor jobs without inventing public evidence:

| RealSelf visitor job | Hea-lth foundation now | Required before an equivalent public module |
| --- | --- | --- |
| Find a procedure | Topic centers, search, treatment-hub URLs, and anatomy resolver | Reviewed content and treatment taxonomy |
| Find a provider | Directory foundation and verification-gated public API | Verified provider records, mapping, routing SLA |
| Assess trust | Source, date, editorial, and verification rules | Review policy, evidence ledger, moderation workflow |
| Read answers | Guides and glossary architecture | Medical review, source citations, publication owner |
| Explore products | Equipment and technology data types | Regulated-product review, seller disclosure, catalog and commerce policy |
| Professional acquisition | Professionals landing path and commercial operating model | Real eligibility, disclosure, billing, and support process |

The present build must not copy RealSelf's review counts, costs, patient photographs, provider records, or answer format. Those require source rights, moderation, medical review, and local legal review.

### Clalit

The live [Clalit homepage](https://www.clalit.co.il/he/Pages/default.aspx) could not be re-read through this research session because its protection layer returned an Incapsula incident response. The existing real-browser visual reference is retained at:

`output/visual-regression/2026-07-11/clalit-home-desktop-top.png`

The local comparison uses the evidence already captured rather than treating the access failure as permission to infer fresh page details. A new live comparison must be captured once the Chrome Profile 3 connection is attached.

## Implementation completed in this slice

1. Added review metadata to existing WordPress `post` and `page` records as well as new Hea-lth entities. This preserves powered URLs while allowing a staged editorial migration.
2. Replaced illustrative homepage guide cards with a reviewed-guide feed that requires all of the following before rendering a content card:
   - published WordPress post;
   - `hp_editorial_state = approved`;
   - non-empty review date;
   - non-empty source note.
3. Added a deliberate empty state that explains why cards are not yet displayed instead of creating fictional articles.
4. Added evidence metadata to rendered cards, including the review date and public source note.
5. Added a local regression test that proves the query conditions and escaping behavior.

## Acceptance criteria before live activation

- At least three reviewed records have a named source, a review date, an editorial owner, and a stable existing URL.
- Card titles, excerpts, and source notes are checked in Hebrew desktop and mobile layouts.
- Each content record passes the medical, legal, and editorial approval process appropriate to its topic.
- A Chrome screenshot compares the rendered source against the latest accessible local and international references.
- No public content card is populated through placeholder or generated medical copy.

## Sources

- [RealSelf homepage](https://www.realself.com/)
- [Clalit homepage](https://www.clalit.co.il/he/Pages/default.aspx)
