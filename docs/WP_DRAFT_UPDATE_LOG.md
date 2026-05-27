# Hea-lth WordPress Draft Update Log

Date: 2026-05-27
Owner: Codex acting as operator
Site: `hea-lth.co.il`
Method: WordPress REST API via encrypted local application password helper
Status: all pages remain `draft`

## 2026-05-27 Private-Health Page Upgrade

Updated the six draft service pages from `docs/MONEY_PAGE_BRIEFS.md`.

| ID | Slug | Status | Updated title |
| --- | --- | --- | --- |
| 610 | `private-doctor-appointment` | draft | `תיאום תור לרופא פרטי לפי התמחות, אזור ומסלול תשלום` |
| 611 | `medical-second-opinion` | draft | `חוות דעת רפואית שנייה - תיאום מסודר מול רופא מומחה` |
| 612 | `mri-ct-appointment` | draft | `תיאום MRI או CT פרטי - בדיקת מסלול לפני קביעת תור` |
| 613 | `health-insurance-refund` | draft | `עזרה בבדיקת מסלול החזר על שירות רפואי פרטי` |
| 614 | `doctor-home-visit` | draft | `ביקור רופא בבית למצבים שאינם חירום - תיאום שירות פרטי` |
| 615 | `premium-health-services` | draft | `שירותי בריאות פרטיים בתיאום אישי - תורים, מסמכים והחזרים` |

## What Changed

- Rebuilt the six pages as private-health coordination assets, not medical advice pages.
- Added a top emergency notice on every page: emergency symptoms and danger to life should route to MDA 101 or ER, not the website.
- Added clear service boundary language: Hea-lth coordinates administrative/private-service options and does not diagnose, prescribe, interpret tests, triage symptoms, recommend treatment, or replace a licensed clinician.
- Added privacy-safe first-contact rules: no medical documents, test results, medication lists, diagnoses, or full medical records in the first form.
- Added CRM-ready CTA routing to `/#lead`.
- Added field tables aligned to the private `health_lead` workflow: source page, service category, city/region, specialty, urgency label, payer route, insurance/provider route, consent, and privacy acknowledgement.
- Added no-guarantee language around availability, medical suitability, treatment results, reimbursement, claim approval, imaging approval, home-visit suitability, and paid concierge outcomes.
- Added source/reviewer/update blocks and explicit publish gates for medical, legal, and privacy review.

## Page-Specific Notes

### `medical-second-opinion`

Commercial role: paid second-opinion coordination package and provider referral only after written terms and disclosure.

Added:

- Ministry of Health second-opinion framing.
- Administrative routing by specialty, region, payer route, and desired timing.
- No promise of different diagnosis, better treatment, specialist availability, or reimbursement.
- Warning not to upload medical documents in the first form.

### `private-doctor-appointment`

Commercial role: paid private-appointment coordination and provider handoff after license/terms verification.

Added:

- Specialty, city, time-window, and payer-route qualification.
- Ministry of Health license-verification requirement before any named provider handoff.
- No provider recommendation, ranking, or medical suitability promise.

### `mri-ct-appointment`

Commercial role: imaging coordination package or approved partner handoff.

Added:

- Administrative readiness checks: MRI/CT/not sure, referral exists/not sure, region, timing, payer route.
- Clear statement that only a doctor or provider can decide suitability, urgency, protocol, contrast, and interpretation.
- No claim that imaging is medically appropriate or faster diagnosis will result.

### `health-insurance-refund`

Commercial role: administrative refund-support fee or approved model after legal/privacy review.

Added:

- Difference between payer routes in administrative terms.
- Refund-route CRM status language.
- No eligibility, amount, approval, legal, or insurance-advice promise.
- No document collection before an approved privacy path.

### `doctor-home-visit`

Commercial role: non-emergency private home-visit coordination or approved provider referral.

Added:

- Emergency warning at the top and non-emergency boundary.
- Non-emergency confirmation checkbox concept.
- No promise of emergency response, home-visit suitability, treatment, arrival, price, or refund.

### `premium-health-services`

Commercial role: paid concierge coordination package through Grow/Green Invoice after service terms are approved.

Added:

- Internal hub linking to second opinion, private doctor, MRI/CT, refund, and home-visit pages.
- Clear separation between administrative coordination and medical case management.
- Payment gate: no paid package until scope, cancellation policy, privacy responsibility, and invoicing flow are approved.

## Research Anchors Used

- Google helpful content / people-first content: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Google Search ranking systems, reliable information systems: https://developers.google.com/search/help/helpful-content-faq
- Google Search Essentials: https://developers.google.com/search/docs/essentials
- Ministry of Health second opinion guidance: https://me.health.gov.il/en/older-adult/services-rights/diagnoses/second-opinion/
- Ministry of Health licensed practitioner lookup: https://www.gov.il/en/service/licensed-medical-practitioners
- Israel Privacy Protection Authority notice obligations: https://mojforms.justice.gov.il/mojaemprivacyprotectionauthority/noticeobligation.html
- National Insurance Institute reimbursement context: https://www.btl.gov.il/English%20Homepage/Insurance/Health%20Insurance/Pages/Reimbursement.aspx

## Verification

REST verification returned these pages as `draft` after the UTF-8 repair and repost:

- `private-doctor-appointment`, modified `2026-05-27T05:14:04`
- `medical-second-opinion`, modified `2026-05-27T05:14:04`
- `mri-ct-appointment`, modified `2026-05-27T05:14:05`
- `health-insurance-refund`, modified `2026-05-27T05:14:05`
- `doctor-home-visit`, modified `2026-05-27T05:14:05`
- `premium-health-services`, modified `2026-05-27T05:14:04`

Final REST output returned proper Hebrew titles from WordPress. The shared helper was patched to read JSON bodies with `-Encoding UTF8` before the final repost.

## Next Steps

1. Verify the lead form captures source page, service category, city/region, urgency label, payer route, insurance provider, UTM fields, consent, and privacy acknowledgement.
2. Confirm medical reviewer and privacy owner before publication.
3. Confirm provider license-check workflow and partner/referral disclosures before any named handoff.
4. Prepare Grow/Green Invoice products only after paid package terms, cancellation policy, and privacy responsibilities are approved.
5. Keep all pages as draft until medical/legal/privacy review is complete.
