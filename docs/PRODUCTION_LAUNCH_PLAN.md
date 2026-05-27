# hea-lth.co.il Production Launch Plan

Date: 2026-05-27
Owner: Codex acting as operator
Launch rank in portfolio: 5, after `robbottx.com`, `nad-lan.co.il`, `betterlaw.co.il`, and `dubai-team.co.il`

## Executive Decision

Hea-lth should launch as a Hebrew private-health service coordination funnel, not as a broad medical-advice site.

The first revenue wave should focus on appointment coordination, second opinions, imaging coordination, insurance-refund assistance, doctor home visits, and premium health-service concierge. These can monetize without pretending to diagnose, treat, recommend a procedure, or replace a licensed clinician.

## Research Inputs

- Google people-first content guidance, including stronger expectations for health/YMYL topics: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Google organization structured data guidance: https://developers.google.com/search/docs/appearance/structured-data/organization
- Ministry of Health second-opinion guidance: https://me.health.gov.il/older-adult/services-rights/second-opinion/
- Ministry of Health licensed practitioner lookup: https://www.gov.il/en/service/licensed-medical-practitioners
- Ministry of Health health registries service: https://www.gov.il/he/service/health-registries
- Ministry of Health patient-rights material on consent and privacy: https://me.health.gov.il/mental-health/information-and-updates/managing-sexual-abuse/sensitive-care/
- Israel Privacy Protection Authority database notice service: https://mojforms.justice.gov.il/mojaemprivacyprotectionauthority/noticeobligation.html
- uPress sandbox/import reference used in staging: https://support.upress.io/dev/import-to-sandbox/

## Production Goal

Within 30 days of production activation:

- Publish one trust-first homepage and six service pages.
- Route every inquiry into the private `health_lead` CRM.
- Capture service category, specialty, city, urgency, payer route, insurance provider, preferred route, UTM, source page, privacy acknowledgement, and consent.
- Convert qualified leads into paid coordination, appointment booking, second-opinion facilitation, imaging coordination, refund-support help, home-visit coordination, or premium concierge packages.
- Avoid medical advice, diagnosis, emergency triage, treatment suitability claims, and unsupported health outcomes.

## First Money Pages

These drafts already exist in WordPress. They should remain draft until medical/privacy/legal review is complete.

| Priority | Slug | Commercial intent | Production angle |
| --- | --- | --- | --- |
| 1 | `/medical-second-opinion/` | Patient wants another medical opinion before a decision | High-trust second-opinion coordination, document-preparation checklist, funding/reimbursement pointers, and licensed-provider verification |
| 2 | `/private-doctor-appointment/` | User wants a private specialist appointment | Service routing by specialty, city, urgency, payer type, and provider availability |
| 3 | `/mri-ct-appointment/` | Imaging appointment need | Coordination for MRI/CT availability, referral/document readiness, payer route, and no clinical interpretation |
| 4 | `/health-insurance-refund/` | User paid or plans to pay privately | Assistance route for SHABAN/private insurance reimbursement checks without promising coverage |
| 5 | `/doctor-home-visit/` | Non-emergency home medical visit | Home-visit coordination for non-emergency situations only, with clear emergency routing |
| 6 | `/premium-health-services/` | High-touch health navigation | Concierge coordination for appointments, documents, provider matching, reminders, and payment/refund follow-up |

Do not publish condition pages, treatment pages, medication pages, or symptom-triage content in the first production wave.

## Keyword Direction

First clusters:

- `חוות דעת רפואית שנייה`
- `רופא פרטי`
- `תור לרופא מומחה`
- `רופא מומחה פרטי`
- `MRI פרטי`
- `CT פרטי`
- `החזר ביטוח בריאות`
- `ביקור רופא בבית`
- `שירותי בריאות פרטיים`
- `ניהול תהליך רפואי פרטי`

Prioritize pages where the user is already seeking a service. Avoid chasing symptom keywords until a named medical reviewer, author system, source policy, and update process are live.

## Medical And Privacy Gates

Health is the strictest YMYL site in the portfolio.

Before publishing:

- A licensed medical professional must review every service page and any text that explains medical decisions, urgency, procedures, or patient rights.
- Verify providers through the Ministry of Health license/registry tools before listing, recommending, or routing to them.
- Add reviewer name, role, license/specialty basis where appropriate, and review/update date to every medical explainer.
- Do not say or imply that a specific service is medically suitable for a user.
- Do not promise faster diagnosis, better treatment, refund approval, medical outcome, insurance payment, or appointment availability.
- Do not collect medical documents through the first form.
- Do not send medical details in plaintext email.
- Keep emergency language visible on every high-intent page.
- Review privacy/database obligations before collecting health-sensitive information at scale or integrating third-party CRM, analytics, or automation tools.

## CRM And Revenue Workflow

