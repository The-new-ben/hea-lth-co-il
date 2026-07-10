# Hea-lth web quality tools

This directory pins the automated accessibility toolchain. Automated results are release gates and diagnostics, not substitutes for keyboard, screen-reader, mobile-device, consent, and usability review.

Performance testing uses the configured official Chrome DevTools MCP plus Google PageSpeed Insights/CrUX APIs. The current Lighthouse/Lighthouse CI npm dependency trees were deliberately not admitted on 2026-07-10 because `npm audit` reported unresolved OpenTelemetry advisories and a high-severity stale `tmp` dependency in Lighthouse CI.

## Install

```powershell
npm ci
```

## Examples

```powershell
npm run axe -- https://hea-lth.co.il
```

Do not run session replay, form completion, or authenticated-page tests with real patient or lead data.
