# Equipment marketplace and RFQ increment — 2026-08-13

## Search ownership

- `/medical-equipment/` owns broad equipment discovery, filtering, comparison and multi-product RFQ intent.
- `/medical-equipment/{supplier}-{product}/` owns one exact machine/model intent.
- `/suppliers/{supplier}/` owns company, distributor and catalog intent.
- `/professionals/clinic-build/` owns full clinic setup and complementary procurement intent.
- Scientific and treatment pages explain biology, indications and technology classes; they link into commerce but do not duplicate product or supplier copy.

## Public experience

- One indexed canonical marketplace page.
- Reviewed, published equipment only.
- Search plus use-family and supplier filters.
- Accessible comparison for up to four machines.
- One RFQ form carrying the selected canonical equipment records.
- No brokerage percentage, commercial agreement language or buyer contact data on the public surface.

## Private workflow

- Submitted slugs are resolved server-side to published, reviewed WordPress equipment records.
- The private request stores canonical IDs, slugs, display-name snapshots and matching verified supplier IDs.
- Supplier candidates are recommendations for the owner; assignment remains an explicit private operation.
- The supplier opportunity view can show requested equipment without revealing buyer contact details before the existing release gate.

## Revenue purpose

This turns catalog traffic into structured purchase intent. A doctor can compare primary machines, add complementary clinic needs and create one opportunity that can be matched to the appropriate supplier or procurement package.
