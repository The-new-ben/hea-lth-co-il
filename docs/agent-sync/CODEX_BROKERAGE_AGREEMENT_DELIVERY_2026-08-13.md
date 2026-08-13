# Codex private brokerage agreement delivery — 2026-08-13

## Scope

- Brokerage economics remain limited to the authenticated supplier portal and private owner administration.
- Public consumer, content, showroom, product, treatment, and science pages do not render brokerage terms.
- The supplier portal remains `noindex`; public content keeps its route-specific index policy.

## Acceptance evidence

- Each acceptance creates an immutable private document tied to the request, supplier, accepting account, timestamp, terms version, economics, attribution window, acceptance source, evidence reference, and acceptance fingerprint.
- The canonical document receives its own SHA-256 hash. Modified records fail validation and cannot satisfy the contact-release gate.
- A branded HTML document is delivered separately to the supplier and Hea-lth owner as an email attachment.
- Per-recipient delivery results are recorded privately. Failed delivery is retried through bounded WordPress cron events.
- Only the assigned supplier account or an authorized owner can download the stored document.

## Release invariant

Buyer contact access requires all of the following:

1. An assigned supplier.
2. Current accepted brokerage terms.
3. A valid acceptance fingerprint.
4. A matching untampered agreement document.
5. Recorded successful delivery to both the supplier and owner.
6. Explicit owner release state.
