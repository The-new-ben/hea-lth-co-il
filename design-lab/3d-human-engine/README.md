# Hea-lth 3D Human Engine contract

This folder is a non-production contract for a reusable Hea-lth 3D discovery engine.

It exists so that a licensed and clinically reviewed human, regional anatomy, organ, procedure, or equipment model can be loaded into the same system without hard-coding business logic into the 3D scene.

## Files

- `schema/anatomy-model-manifest.schema.json`: expected model and semantic metadata contract.
- `examples/nose-vertical-slice.manifest.json`: a non-public semantic example for the nose discovery flow.
- `scripts/new-3d-model-manifest.mjs`: creates a new manifest skeleton for an approved model.

## Rules

- A manifest does not grant a model license. `license.webDeliveryAllowed` remains `false` until legal approval is recorded.
- A mesh name is not a medical claim. Every public anatomy, treatment, and provider relation needs review ownership and evidence before publication.
- No production model is accepted without GLB validation, visual inspection, performance inspection, asset provenance, clinical review, and accessible non-3D fallback.
- The resolver returns stable entity IDs. WordPress or a future catalog service supplies the actual governed public content.

## Create a skeleton

```powershell
node scripts/new-3d-model-manifest.mjs `
  --model adult-human-v1 `
  --out examples/adult-human-v1.manifest.json `
  --structure anatomy:nose `
  --mesh face.nose.external
```

The script creates a deliberately blocked license record and an empty context mapping. A human must fill in legal, clinical, and data ownership fields before it can be considered for production.
