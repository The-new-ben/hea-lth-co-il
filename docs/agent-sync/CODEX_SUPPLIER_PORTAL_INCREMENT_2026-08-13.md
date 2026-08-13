# Codex supplier portal increment — 2026-08-13

## Product delivered

- A private supplier portal at `/professionals/supplier-portal/`.
- Explicit account ownership for reviewed supplier profiles.
- Membership plans and states for Verified, Showroom, and Growth.
- A private catalog submission queue with administrator review.
- Explicit opportunity assignment, contact-release state, supplier pipeline status, and bounded audit history.
- Contact details remain unavailable to a supplier until an administrator records the release.
- Public supplier onboarding now has clear plan pricing and passes the selected plan into the private request.

## Operating sequence

1. Create or select a WordPress user for the supplier representative.
2. Edit the supplier record, add the user ID, select the plan, and set the membership state.
3. The representative signs in and uses the supplier portal.
4. Assign a private B2B request to a supplier.
5. Keep contact details held until the applicable business condition is recorded.
6. Change the release state to released; the assigned supplier can then see the contact details.
7. The supplier updates the pipeline from the portal. Each change is recorded.

## Release evidence required

- Full PHP, JavaScript, pipeline, PHPCS, PHPStan, packaging, and dry-run gates.
- Desktop and 375px render verification.
- Logged-out public route verification and authenticated workflow code contracts.
- Production healthcheck and rendered URL checks after deployment.
