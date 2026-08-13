# Codex brokerage revenue ledger — 2026-08-13

## Increment

- Per-opportunity brokerage terms: percentage, fixed, or hybrid.
- Commission rate, fixed fee, minimum fee, and attribution days.
- Supplier acceptance through the authenticated supplier account.
- Acceptance timestamp, account ID, supplier ID, terms version, and SHA-256 fingerprint.
- Contact release fails closed unless the current accepted fingerprint validates.
- Reassignment or changed economics invalidates acceptance and holds contact details again.
- Estimated and closed deal value in integer ILS.
- Deterministic commission calculation.
- Invoice lifecycle: not ready, ready, issued, paid, or cancelled.
- Automatic owner email when a commission first becomes invoice-ready.
- Aggregate revenue ledger inside the Hea-lth owner control center.

## Standard offer defaults

- Model: percentage.
- Rate: 10%.
- Minimum fee: ₪8,000.
- Attribution window: 180 days.

All defaults remain editable per opportunity before the offer is sent. Editing an accepted offer requires fresh supplier acceptance.
