#!/usr/bin/env node

import { mkdir, writeFile } from 'node:fs/promises';
import { dirname, resolve } from 'node:path';

function usage() {
  return [
    'Usage:',
    '  node new-3d-model-manifest.mjs --model <model-id> --out <file> --structure <anatomy:id> --mesh <mesh.id>',
    '',
    'Example:',
    '  node new-3d-model-manifest.mjs --model adult-human-v1 --out examples/adult-human-v1.manifest.json --structure anatomy:nose --mesh face.nose.external'
  ].join('\n');
}

function parseArgs(argv) {
  const values = {};
  for (let index = 0; index < argv.length; index += 2) {
    const key = argv[index];
    const value = argv[index + 1];
    if (!key?.startsWith('--') || !value) {
      throw new Error('Invalid arguments.\n\n' + usage());
    }
    values[key.slice(2)] = value;
  }
  return values;
}

function assertPattern(value, pattern, label) {
  if (!pattern.test(value)) {
    throw new Error(`${label} is not valid: ${value}`);
  }
}

function createManifest({ model, structure, mesh }) {
  assertPattern(model, /^[a-z0-9]+(?:-[a-z0-9]+)*-v[0-9]+$/, 'model');
  assertPattern(structure, /^anatomy:[a-z0-9]+(?:-[a-z0-9]+)*$/, 'structure');
  assertPattern(mesh, /^[a-z0-9]+(?:[.-][a-z0-9]+)*$/, 'mesh');

  const defaultLabel = structure.replace(/^anatomy:/, '').replace(/-/g, ' ');
  return {
    modelId: model,
    version: '0.1.0',
    status: 'draft',
    license: {
      owner: 'TBD',
      sourceUrl: 'https://example.invalid/requires-approved-asset',
      webDeliveryAllowed: false,
      derivativeUseAllowed: false,
      contractReference: 'TBD'
    },
    clinicalReview: {
      status: 'required',
      owner: 'TBD'
    },
    asset: {
      sourceGlb: 'assets/TBD.glb',
      lods: [
        { id: 'lod-0', path: 'assets/TBD-preview.glb', purpose: 'preview' }
      ],
      validation: { gltfValid: false, visualQa: 'pending', performanceQa: 'pending' }
    },
    layers: [
      { id: 'skin', kind: 'surface', meshIds: ['skin.outer'], defaultVisible: true }
    ],
    structures: [
      {
        id: structure,
        meshIds: [mesh],
        labels: { he: 'TBD', en: defaultLabel },
        contexts: [
          {
            id: 'TBD',
            labelHe: 'TBD',
            resolverEntityIds: { topics: [], specialties: [], treatments: [], equipmentCategories: [] }
          }
        ]
      }
    ]
  };
}

async function main() {
  if (process.argv.includes('--help') || process.argv.includes('-h')) {
    console.log(usage());
    return;
  }
  const args = parseArgs(process.argv.slice(2));
  for (const required of ['model', 'out', 'structure', 'mesh']) {
    if (!args[required]) {
      throw new Error(`Missing --${required}.\n\n` + usage());
    }
  }

  const outputPath = resolve(args.out);
  await mkdir(dirname(outputPath), { recursive: true });
  await writeFile(outputPath, JSON.stringify(createManifest(args), null, 2) + '\n', { encoding: 'utf8', flag: 'wx' });
  console.log(`Created ${outputPath}`);
}

main().catch((error) => {
  console.error(error.message);
  process.exitCode = 1;
});
