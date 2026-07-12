/**
 * Regression test for the public anatomy resolver contract.
 *
 * This checks the source data consumed by the text fallback and future map,
 * directory, catalog, and treatment integrations. It never creates records or
 * requests a public API.
 */

import { readFile } from 'node:fs/promises';

const resolverPath = new URL('../../theme-src/hea-lth-portal/assets/data/anatomy-discovery-v1.json', import.meta.url);
const resolver = JSON.parse(await readFile(resolverPath, 'utf8'));
const themeFunctionsPath = new URL('../../theme-src/hea-lth-portal/functions.php', import.meta.url);
const themeFunctions = await readFile(themeFunctionsPath, 'utf8');

const fail = (message) => {
  process.stderr.write(`${message}\n`);
  process.exit(1);
};

const expect = (condition, message) => {
  if (!condition) {
    fail(message);
  }
};

const allowedRoutingKeys = {
  directory: new Set(['specialty', 'bodyRegion', 'region', 'service']),
  map: new Set(['specialty', 'bodyRegion', 'region', 'service']),
  catalog: new Set(['bodyRegion', 'category'])
};
const allowedEntryQueryKeys = new Set(['specialty', 'body_region', 'region', 'service']);
const routeKeys = new Set();

expect(Array.isArray(resolver.regions) && resolver.regions.length > 0, 'Resolver must contain at least one region.');

resolver.regions.forEach((region) => {
  expect(typeof region.id === 'string' && region.id.length > 0, 'Every region requires a stable ID.');
  expect(Array.isArray(region.contexts) && region.contexts.length > 0, `${region.id} requires at least one context.`);

  region.contexts.forEach((context) => {
    expect(typeof context.id === 'string' && context.id.length > 0, `${region.id} context requires a stable ID.`);
    expect(context.routing && typeof context.routing === 'object', `${region.id}/${context.id} requires governed routing.`);
    expect(typeof context.routing.treatmentHubRouteKey === 'string' && /^[a-z0-9_]+$/.test(context.routing.treatmentHubRouteKey), `${region.id}/${context.id} requires a controlled treatment hub route key.`);
    routeKeys.add(context.routing.treatmentHubRouteKey);

    ['directory', 'map', 'catalog'].forEach((target) => {
      const filters = context.routing[target];
      expect(filters && typeof filters === 'object' && !Array.isArray(filters), `${region.id}/${context.id} requires ${target} filters.`);

      Object.entries(filters).forEach(([key, value]) => {
        expect(allowedRoutingKeys[target].has(key), `${region.id}/${context.id} contains unsupported ${target} key: ${key}.`);
        expect(typeof value === 'string' && /^[a-z0-9-]+$/.test(value), `${region.id}/${context.id} ${target}.${key} must be a safe slug.`);
      });
    });

    expect(Array.isArray(context.entries) && context.entries.length > 0, `${region.id}/${context.id} requires visible discovery entries.`);
    context.entries.forEach((entry) => {
      expect(typeof entry.routeKey === 'string' && /^[a-z0-9_]+$/.test(entry.routeKey), `${region.id}/${context.id} entry requires a controlled route key.`);
      routeKeys.add(entry.routeKey);
      expect(!Object.prototype.hasOwnProperty.call(entry, 'url'), `${region.id}/${context.id} entry must not carry a raw URL.`);

      if (entry.query !== undefined) {
        expect(entry.query && typeof entry.query === 'object' && !Array.isArray(entry.query), `${region.id}/${context.id} entry query must be an object.`);
        Object.entries(entry.query).forEach(([key, value]) => {
          expect(allowedEntryQueryKeys.has(key), `${region.id}/${context.id} entry query contains unsupported key: ${key}.`);
          expect(typeof value === 'string' && /^[a-z0-9-]+$/.test(value), `${region.id}/${context.id} entry query ${key} must be a safe slug.`);
        });
      }
    });
  });
});

expect(!JSON.stringify(resolver).includes('"url"'), 'Resolver must not contain raw public URLs.');
expect(themeFunctions.includes('function hea_lth_portal_anatomy_route_map()'), 'Theme must publish the controlled anatomy route map.');
routeKeys.forEach((routeKey) => {
  expect(themeFunctions.includes(`'${routeKey}'`), `Anatomy route key is missing from the controlled theme route map: ${routeKey}.`);
});

process.stdout.write(`Anatomy resolver contract passed for ${resolver.regions.length} regions.\n`);