Current CRM object: private `health_lead` custom post type.

Current statuses:

1. `new`
2. `triage_needed`
3. `needs_clinical_review`
4. `provider_match`
5. `appointment_requested`
6. `refund_route`
7. `closed_lost`

Operational rules:

- Response target: same business day for standard service requests.
- Any urgent or ambiguous medical situation routes to emergency/doctor guidance, not site-level triage.
- If the user describes symptoms or treatment decisions, mark `needs_clinical_review` before routing.
- Use `refund_route` only for administrative insurance/refund handling, not coverage promises.
- Provider handoff must log provider identity, license verification date, source page, and commercial terms.
- Do not automate medical advice emails or chatbot replies.

Revenue handling:

- Paid coordination package: Grow checkout plus Green Invoice after service terms are approved.
- Second-opinion facilitation: charge for administrative coordination or concierge support, not for medical judgment unless delivered by the licensed provider.
- Imaging coordination: charge only for coordination/support if legally and commercially approved.
- Insurance-refund assistance: charge for administrative help or success-fee model only after terms and legal/privacy review.
- Provider/referral revenue: activate only after written partner terms, license verification, privacy responsibilities, and disclosure language are documented.

## SEO Architecture

- Homepage targets private-health service coordination in Hebrew and links to the six service pages.
- `/premium-health-services/` acts as the broad concierge pillar.
- `/medical-second-opinion/`, `/private-doctor-appointment/`, and `/mri-ct-appointment/` are the highest-value acquisition pages.
- `/health-insurance-refund/` supports payer-route and reimbursement questions.
- `/doctor-home-visit/` must be explicitly non-emergency and should link to emergency guidance.
- Every service page must include:
  - emergency notice
  - "not medical advice" disclosure
  - what the site can coordinate
  - what only a licensed professional can decide
  - what information not to send in the first form
  - source/reviewer/update block
- Add `Organization` or `LocalBusiness` schema only after confirming official business details.
- Avoid medical-condition schema, review/rating markup, or physician profile markup until verified provider data and compliance review are complete.

## Production Activation Steps

1. Confirm private GitHub to uPress Git sync credential method. Current blocker: do not embed a GitHub token into uPress clone URLs without explicit approval for that exact method.
2. Re-import production into staging if more than 7 days have passed since the last staging import.
3. Confirm `Health Revenue` activates cleanly after the latest import.
4. Review existing WooCommerce, SEO, and health plugins from the live site. Do not disable or remove production plugins without separate review.
5. Run a staging form test and confirm CRM row, notification email without medical details, success URL, UTM capture, privacy acknowledgement, and consent.
6. Add medical reviewer/source/update sections to the six draft pages.
7. Review privacy/database obligations and update privacy policy before collecting broader health-sensitive data.
8. Deploy to production through the approved uPress path.
9. Activate the theme in a low-traffic window.
10. Publish only the medically/privacy-reviewed service pages.
11. Confirm production CRM with one internal test lead that contains no sensitive medical detail.
12. Remove or noindex staging/dev URLs and submit sitemap/recrawl through Search Console.

## Go/No-Go Checklist

Go only when all are true:

- Staging theme and CRM are verified after the latest import.
- Production backup exists in uPress.
- Medical reviewer has approved published service copy.
- Privacy policy and data-handling responsibilities are updated.
- No diagnosis, treatment, dosage, urgency, cure, outcome, or refund promises are present.
- Provider license verification process is documented.
- CRM notification does not expose medical details in email.
- Search Console property and sitemap are ready.
- Payment/invoice path is configured only for approved coordination offers.

No-go if any are true:

- A page gives unreviewed medical advice or symptom triage.
- A provider is listed or routed without license verification.
- The first form asks users to upload or paste sensitive medical records.
- The site cannot test lead capture end to end.
- uPress deployment requires unapproved token exposure.

## First 14-Day Operating Rhythm

Daily:

- Check `Health Leads`.
- Mark every lead status and next action.
- Move symptom-heavy or clinical-decision leads to `needs_clinical_review`.
- Confirm no medical details are flowing into plaintext email.

Twice weekly:

- Improve one service page based on real coordination questions.
- Add one official-source note or reviewer clarification.
- Review Search Console indexing and form errors.

Weekly:

- Review conversion by service category and payer route.
- Decide whether paid ads should start for one service only, most likely second opinion or private specialist appointments.
- Review Grow/Green Invoice package terms for paid coordination.

## Open Blockers

- Private GitHub to uPress Git sync needs approved secret handling.
- Medical reviewer workflow and reviewer identity are not yet configured.
- Privacy/database obligations need review before scaling health-sensitive lead capture.
- Provider/referral terms and license-verification workflow are not finalized.
- Search Console access and production sitemap state should be confirmed.
- Grow/Green Invoice products for health coordination packages are not configured yet.
