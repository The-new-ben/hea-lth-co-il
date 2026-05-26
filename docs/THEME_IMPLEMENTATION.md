# Hea-lth Theme Implementation

Updated: 2026-05-27

## Files

- `style.css` - theme metadata and RTL service-funnel styling.
- `functions.php` - theme setup, `health_lead` CRM CPT, lead handler, admin status workflow, medical disclosure shortcode, schema, and attribution capture.
- `front-page.php` - Hebrew homepage for private-health service coordination.
- `header.php`, `footer.php`, `index.php` - base WordPress templates.
- `theme.json` - editor palette, layout widths, and Hebrew font stack.

## Commercial Positioning

The homepage is built around high-value private-health intent:

- `private-doctor-appointment`
- `medical-second-opinion`
- `mri-ct-appointment`
- `health-insurance-refund`
- `doctor-home-visit`
- `premium-health-services`

The site should not become a broad automated medical-advice portal. Its job is to qualify and route service demand.

## SEO And Trust

- Front-page schema uses `MedicalBusiness`.
- The visible emergency notice tells users to contact MDA 101 or emergency room for urgent symptoms.
- The medical disclosure says the site does not diagnose, treat, or replace a licensed physician.
- Any medical explainer must be reviewed before publication and include sources, reviewer, and update date.

## CRM Behavior

- Form submission creates a private `health_lead`.
- Required fields: name, phone, service category, privacy acknowledgement, consent.
- Anti-spam: hidden honeypot field.
- Stored attribution: landing page, referrer, and UTM fields.
- Admin status can be updated from the lead edit screen.
- Email notification intentionally avoids sending medical lead details in plaintext.

## UPress Sync Checklist

1. Create a dedicated empty theme folder in UPress file manager.
2. Connect this GitHub branch to that folder only.
3. Run PHP lint in staging or via UPress tooling.
4. Preview the theme without activating on production.
5. Submit a test lead and verify CRM/email/UTM capture.
6. Only then activate.

## Remaining Work

- Add reviewed provider/trust details.
- Add privacy policy language specific to health lead handling.
- Add reviewed copy for each draft service page.
- Decide if Grow/Green Invoice should sell paid coordination, second-opinion booking, or premium concierge packages.
- Add Search Console and conversion events after activation.
