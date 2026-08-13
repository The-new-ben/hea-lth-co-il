# Private RFQ invitations and supplier account activation — 2026-08-13

## Decision

Hea-lth may invite several verified suppliers to assess one anonymized procurement opportunity. The invitation stage is private and does not assign or release the lead. Only one supplier may be awarded and moved into the existing brokerage-agreement workflow.

## Public/private boundary

- Public supplier showrooms, equipment pages, comparison, scientific content, and commercial landing pages remain indexable.
- The supplier portal, RFQ invitations, brokerage records, agreement documents, delivery logs, and lead records remain private and out of search.
- Initial RFQ invitations contain only a generic verified-buyer label, requested equipment/categories, and a secure portal link.
- Buyer name, company, clinic, city, phone, and email remain hidden until the selected supplier has satisfied the agreement and release gates.
- Brokerage economics do not appear in public content or in the initial multi-supplier invitation.

## Workflow

1. An administrator selects verified suppliers for an anonymized RFQ invitation.
2. Each supplier receives a separate message and responds from its authenticated account.
3. The administrator can award only a supplier that responded as interested.
4. Awarding assigns exactly one supplier, withdraws the other invitations, keeps the lead held, and opens the existing agreement workflow.
5. Agreement acceptance creates an immutable private document and delivery evidence for the supplier and Hea-lth.
6. Buyer contact details are released only through the existing agreement-delivery gate.

## Account activation

The supplier administration card can create or link a WordPress account using a verified email address. Access is sent as a one-time password setup link; no password is stored in delivery records or emailed. Delivery evidence stores a SHA-256 recipient hash, status, UTC timestamp, and linked user ID.

## Production policy

This increment follows the owner instruction to deploy directly to production through the reviewed GitHub Actions release pipeline. No staging site is created.
