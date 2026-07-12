# Lead-route resolver boundary

Status: implemented source foundation, not a live intake system and not authorization to collect or forward any visitor data.

## What exists

`Hea_Lth_Lead_Route_Resolver` is an internal plugin service. It owns a private `hp_lead_route` configuration record and returns only internal IDs for routes that meet all of these conditions:

1. Route is published, active, and marked as accepting capacity.
2. Route matches the selected specialty, region, service, and body-region context.
3. Recipient is an existing published `hp_provider` or `hp_clinic` record with public state `verified`.

The anatomy resolver and verified directory use the same body-region taxonomy. That makes `nose`, `scalp`, `skin-face`, and `movement` meaningful matching inputs rather than decorative UI state.

## What it cannot do

- It accepts no name, phone, email, symptom description, file, payment, or consent record.
- It exposes no public REST endpoint and does not send email, webhook, CRM, or provider notification.
- It does not rank or select recipients using sponsorship state.
- It does not make a clinical recommendation or claim capacity or medical suitability.

## Required next gate before real inquiries

1. Approve CRM owner, data roles, retention, breach process, and exit rights.
2. Approve explicit named-recipient or named-category consent copy in Hebrew.
3. Add anti-abuse, duplicate, suppression, recipient SLA, audit, and credit policies.
4. Build a separate minimal-intake service that passes only consented operational data into this resolver.
5. Conduct privacy, legal, clinical, and operational sign-off before activation.

This boundary implements the routing order defined in the commercial operating model without pretending the required CRM and consent system already exists.
