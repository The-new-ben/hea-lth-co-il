# Hea-lth CRM Automation

Updated: 2026-05-27

## CRM Object

- Storage: private WordPress custom post type `health_lead`.
- Purpose: collect service-coordination leads without publishing medical information.
- Admin menu: Health Leads.

## Captured Fields

- Contact: name, phone, email.
- Service routing: service category, specialty needed, city, urgency, preferred route.
- Payment routing: private, SHABAN / HMO, private insurance, unknown; optional provider name.
- Privacy: consent and explicit acknowledgement that the site does not diagnose or treat.
- Attribution: landing URL, referrer, UTM source, medium, campaign, term, and content.

## Status Workflow

1. `new` - lead received.
2. `triage_needed` - missing facts or unclear service request.
3. `needs_clinical_review` - requires review by licensed medical professional before routing.
4. `provider_match` - possible provider/service identified.
5. `appointment_requested` - appointment request sent or in progress.
6. `refund_route` - insurance/refund track.
7. `closed_lost` - no fit, no response, or rejected.

## Privacy And Safety Rules

- Do not send medical details in plaintext email. The theme notification links to the private WordPress lead record instead.
- Do not provide diagnosis, treatment instructions, dosage advice, or emergency triage.
- Do not collect medical documents through this form.
- Emergency cases should be directed to MDA 101 or emergency room.
- Any health advice page must have medical review, source links, author/reviewer details, and update date before publishing.

## Revenue Follow-Up

- First revenue models: appointment coordination, second-opinion facilitation, imaging coordination, insurance-refund assistance, home visits, and premium health-service concierge.
- Grow and Green Invoice can later support paid coordination, premium consultation booking, or provider billing.
- Provider/referral tracking should use lead status plus UTM fields before any payout model is introduced.

## Deployment Notes

- Sync only into a dedicated empty theme folder in UPress.
- Run PHP lint and staging preview before activation.
- After activation, submit one internal test lead and confirm:
  - Lead appears as private `health_lead`.
  - Email notification does not expose medical details.
  - UTM fields persist from URL query parameters.
  - Emergency and medical disclosure text is visible.

## 2026-05-27 Initial Status Routing

New leads no longer all start as `new`.

- Second-opinion leads start as `needs_clinical_review`.
- Refund or payer-route leads start as `refund_route`.
- Premium or broad provider-matching leads start as `provider_match`.
- Private-doctor appointment leads start as `appointment_requested`.
- MRI/CT, home-visit, and urgent non-emergency leads start as `triage_needed`.
- Everything else remains `new` for manual review.

These statuses are operator queues only. They do not provide diagnosis, medical triage, or medical advice.

## 2026-05-27 Conversion Measurement

When the stored-lead redirect reaches `?lead=received`, the theme pushes a privacy-safe `generate_lead` event into `window.dataLayer`.

Payload fields: `event`, `lead_form`, `portfolio_site`, `lead_result`, and `conversion_source`.

No personally identifiable information, health-service selection, medical detail, message text, payer details, or sensitive data is sent in this browser event. Configure GTM/GA4 to treat `generate_lead` as the lead key event after production deployment.

## 2026-05-27 Admin Source Columns

The Health Leads admin list shows `UTM source` and `Landing URL` beside the core triage fields so operators can quickly spot which campaigns and pages are producing leads.

Open the private lead detail screen for the full attribution record, including referrer URL and the complete UTM set.

## 2026-05-27 Revenue Board

The Health Leads menu includes a `Revenue Board` submenu. It summarizes the latest 50 private leads by status, UTM source, and landing URL, then lists the latest leads with edit links.

Use it for weekly revenue triage: identify which pages and campaigns are producing qualified service-coordination opportunities before prioritizing new SEO work, provider outreach, or paid tests.
