# hea-lth.co.il — Multi-Track Business Plan (2026-08-18)

Prepared by Claude Fable 5 from the repo strategy docs (cited inline) plus the live deal flow of 2026-08-12→18. Companion release receipt: `agent-sync/CLAUDE_ADVISORY_ROOM_001_RECEIPT_2026-08-18.md`. Code state at writing: plugin 0.19.0 / theme 0.18.2 live.

---

## A. The business in one page

**What the asset is today.** A live, institute-grade Hebrew RTL private-health portal with three layers:

1. **Traffic surfaces (demand):** 3D real-body anatomy engine above the fold (37 clickable structures, click→services); care map with 986 real institutions; 22 foundation pages, zero 404s; 9 science pages (`/biology/` + spokes, `/longevity-medicine/`, `/skin/`, `/health-technology/…`); WhatsApp consult bar. Science↔commerce wiring is governed by a URL graph: science owns mechanisms, commercial pages own comparison and action (`SCIENCE_TO_REVENUE_URL_GRAPH_2026-08-13.md`).
2. **B2B rails (supply):** `/suppliers/` marketplace (2 seeded showrooms: NUBWAY/Nicro, Galaxy; 20 equipment pages at `/medical-equipment/{supplier}-{product}/`); private supplier portal (plans, catalog review queue, assigned opportunities); published plan pricing at `/professionals/supplier-join/` — Verified ₪990/mo, Showroom ₪7,500 + ₪2,490/mo, Growth ₪3,900/mo + success fee; clinic-build plans; B2B intake stored privately with consent version (`B2B_MONETIZATION_OPERATING_MODEL_2026-08-13.md`).
3. **Brokerage machinery (transaction):** ledger with per-opportunity %/fixed/hybrid terms, minimum fee, attribution window; fingerprinted supplier acceptance; immutable agreement documents; PII held until explicit release; invoice states `not_ready → ready → issued → paid`; anonymized RFQ invitations; and — shipped today — **private code-gated advisory rooms** (`/advisory/{room}/`), room #1 live for Dr. A.

**The flywheel.** Science content earns qualified discovery → equipment pages capture model-level intent → a doctor asks the portal to run procurement (Dr. A is the proof) → the deal pulls suppliers onto the platform under non-circumvention → suppliers seed the catalog to win the next deal → catalog + advisory rooms attract the next clinic-opening doctor. **One whale buyer recruits the supply side for free** — the correct cold-start order for a two-sided marketplace.

**Strategic shift.** The July docs sequenced patient-side lead-gen first. Reality inverted it: first revenue is B2B brokerage — no patient-PII exposure, no MoH advertising sensitivity at the transaction layer, and a buyer who *refuses* the alternative channel (direct sales calls). Patient side stays the long-term moat; B2B is the now.

---

## B. Revenue tracks

### B1. Equipment brokerage (success fees) — the live template
- **Customer:** doctors/clinic founders who want procurement run for them; the winning supplier pays.
- **Offer:** protected introduction + end-to-end procurement (requirements → supplier recruitment → RFQ → comparison → award → delivery follow-through), non-circumvention + attribution per opportunity.
- **Pricing anchor:** **10% of closed value or ₪8,000–15,000 minimum per opportunity** (B2B model doc; live framework accepted in writing by Nicro/NUBWAY).
- **Unit economics:** minimum outearns 10% below ₪80–150K deal value → every opportunity floored at ₪8–15K. Deal #1 = **2 billable opportunity categories** (body-contouring platform; clinical hair-removal laser) → floor **₪16–30K** if both close at minimum, 10% upside above.
- **In code:** everything transactional. **Missing:** CRM; fragile lead funnel (C2/C3/M4 in the review); C1 temp-dir check; consent disclosure (H3).
- **90 days:** close deal #1 *through the machinery* (ledger terms, RFQ, invoice states), then package as the repeatable product.
- **Risk:** success-contingent, concentrated; non-circumvention only as strong as fingerprinted agreements + attribution evidence — every intro through the ledger, never WhatsApp.

### B2. Supplier subscriptions (Verified / Showroom / Growth)
- **Customer:** IL equipment importers/manufacturers (seed cohort: Nicro/NUBWAY, Galaxy, Venus Concept).
- **Pricing anchor:** published + in code: **₪990/mo · ₪7,500 + ₪2,490/mo · ₪3,900/mo + fee** (internal bands 690–1,290 / 4,900–12,000 + 1,990–3,900).
- **In code:** plan state, portal, catalog review queue. **Missing:** billing rail; a *real* verification process (H4 — today the badge is script-stamped); supplier performance reporting.
- **90 days:** free pilot for the 3 engaged suppliers with published prices named and trigger-based conversion (Section D).
- **Risk:** free-pilot inertia → conversion by trigger events tied to received value, free tier can never be better than paid.

### B3. Clinic-establishment advisory ("clinic-build")
- **Customer:** the doctor (buyer side pays).
- **Offer:** productized procurement/ERP-lite — **the advisory room is the product**: private code-gated mini-site with requirements map, live supplier tracks, curated approved equipment, process tracker. Entity map spans 9 procurement categories beyond machines → one machine inquiry expands to a whole account.
- **Pricing anchor:** **₪10,000–25,000 buyer project fee + supplier commissions** (B2B model doc). Dr. A pays no project fee (pilot) — he is the reference case that prices client #2.
- **Missing:** public paid-tier offer page; self-serve room creation (rooms are code-defined — fine at n≤5).
- **90 days:** deliver Dr. A end-to-end → case study (with his permission) → publish the paid offer → sell to clinic-opener #2.
- **Risk:** service scaling on owner time — mitigated by the room template making delivery asynchronous and reusable.

