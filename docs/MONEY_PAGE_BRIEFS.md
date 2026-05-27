# Hea-lth Money Page Briefs

Date: 2026-05-27
Owner: Codex acting as operator
Site: `hea-lth.co.il`
Status: execution brief for draft service pages. Do not publish before medical, privacy, and legal review.

## Operating Decision

Hea-lth should sell private-health coordination, not medical judgment.

The money comes from urgent, high-intent users who are already looking for help navigating the private health system: second opinions, private specialist appointments, MRI/CT coordination, reimbursement help, non-emergency home visits, and premium concierge service.

The site must not diagnose, prescribe, triage symptoms, interpret tests, promise treatment results, promise refund approval, or imply that the platform replaces a licensed clinician.

## Current Research Anchors

- Google helpful content guidance: health and safety topics require strong people-first content and strong E-E-A-T signals: https://developers.google.com/search/docs/fundamentals/creating-helpful-content
- Google organization structured data guidance: add organization data only after confirming real business identity: https://developers.google.com/search/docs/appearance/structured-data/organization
- Ministry of Health licensed practitioner lookup: verify provider license and specialty before routing or listing providers: https://www.gov.il/en/service/licensed-medical-practitioners
- Ministry of Health health registries: use official registries for licensed professionals, organizations, and medical products where relevant: https://www.gov.il/he/service/health-registries
- Ministry of Health second-opinion guidance: patients may seek a second opinion, and private insurance or SHABAN reimbursement should be checked without promising coverage: https://me.health.gov.il/en/older-adult/services-rights/diagnoses/second-opinion/
- Privacy Protection Authority notice obligations: review database/privacy duties before scaling sensitive health lead collection: https://mojforms.justice.gov.il/mojaemprivacyprotectionauthority/noticeobligation.html

## Shared Page Rules

Every page must include:

- Clear emergency notice: in emergency, chest pain, shortness of breath, stroke signs, severe bleeding, loss of consciousness, or danger to life, call MDA 101 or go to ER.
- Clear boundary: the site coordinates administrative/private-service options and does not provide medical advice.
- No first-step upload of medical documents.
- No request to paste diagnosis, test results, medication list, or medical record into the first lead form.
- Privacy acknowledgement and consent checkbox before sending.
- Source/reviewer/update block before publication.
- CTA framed as coordination request, not medical recommendation.
- CRM hidden fields: source page, service category, city, specialty, urgency label, payer route, insurance provider, UTM source/medium/campaign, consent, privacy acknowledgement.

Forbidden claims:

- "תור מובטח", "אבחון מהיר יותר", "הטיפול הטוב ביותר", "החזר מובטח", "רופא מומלץ במיוחד", "מתאים למצב שלך", "נחסוך לך זמן קריטי", or any similar medical/outcome promise.
- Provider comparison, ranking, or recommendation before license verification and partner disclosure are documented.
- Any chatbot/email that answers clinical questions without medical reviewer workflow.

## CRM Status Routing

Use the existing private `health_lead` workflow:

| Lead type | First CRM status | Next operator action |
| --- | --- | --- |
| Appointment request with no symptoms shared | `triage_needed` | Confirm service category, city, payer route, and whether user only wants coordination. |
| Second opinion request | `needs_clinical_review` | Ask only administrative questions first; do not request documents until privacy path is approved. |
| MRI/CT request | `triage_needed` | Confirm referral/document readiness and payer route; do not interpret urgency or test results. |
| Refund/reimbursement request | `refund_route` | Collect payer/admin details only; do not promise eligibility or approval. |
| Home visit request | `triage_needed` | Confirm non-emergency framing; emergency cases route to MDA 101/ER. |
| Premium concierge request | `provider_match` | Prepare paid coordination offer after terms and privacy approval. |

## Page 1: `/medical-second-opinion/`

WordPress draft ID: 611
Priority: 1
Commercial intent: patient or family wants another professional opinion before a decision.
Revenue path: paid second-opinion coordination package, provider partner referral only after written terms and disclosure.

Primary keyword:

- חוות דעת רפואית שנייה

Supporting keywords:

- חוות דעת שנייה רופא מומחה
- חוות דעת שנייה פרטי
- חוות דעת שנייה ביטוח בריאות
- ייעוץ רפואי נוסף
- רופא מומחה חוות דעת שנייה

Searcher problem:

The user is worried about a diagnosis, surgery, treatment choice, or major health decision and wants help finding the right administrative route to a licensed specialist.

Page promise:

We help organize the process: specialty match, appointment options, document checklist for the provider, payer/refund route, reminders, and handoff coordination.

Do not promise:

- Different diagnosis.
- Better treatment decision.
- Specialist availability.
- Insurance reimbursement.
- That the second opinion is medically necessary.

Recommended H1:

`חוות דעת רפואית שנייה - תיאום מסודר מול רופא מומחה`

Required sections:

