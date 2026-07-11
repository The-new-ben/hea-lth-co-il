(() => {
  'use strict';

  const viewer = document.querySelector('[data-anatomy-viewer]');
  if (!viewer) {
    return;
  }

  const regionControls = viewer.querySelector('[data-anatomy-region-controls]');
  const contextControls = viewer.querySelector('[data-anatomy-context-controls]');
  const resultPanel = viewer.querySelector('[data-anatomy-results]');
  const status = viewer.querySelector('[data-anatomy-status]');
  const modelStage = viewer.querySelector('[data-anatomy-model-stage]');
  const configUrl = viewer.dataset.configUrl;
  const modelConfig = window.heaLthAnatomyViewer && typeof window.heaLthAnatomyViewer === 'object'
    ? window.heaLthAnatomyViewer
    : { status: 'license-gated', engine: 'none', reason: 'missing-public-config' };
  const routeMap = window.heaLthAnatomyRoutes && typeof window.heaLthAnatomyRoutes === 'object'
    ? window.heaLthAnatomyRoutes
    : {};
  const allowedEntryQueryKeys = new Set(['specialty', 'body_region', 'region', 'service']);

  const state = {
    resolver: null,
    region: null,
    context: null
  };

  const setStatus = (message) => {
    if (status) {
      status.textContent = message;
    }
  };

  const isApprovedThreeViewer = () => (
    modelConfig.status === 'approved' && modelConfig.engine === 'three-webgl'
  );

  const makeButton = (label, isSelected = false) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'hp-anatomy-control';
    button.textContent = label;
    button.setAttribute('aria-pressed', String(isSelected));
    return button;
  };

  const renderUnavailableModel = () => {
    if (!modelStage) {
      return;
    }

    modelStage.dataset.state = 'license-gated';
    modelStage.replaceChildren();

    const grid = document.createElement('div');
    grid.className = 'hp-anatomy-model-stage__grid';
    grid.setAttribute('aria-hidden', 'true');
    for (let index = 0; index < 4; index += 1) {
      grid.appendChild(document.createElement('i'));
    }

    const message = document.createElement('div');
    message.className = 'hp-anatomy-model-stage__message';
    const label = document.createElement('span');
    label.textContent = 'סביבת הצגה';
    const heading = document.createElement('strong');
    heading.textContent = 'נכס תלת ממד מאושר טרם צורף';
    const copy = document.createElement('p');
    copy.textContent = 'אפשר להמשיך עם מסלול הבחירה הטקסטואלי גם ללא המודל.';
    message.append(label, heading, copy);
    modelStage.append(grid, message);
  };

  const renderModelLoading = () => {
    if (!modelStage) {
      return;
    }

    modelStage.dataset.state = 'loading';
    modelStage.replaceChildren();

    const message = document.createElement('div');
    message.className = 'hp-anatomy-model-stage__message hp-anatomy-model-stage__message--loading';
    const label = document.createElement('span');
    label.textContent = 'מודל תלת ממד';
    const heading = document.createElement('strong');
    heading.textContent = 'טוענים מודל אנטומי מאושר';
    const copy = document.createElement('p');
    copy.textContent = 'אפשר להמשיך במסלול הטקסטואלי במקביל לטעינה.';
    message.append(label, heading, copy);
    modelStage.appendChild(message);
  };

  const createElement = (tagName, className, text) => {
    const element = document.createElement(tagName);
    if (className) {
      element.className = className;
    }
    if (typeof text === 'string') {
      element.textContent = text;
    }
    return element;
  };

  const resolveRoute = (routeKey, query = {}) => {
    if (typeof routeKey !== 'string' || !Object.prototype.hasOwnProperty.call(routeMap, routeKey) || typeof routeMap[routeKey] !== 'string') {
      return '';
    }

    try {
      const url = new URL(routeMap[routeKey], window.location.origin);
      if (url.origin !== window.location.origin) {
        return '';
      }

      if (query && typeof query === 'object' && !Array.isArray(query)) {
        Object.entries(query).forEach(([key, value]) => {
          if (allowedEntryQueryKeys.has(key) && typeof value === 'string' && /^[a-z0-9-]+$/.test(value)) {
            url.searchParams.set(key, value);
          }
        });
      }

      return url.toString();
    } catch (error) {
      return '';
    }
  };

  const createResultCard = (entry) => {
    if (!entry || typeof entry !== 'object') {
      return null;
    }

    const url = resolveRoute(entry.routeKey, entry.query);
    if (!url || typeof entry.kind !== 'string' || typeof entry.label !== 'string' || typeof entry.detail !== 'string') {
      return null;
    }

    const card = createElement('a', 'hp-anatomy-result-card');
    card.href = url;
    card.append(
      createElement('span', '', entry.kind),
      createElement('strong', '', entry.label),
      createElement('small', '', entry.detail)
    );
    const arrow = createElement('b', '', '←');
    arrow.setAttribute('aria-hidden', 'true');
    card.appendChild(arrow);
    return card;
  };

  const renderResults = (region, context) => {
    if (!resultPanel || !region || !context) {
      return;
    }

    const heading = createElement('div', 'hp-anatomy-results__heading');
    heading.append(
      createElement('p', '', 'מסלול שנבחר'),
      createElement('h2', '', region.label),
      createElement('span', '', context.label)
    );

    const cards = createElement('div', 'hp-anatomy-results__cards');
    const entries = Array.isArray(context.entries) ? context.entries : [];
    entries.map(createResultCard).filter(Boolean).forEach((card) => cards.appendChild(card));

    if (!cards.childElementCount) {
      cards.appendChild(createElement('p', '', 'אין כרגע מסלול מאושר להצגה עבור ההקשר הזה.'));
    }

    resultPanel.replaceChildren(heading, cards);
  };

  const serialiseRouting = (context) => {
    const routing = context && context.routing && typeof context.routing === 'object' ? context.routing : {};
    const allowKeys = (value, keys) => {
      if (!value || typeof value !== 'object') {
        return {};
      }

      return keys.reduce((safe, key) => {
        if (typeof value[key] === 'string' && value[key].length > 0) {
          safe[key] = value[key];
        }
        return safe;
      }, {});
    };

    return {
      treatmentHubUrl: resolveRoute(routing.treatmentHubRouteKey, routing.treatmentHubQuery),
      directory: allowKeys(routing.directory, ['specialty', 'bodyRegion', 'region', 'service']),
      map: allowKeys(routing.map, ['specialty', 'bodyRegion', 'region', 'service']),
      catalog: allowKeys(routing.catalog, ['bodyRegion', 'category'])
    };
  };

  const emitResolutionUpdated = (source) => {
    if (!state.region || !state.context) {
      return;
    }

    window.dispatchEvent(new CustomEvent('hea-lth:anatomy-resolution-updated', {
      detail: {
        source,
        region: {
          id: state.region.id,
          label: state.region.label
        },
        context: {
          id: state.context.id,
          label: state.context.label
        },
        routing: serialiseRouting(state.context)
      }
    }));
  };

  const updatePressedState = (container, selectedId) => {
    if (!container) {
      return;
    }

    container.querySelectorAll('button[data-anatomy-id]').forEach((button) => {
      button.setAttribute('aria-pressed', String(button.dataset.anatomyId === selectedId));
    });
  };

  const selectContext = (contextId, source = 'controls') => {
    if (!state.region || !Array.isArray(state.region.contexts)) {
      return;
    }

    const nextContext = state.region.contexts.find((context) => context.id === contextId);
    if (!nextContext) {
      return;
    }

    state.context = nextContext;
    updatePressedState(contextControls, nextContext.id);
    renderResults(state.region, nextContext);
    emitResolutionUpdated(source);
  };

  const renderContexts = (region, source) => {
    if (!contextControls) {
      return;
    }

    contextControls.replaceChildren();
    const contexts = Array.isArray(region.contexts) ? region.contexts : [];
    contexts.forEach((context, index) => {
      const button = makeButton(context.label, index === 0);
      button.dataset.anatomyId = context.id;
      button.addEventListener('click', () => selectContext(context.id, 'controls'));
      contextControls.appendChild(button);
    });

    if (contexts[0]) {
      selectContext(contexts[0].id, source);
    }
  };

  const selectRegionById = (regionId, source = 'controls') => {
    if (!state.resolver || !Array.isArray(state.resolver.regions)) {
      return false;
    }

    const nextRegion = state.resolver.regions.find((region) => region.id === regionId);
    if (!nextRegion) {
      return false;
    }

    state.region = nextRegion;
    updatePressedState(regionControls, nextRegion.id);
    renderContexts(nextRegion, source);

    window.dispatchEvent(new CustomEvent('hea-lth:anatomy-region-selected', {
      detail: { regionId: nextRegion.id, source }
    }));

    return true;
  };

  const renderRegions = (resolver) => {
    if (!regionControls) {
      return;
    }

    regionControls.replaceChildren();
    const regions = Array.isArray(resolver.regions) ? resolver.regions : [];
    regions.forEach((region, index) => {
      const button = makeButton(region.label, index === 0);
      button.dataset.anatomyId = region.id;
      button.addEventListener('click', () => selectRegionById(region.id));
      regionControls.appendChild(button);
    });

    if (regions[0]) {
      selectRegionById(regions[0].id);
    }
  };

  const initialise = async () => {
    if (isApprovedThreeViewer()) {
      renderModelLoading();
      setStatus('בחירת אזור פעילה. המודל האנטומי נטען כעת.');
    } else {
      renderUnavailableModel();
      setStatus('בחירת אזור פעילה. טעינת מודל תתאפשר רק לאחר אישור רישוי ובקרה קלינית.');
    }

    if (!configUrl) {
      setStatus('לא נמצא קובץ הגדרה לחוויית הגוף.');
      return;
    }

    try {
      const response = await window.fetch(configUrl, { credentials: 'same-origin' });
      if (!response.ok) {
        throw new Error(`Configuration returned ${response.status}`);
      }

      state.resolver = await response.json();
      renderRegions(state.resolver);

      window.heaLthAnatomyDiscovery = Object.freeze({
        selectRegionById: (regionId) => selectRegionById(regionId, 'external'),
        getSelectedRegionId: () => state.region ? state.region.id : null
      });

      window.dispatchEvent(new CustomEvent('hea-lth:anatomy-discovery-ready', {
        detail: { modelStatus: modelConfig.status || 'unknown' }
      }));
    } catch (error) {
      viewer.dataset.configError = 'true';
      setStatus('לא ניתן לטעון את מסלולי הבחירה כרגע. אפשר להמשיך לאינדקס הטיפולים והמקצוענים.');
    }
  };

  window.addEventListener('hea-lth:anatomy-viewer-selection', (event) => {
    const regionId = event.detail && event.detail.regionId;
    if (regionId) {
      selectRegionById(regionId, 'model');
    }
  });

  window.addEventListener('hea-lth:anatomy-viewer-ready', () => {
    if (isApprovedThreeViewer()) {
      setStatus('המודל האנטומי מוכן. אפשר לסובב, להגדיל ולבחור אזור.');
    }
  });

  window.addEventListener('hea-lth:anatomy-viewer-failed', () => {
    renderUnavailableModel();
    setStatus('המודל האנטומי אינו זמין כרגע. מסלול הבחירה הטקסטואלי נשאר פעיל.');
  });

  initialise();
})();
