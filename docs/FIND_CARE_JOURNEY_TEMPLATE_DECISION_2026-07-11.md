# Find-care journey template decision

Observed: 2026-07-11  
Status: source implemented, not live, no intake or CRM handoff enabled

## Why this route exists

The portal already used `/find-care/` from the header, homepage, and profile CTA. It did not yet have a dedicated reusable template. That left the most important public starting action dependent on generic page output.

Mayo Clinic makes care and provider discovery prominent through appointment and doctor-finder paths, while keeping health-library navigation separate. The Hea-lth route applies the same journey separation in Hebrew without copying copy or implying that the site is a medical provider. [Mayo Clinic home](https://www.mayoclinic.org/) and [Mayo doctor finder](https://www.mayoclinic.org/appointments/find-a-doctor)

## Implemented route contract

| Visitor choice | Canonical destination | Purpose |
| --- | --- | --- |
| טיפול או ניתוח | `/treatments/` | Understand treatment and preparation paths. |
| רופא, מרפאה או שירות | Existing doctor and clinic index URL | Search verified directory records when inventory is available. |
| בדיקה או דימות | `/diagnostics/` | Read about preparation and follow-up questions. |
| חוות דעת נוספת | Existing high-intent second-opinion URL | Preserve the existing SEO decision. |
| טכנולוגיה או ציוד | `/health-technology/` | Explore governed technology information. |

## Safety and commercial boundary

- The template has no form, API call, CRM call, cookie-based profiling, medical questionnaire, or provider-routing action.
- It explicitly says that visitors should not enter medical records, diagnoses, medication lists, or other sensitive health data in the initial route.
- The visual CTA is navigation only. A future consent-first intake layer can attach only after the lead-routing audit, CRM, retention, legal, and provider-capacity gates are approved.
- It keeps the existing high-value URL map intact and does not create new search-target pages.

## Evidence required before launch

1. The page is created in WordPress and assigned this template without replacing an existing powered URL.
2. Desktop and mobile Chrome screenshots verify Hebrew RTL order, menu behaviour, readable headings, and touch targets.
3. Search Console and analytics record the canonical route and selection events only after tracking governance is approved.
4. Any later contact or booking flow proves consent wording, data minimization, recipient verification, route capacity, audit trail, and failure handling.
