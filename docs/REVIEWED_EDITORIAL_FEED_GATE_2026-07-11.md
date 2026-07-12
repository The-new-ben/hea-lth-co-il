# Reviewed editorial-feed gate, 2026-07-11

**Status:** Implemented and locally tested in source. No posts, URLs, metadata,
or WordPress settings were changed on the live site.

## Red alert discovered

The guide and glossary templates told visitors that their cards represented
approved, reviewed, source-backed content. Their original `WP_Query` calls
required only `post_status=publish`. A published legacy post could therefore
appear inside a premium medical knowledge feed without the explicit editorial
metadata promised by the interface.

This was a trust and YMYL-content red alert. It did not justify deleting or
redirecting any existing URL.

## Containment implemented

1. A single shared query gate requires `hp_editorial_state=approved`, a
   non-empty `hp_last_reviewed`, and a non-empty `hp_source_note`.
2. The guide index now calls the shared reviewed-guide query. It can render up
   to nine records on the index and remains capped at twelve records.
3. The glossary index preserves its existing `post` URLs in the `glossary`
   category while applying the same shared gate. The internal `hp_glossary`
   type remains non-public until migration evidence exists.
4. Guide and glossary cards disclose their review date and source note. No
   placeholder reviewer, source, medical claim, or article is created.
5. Individual legacy articles remain reachable. Their page template now tells
   visitors whether the review metadata is complete instead of implying a
   completed review. A reviewed record shows its review date and source note.

## Why this is aligned with search and content quality

Google's current people-first content guidance emphasizes accurate sourcing,
clear authorship or expertise signals where expected, and additional weight for
trust on health topics. It also cautions against mass-produced or search-engine
first content. [Google helpful, reliable, people-first content](https://developers.google.com/search/docs/fundamentals/creating-helpful-content)

The WordPress query implementation uses explicit published status, post type,
and meta conditions, which are supported by the documented `WP_Query` API.
[WordPress `WP_Query` reference](https://developer.wordpress.org/reference/classes/wp_query/)

## Evidence

- `theme-src/hea-lth-portal/inc/portal-template-helpers.php`
- `theme-src/hea-lth-portal/page-templates/template-guides.php`
- `theme-src/hea-lth-portal/page-templates/template-glossary.php`
- `theme-src/hea-lth-portal/single.php`
- `tooling/tests/reviewed-guide-feed-test.php`
- `tooling/tests/reviewed-glossary-feed-test.php`
- `tooling/tests/single-editorial-status-contract-test.php`

## Still required before publication at scale

1. An approved keyword-to-URL row and localized SERP evidence for each
   individual medical page.
2. Claim-level medical evidence, named author and reviewer ownership, and a
   review/update date for every public medical statement.
3. Existing-URL inventory, content-equivalence decision, redirect plan, and
   post-launch monitoring before any route changes.
4. Chrome visual and accessibility evidence for cards at desktop and mobile
   breakpoints.
