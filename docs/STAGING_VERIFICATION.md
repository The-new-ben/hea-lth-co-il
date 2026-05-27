# Health staging verification

Date: 2026-05-27

## Scope

- Site: `hea-lth.co.il`
- Staging: `hea-lth-co-il-rev.s1086.upress.link`
- Theme folder: `wp-content/themes/health-revenue`
- Active staging theme: `Health Revenue`
- Execution excludes `jus-tice.co.il`; that site remains a reference pattern only.

## Research inputs

- Google people-first content guidance, including stronger YMYL expectations for health topics: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Google organization/local-business structured data reference: https://developers.google.com/search/docs/appearance/structured-data/organization
- uPress sandbox import flow: https://support.upress.io/dev/import-to-sandbox/
- uPress file manager Git flow: https://support.upress.io/tag/manage-git/

## Deployment proof

- Created a fresh uPress development environment with suffix `rev`.
- Imported the live `hea-lth.co.il` WordPress site into staging.
- Uploaded and activated the code-first theme from this repository.
- Confirmed `home` and `siteurl` are set to `http://hea-lth-co-il-rev.s1086.upress.link` in staging.
- Strengthened the anti-spam honeypot clipping rule.
- Confirmed the front page shows the health lead funnel and success notice at `/?lead=received`.

## CRM proof

- Lead post type: `health_lead`
- Admin screen: `/wp-admin/edit.php?post_type=health_lead`
- Internal test lead: `בדיקת Codex סטייג׳ינג – חוות דעת שנייה – 2026-05-27`
- Phone: `050-000-0000`
- Service: `חוות דעת שנייה`
- Urgency: `השבוע`
- Status column: `New`

## Medical guardrails

- The funnel is for coordination and qualification only.
- The form copy tells users not to send sensitive medical information at the initial stage.
- The page includes emergency language that directs urgent medical situations to MDA 101 or the emergency room.
- Content expansion should follow `docs/MEDICAL_GUARDRAILS.md` and require medical review before any condition/treatment advice is published.

## Production cautions

- The staging site includes WooCommerce and several SEO/health plugins from the live site. Do not remove or disable existing production plugins without a separate review.
- uPress Git manager accepts HTTPS clone URLs. Because the GitHub repository is private, uPress Git sync still needs an approved credential approach. Do not embed a GitHub token in uPress until the owner approves that exact method.