- When people usually look for a second opinion, citing Ministry of Health guidance.
- What Hea-lth can coordinate.
- What only the licensed doctor can decide.
- How private payment, SHABAN, and private insurance checks may work.
- What not to send in the first form.
- Emergency notice.
- Source/reviewer/update block.

CTA:

`בדיקת אפשרות לתיאום חוות דעת שנייה`

Lead form fields:

- Specialty requested.
- City/region.
- Payer route: private, SHABAN, private insurance, not sure.
- Desired timing: flexible, this week, urgent but not emergency.
- Consent/privacy acknowledgement.

Publish gate:

Medical reviewer must approve all phrasing around when a second opinion is relevant.

## Page 2: `/private-doctor-appointment/`

WordPress draft ID: 610
Priority: 2
Commercial intent: user wants a private appointment with a specialist.
Revenue path: paid appointment coordination, premium routing package, provider partner referral after license/terms verification.

Primary keyword:

- רופא פרטי

Supporting keywords:

- תור לרופא מומחה פרטי
- רופא מומחה פרטי
- תיאום תור לרופא פרטי
- רופא פרטי מהר
- רופא פרטי במרכז

Searcher problem:

The user needs help finding a private appointment route without wasting time calling clinics.

Page promise:

We coordinate options by specialty, city, availability window, and payer route.

Do not promise:

- Immediate appointment.
- Shorter wait than public system.
- Specific doctor recommendation.
- Medical suitability.

Recommended H1:

`תיאום תור לרופא פרטי לפי התמחות, אזור ומסלול תשלום`

Required sections:

- What information is needed to coordinate an appointment.
- How specialty/city/payer route affect the process.
- License verification note.
- What Hea-lth does not decide medically.
- Emergency notice.
- Privacy-safe first contact.

CTA:

`בקשת תיאום לרופא פרטי`

Lead form fields:

- Specialty.
- City/region.
- Preferred time window.
- Payer route.
- Phone/email.
- Consent/privacy acknowledgement.

Publish gate:

Provider routing workflow must include Ministry of Health license check date before any named provider handoff.

## Page 3: `/mri-ct-appointment/`

WordPress draft ID: 612
Priority: 3
Commercial intent: user has or expects a referral and wants imaging appointment coordination.
Revenue path: imaging coordination package, partner clinic handoff only after terms/privacy approval.

Primary keyword:

- MRI פרטי

Supporting keywords:

- CT פרטי
- תור ל-MRI פרטי
- תור ל-CT פרטי
- בדיקת MRI פרטית
- תיאום בדיקת הדמיה

Searcher problem:

The user needs help navigating imaging appointment options and payment routes.

Page promise:

We help check administrative readiness: referral, payer route, region, scheduling preferences, and handoff to relevant provider options.

Do not promise:

- Imaging approval.
- Clinical urgency.
- Result interpretation.
- Faster diagnosis.
- Test suitability.

Recommended H1:

`תיאום MRI או CT פרטי - עזרה אדמיניסטרטיבית לפני קביעת תור`

Required sections:

- Referral/document readiness without collecting documents in the first form.
- Payer route: private, SHABAN, private insurance, not sure.
- What the imaging provider/doctor decides.
- What Hea-lth can coordinate.
- Emergency notice.
- Privacy and consent note.

CTA:

`בדיקת אפשרות לתיאום MRI או CT`

Lead form fields:

- Test type: MRI, CT, not sure.
- Referral exists: yes/no/not sure.
- Region.
- Payer route.
- Preferred timing.
- Consent/privacy acknowledgement.

Publish gate:

No copy may imply the user should get MRI/CT or that a test is medically appropriate.

## Page 4: `/health-insurance-refund/`

WordPress draft ID: 613
Priority: 4
Commercial intent: user paid or plans to pay privately and wants reimbursement help.
Revenue path: administrative refund-support fee or approved success-fee model after legal review.

Primary keyword:

- החזר ביטוח בריאות

Supporting keywords:

- החזר ביטוח בריאות פרטי
- החזר על רופא פרטי
- החזר על חוות דעת שנייה
- החזר קופת חולים שירותי בריאות נוספים
- בדיקת זכאות להחזר רפואי

Searcher problem:

The user wants to understand what paperwork and payer route may be needed after private medical spending.

Page promise:

We help organize the administrative reimbursement route: payer type, receipt/invoice checklist, policy/SHABAN path, and follow-up tasks.

Do not promise:

- Eligibility.
- Refund amount.
- Claim approval.
- Legal/insurance advice unless reviewed by qualified professional.

Recommended H1:

`עזרה בבדיקת מסלול החזר על שירות רפואי פרטי`

Required sections:

- Difference between private insurance, SHABAN, and private payment in administrative terms.
- What documents may be requested later by payer/provider.
- Why first contact should not include sensitive medical files.
- What Hea-lth can organize.
- What payer decides.

