# Science-to-revenue URL graph

## Objective

Build durable scientific authority that routes users to the correct treatment, clinic, product, equipment, supplier, and procurement destination. A science page explains mechanisms. A commercial page supports comparison or action. They do not share a primary keyword.

## Ownership map

| Layer | URL | Owns | Must not own | Revenue bridge |
|---|---|---|---|---|
| Biology pillar | `/biology/` | Biology of aging and system mechanisms | Clinics, treatments, products | Longevity, skin, wellness, biomarkers |
| Cellular aging | `/biology/cellular-aging/` | Hallmarks and cellular mechanisms | Intervention promises | Longevity, biomarkers |
| Metabolism | `/biology/metabolism/` | Energy balance, nutrient sensing, mitochondria | Weight-loss offers | Wellness, clinic procurement, biomarkers |
| Inflammation | `/biology/inflammation/` | Immune signaling and chronic inflammation concepts | Diagnosis from a marker | Longevity, wellness, biomarkers |
| Genetics and epigenetics | `/biology/genetics-epigenetics/` | Genome stability and gene regulation | Genetic-test sales | Biomarkers, AI and robotics |
| Longevity medicine | `/longevity-medicine/` | Evidence framework for healthy ageing practice | Reversal or cure claims | Prevention, biomarkers, clinic directory |
| Skin science | `/skin/` | Skin structure, barrier and aging biology | Treatment and provider comparison | `/skin-treatments-private/`, products, aesthetics |
| Biomarkers | `/health-technology/biomarkers/` | Measurement validity and interpretation framework | Selling a named test | Labs, equipment, longevity |
| AI and robotics | `/health-technology/ai-robotics/` | Technology classes, transparency and lifecycle | Claiming regulatory status for a vendor | Equipment, suppliers, clinic build |

## Canonical separation rules

1. `/skin/` targets scientific and informational intent. `/skin-treatments-private/` remains the commercial treatment pillar.
2. `/biology/metabolism/` explains mechanisms. Weight-management clinic plans and device pages own procurement and product facts.
3. `/health-technology/biomarkers/` explains validity and interpretation. Laboratory pages own test preparation; future product pages own a named offer.
4. `/health-technology/ai-robotics/` explains technology and governance. Supplier and equipment pages own brands, models, specifications, and inquiries.
5. Scientific hubs link outward through stable route keys. Templates do not invent internal URLs.

## Evidence and review contract

- Every node stores a review level, review date, source note, evidence-source URLs, and controlled bridge keys.
- Medical and emerging-field nodes use maximum review.
- Public language distinguishes research mechanisms from demonstrated clinical benefit.
- Unsupported reversal, cure, guaranteed outcome, and single-biomarker diagnosis claims are prohibited.
- Sources for this release are PubMed, NIH/NIA, WHO, and FDA records listed on the rendered pages.

## Monetization path

Scientific discovery leads to a governed commercial destination, then to a supplier showroom, clinic profile, quote request, procurement request, or commerce offer. Attribution should preserve the originating science node and final commercial context in the private request record. This makes authority content measurable without turning it into sales copy.