### B4. Premium presence on the care map (disclosed pin)
- Client #1 precedent live (disclosed premium H-pin + per-pin metrics). No pin-specific documented price — sell inside the **₪690–1,290/mo** verified-presence band as "pin + verified profile + monthly engagement report". Near-zero effort; bolt-on, not a focus. Dr. A's clinic at opening is a natural pin candidate.

### B5. Patient-side lead routing to clinics
- The audited `hp_lead_route` resolver exists and is contract-tested (verified recipient, capacity, consent version, review date, disclosure). **No price by design** — the July operating model forbids quoting without pilot evidence, and names mandatory pre-launch gates (counsel review, CRM, consent text, SLA, billing rules). **Do not launch billing this quarter**; prepare one narrow vertical (premium aesthetics) where content, suppliers, and Dr. A's clinic converge.

### B6. Additional doc-supported tracks
- **Category sponsorship** — ₪5,000–20,000/mo, labeled, near-term viable *after* H4 verification is real. First buyer: a pilot supplier wanting the body-contouring or laser category.
- **Commerce margin (consumables)** — documented, deferred until ≥3 operating clinic accounts; WooCommerce kept for this.
- **Data/insights** — feature of B2 tiers, not a standalone track (nothing supports standalone pricing). Speculative.
- **Editorial sponsorship** — gated behind independence + approvals; not before H1 (YMYL self-certification) is fixed.

---

## C. Prioritization

| Rank | Track | Time to first ₪ | Effort | Moat |
|---|---|---|---|---|
| **1** | B1 Brokerage | **Weeks — deal in motion** | Low-med | Med-high (non-circumvention + evidence) |
| **2** | B2 Supplier pilot → subs | 60–90d (trigger-based) | Low | High (catalog compounds) |
| **3** | B3 Clinic-build advisory | Next client | Med | **Highest** (whole-account) |
| 4 | B4 Pins | Days, low ticket | Near-zero | Low-med |
| 5 | B6a Sponsorship | After H4 fix | Low | Med |
| 6 | B5 Lead routing | Quarters (legal gates) | High | High (long-term) |

**Top 3 = one motion:** the brokered deal recruits the pilot suppliers (B2) and produces the case study that sells the next clinic-build (B3).

### 4-week plan for #1 (Dr. A deal)
- **W1 — lock the frame, protect the funnel:** Venus signature (only supplier not yet bound while seeing the deal); record all 3 supplier terms as **ledger opportunities** (fingerprinted acceptance = evidence; WhatsApp is not); collect NUBWAY/Galaxy specs + price ranges; keep the advisory room current (it IS the client deliverable); ship the lead-funnel fix bundle (C2+C3+M4); run the C1 `get_temp_dir()` server check.
- **W2 — quotes & comparison:** quotes through RFQ workflow; comparison concentrated in the room; demos on Dr. A's no-sales-calls terms; fix H3 consent copy before further PII release.
- **W3 — award & invoice readiness:** record award, closed value → commission auto-computes → invoice `ready → issued`; start H4 remediation (supplier-confirmed profiles).
- **W4 — collect & productize:** invoice `paid`; delivery/installation/training follow-through; case study; room template for client #2; one-page brokerage SOP; publish the paid clinic-build offer (₪10–25K).

---

## D. Pilot playbook — 3 suppliers → catalog tenants this week

1. **Pilot letter (day 1–2):** free profile + catalog for the pilot window; published paid plans named explicitly; conversion triggers listed; non-circumvention restated. Free = introductory window, not the product.
2. **Portal accounts:** link each supplier's WP user to their reviewed profile; plan interest recorded. Nicro first (terms accepted), Galaxy second, Venus upon signature.
3. **Make "verified" true (kills H4):** each supplier submits/confirms their own catalog through the review queue — specs, trademark distribution rights, imagery, written contact confirmation. Their confirmation + review checklist = the recorded verification process.
4. **Wire into the live deal:** assign the Dr. A opportunities in the ledger so their portal shows a real deal — the retention hook no free profile provides. Tell them the advisory room exists: a live buyer-facing comparison disciplines quote quality and speed.
5. **Conversion triggers (not dates):** first closed brokered deal → Growth; catalog live + first routed inquiry → Verified; supplier asks for expanded presence → Showroom; category-prominence request or competitor joins → sponsorship. Pilot exit: decliners after real value received drop to a neutral non-badged listing.

---

## E. Risks & guardrails (top 5)

1. **Trust is asserted, not earned** (H1 self-certified YMYL reviews; H4 script-"verified" suppliers) → real review pass or downgrade stamps; supplier-confirmed verification via the pilot.
2. **The funnel can silently lose the next Dr. A** (C2 invisible leads, C3 cached nonce, M4 no rate limit) → one coherent fix bundle, week 1.
3. **Consent/PII at the commercial seam** (H3 consent copy; C1 temp-dir exposure risk; parallel release gate) → fix consent copy, route releases through the audited resolver, verify `get_temp_dir()` before scaling releases.
4. **Concentration + key-man** (one buyer, three suppliers, owner-operated; success-fee binary) → keep ₪8–15K floors, charge the buyer project fee from client #2, convert the case study into pipeline immediately.
5. **Non-circumvention & regulatory enforceability** (Venus unsigned yet exposed; MoH advertising + Amendment 13 over the commercial layer) → no introduction outside fingerprinted ledger agreements; disclosure labels on every paid placement; counsel review before patient-side billing.

**Bottom line:** the machinery for the three priority tracks is built and live; this quarter's work is to push one real deal all the way through it, make "verified" and "reviewed" true, and let the deal recruit the supply side onto paid rails by trigger, not by calendar.