CTA:

`בדיקת מסלול החזר אפשרי`

Lead form fields:

- Payer route.
- Service type.
- Insurance provider/fund.
- Already paid: yes/no.
- Need help before or after appointment.
- Consent/privacy acknowledgement.

Publish gate:

Legal/privacy review required before success-fee language or document collection.

## Page 5: `/doctor-home-visit/`

WordPress draft ID: 614
Priority: 5
Commercial intent: user wants non-emergency home doctor visit coordination.
Revenue path: home-visit coordination fee or provider referral after license/terms verification.

Primary keyword:

- ביקור רופא בבית

Supporting keywords:

- רופא עד הבית פרטי
- רופא בבית פרטי
- ביקור רופא פרטי בבית
- הזמנת רופא הביתה
- רופא עד הבית לא חירום

Searcher problem:

The user wants help finding a non-emergency private home-visit option.

Page promise:

We coordinate non-emergency availability options by city, patient age category, payer route, and provider availability.

Do not promise:

- Emergency response.
- Medical suitability for home visit.
- Faster care.
- Specific treatment at home.

Recommended H1:

`ביקור רופא בבית למצבים שאינם חירום - תיאום שירות פרטי`

Required sections:

- Prominent emergency warning at top and near form.
- Non-emergency boundary.
- What Hea-lth can coordinate.
- What the provider decides.
- License verification note.
- Privacy-safe first contact.

CTA:

`בקשת תיאום ביקור רופא בבית`

Lead form fields:

- City.
- Age category: adult, child, older adult.
- Timing preference.
- Payer route.
- Non-emergency confirmation checkbox.
- Consent/privacy acknowledgement.

Publish gate:

Emergency language must be reviewed and visible without scrolling on mobile.

## Page 6: `/premium-health-services/`

WordPress draft ID: 615
Priority: 6
Commercial intent: family or patient wants high-touch coordination across several private-health tasks.
Revenue path: paid concierge package through Grow and Green Invoice after service terms are approved.

Primary keyword:

- שירותי בריאות פרטיים

Supporting keywords:

- ניהול תהליך רפואי פרטי
- תיאום שירותי בריאות
- קונסיירז' רפואי
- ליווי רפואי פרטי
- עזרה בקביעת תורים רפואיים

Searcher problem:

The user does not want to manage calls, documents, appointments, payer routes, and reminders alone.

Page promise:

We provide administrative concierge coordination: route planning, appointment options, document checklist, reminders, payer/refund follow-up, and provider handoff records.

Do not promise:

- Medical case management by non-clinicians.
- Clinical recommendations.
- Better medical outcomes.
- Treatment planning.

Recommended H1:

`שירותי בריאות פרטיים בתיאום אישי - תורים, מסמכים והחזרים`

Required sections:

- What is included in concierge coordination.
- What is outside the service.
- Medical reviewer/provider boundaries.
- Privacy-safe workflow.
- Payment terms after scope approval.
- Emergency notice.

CTA:

`בדיקת התאמה לשירות תיאום אישי`

Lead form fields:

- Needed services: appointment, second opinion, imaging, refund, home visit, other.
- City/region.
- Preferred language.
- Payer route.
- Desired support level.
- Consent/privacy acknowledgement.

Publish gate:

Paid package terms, cancellation policy, privacy responsibilities, and Green Invoice/Grow flow must be approved before taking payment.

## Internal Link Architecture

- Homepage links above the fold to second opinion, private doctor appointment, MRI/CT, and premium service.
- `premium-health-services` links to all six pages and acts as the commercial pillar.
- `medical-second-opinion` links to `health-insurance-refund` and `private-doctor-appointment`.
- `mri-ct-appointment` links to `health-insurance-refund` and `premium-health-services`.
- `doctor-home-visit` links only to emergency guidance and premium service; avoid symptom-condition clusters.
- `health-insurance-refund` links back to second opinion, private doctor, MRI/CT, and premium service.

## Conversion Measurement

Track these fields weekly:

- Leads by page.
- Leads by service category.
- Payer route mix.
- City/region.
- Conversion to paid coordination.
- Provider handoff count.
- Lost reason.
- Privacy/medical review escalation count.
- Leads that included sensitive health information in free text.

First commercial target:

- 20 qualified leads in 30 days after production publish.
- 3 paid coordination packages or approved provider handoffs in 30 days.
- Zero unreviewed medical-advice responses.

## Next Implementation Steps

1. Update the six WordPress draft pages with the page-specific briefs.
2. Add reviewer/source/update blocks but keep pages as draft.
3. Add privacy-safe lead forms that do not collect medical documents.
4. Verify CRM field mapping for each page.
5. Confirm medical reviewer and privacy policy owner.
6. Prepare Grow/Green Invoice products only after service terms are approved.
7. Publish pages one at a time after review, beginning with `/medical-second-opinion/`.
