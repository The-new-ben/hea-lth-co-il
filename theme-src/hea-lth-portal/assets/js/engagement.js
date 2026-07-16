/**
 * Engagement layer: (1) a visible info card for 3D body-part selections that
 * routes into the resolver's services panel, and (2) a premium WhatsApp
 * consult bar whose prefilled Hebrew message carries the page context.
 * Additive only — listens to the frozen viewer's public events.
 */
(() => {
  'use strict';

  const config = window.heaLthEngage && typeof window.heaLthEngage === 'object' ? window.heaLthEngage : {};

  /* ------------------------------------------------------------------ *
   * 1. Selection info card — "press a part, get everything around it". *
   * ------------------------------------------------------------------ */
  // Resolved lazily: the viewer config is injected after this file in the
  // footer, so it must never be captured at load time.
  const getViewerConfig = () => (
    window.heaLthAnatomyViewer && typeof window.heaLthAnatomyViewer === 'object'
      ? window.heaLthAnatomyViewer
      : null
  );

  const REGION_BY_STRUCTURE = {
    'anatomy:nose': 'nose',
    'anatomy:lips': 'skin-face',
    'anatomy:facial-muscles': 'skin-face',
    'anatomy:jaw-muscles': 'skin-face',
    'anatomy:cranium': 'skin-face',
    'anatomy:mandible': 'skin-face',
    'anatomy:pectorals': 'chest',
    'anatomy:abdominals': 'chest'
  };

  const labelFor = (structureId) => {
    const viewerConfig = getViewerConfig();
    if (!viewerConfig || !Array.isArray(viewerConfig.structures)) {
      return null;
    }
    const match = viewerConfig.structures.find((item) => item.id === structureId);
    return match && match.labels ? (match.labels.he || match.labels.en) : null;
  };

  let card = null;
  let cardName = null;
  let currentRegion = null;

  const ensureCard = () => {
    if (card) {
      return card;
    }
    const stage = document.querySelector('[data-anatomy-model-stage]');
    if (!stage) {
      return null;
    }

    card = document.createElement('div');
    card.className = 'hp-select-card';
    card.hidden = true;

    const eyebrow = document.createElement('span');
    eyebrow.className = 'hp-select-card__eyebrow';
    eyebrow.textContent = 'נבחר במודל';

    cardName = document.createElement('strong');
    cardName.className = 'hp-select-card__name';

    const action = document.createElement('button');
    action.type = 'button';
    action.className = 'hp-select-card__action';
    action.textContent = 'מידע ושירותים לאזור';

    const close = document.createElement('button');
    close.type = 'button';
    close.className = 'hp-select-card__close';
    close.setAttribute('aria-label', 'סגירה');
    close.textContent = '×';

    action.addEventListener('click', () => {
      if (window.heaLthAnatomyDiscovery && currentRegion) {
        window.heaLthAnatomyDiscovery.selectRegionById(currentRegion);
      }
      const results = document.querySelector('[data-anatomy-results]');
      if (results) {
        results.scrollIntoView({ behavior: 'smooth', block: 'center' });
      }
    });

    close.addEventListener('click', () => {
      card.hidden = true;
    });

    card.append(close, eyebrow, cardName, action);
    stage.appendChild(card);
    return card;
  };

  window.addEventListener('hea-lth:anatomy-viewer-selection', (event) => {
    const detail = event.detail || {};
    const label = labelFor(detail.structureId);
    if (!label || !ensureCard()) {
      return;
    }
    cardName.textContent = label;
    currentRegion = REGION_BY_STRUCTURE[detail.structureId] || 'movement';
    card.hidden = false;
  });

  /* ------------------------------------------------------------------ *
   * 2. WhatsApp consult bar — renders only when a number is configured. *
   * ------------------------------------------------------------------ */
  const number = String(config.whatsapp || '').replace(/[^0-9]/g, '');
  if (!number) {
    return;
  }

  const pageTitle = (document.querySelector('h1') || {}).textContent || document.title;
  const message = 'שלום, הגעתי מהעמוד "' + pageTitle.trim() + '" באתר Hea-lth (' + window.location.href.split('?')[0] + ') ואשמח לייעוץ ראשוני.';
  const href = 'https://wa.me/' + number + '?text=' + encodeURIComponent(message);

  const bar = document.createElement('a');
  bar.className = 'hp-wa';
  bar.href = href;
  bar.target = '_blank';
  bar.rel = 'noopener';
  bar.innerHTML = '<span class="hp-wa__icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path fill="currentColor" d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 18.2a8.2 8.2 0 0 1-4.2-1.2l-.3-.2-3 .8.8-2.9-.2-.3A8.2 8.2 0 1 1 12 20.2zm4.6-6.1c-.3-.1-1.5-.7-1.7-.8s-.4-.1-.6.1-.7.8-.8 1-.3.2-.6.1a6.7 6.7 0 0 1-2-1.2 7.4 7.4 0 0 1-1.4-1.7c-.1-.3 0-.4.1-.6l.4-.5c.1-.2.2-.3.3-.5s0-.4 0-.5-.6-1.5-.8-2-.4-.5-.6-.5h-.5a1 1 0 0 0-.7.3 3 3 0 0 0-.9 2.2 5.2 5.2 0 0 0 1.1 2.8 11.9 11.9 0 0 0 4.6 4 15.6 15.6 0 0 0 1.5.6 3.7 3.7 0 0 0 1.7.1 2.8 2.8 0 0 0 1.8-1.3 2.2 2.2 0 0 0 .2-1.3c-.1-.1-.3-.2-.6-.3z"/></svg></span><span class="hp-wa__copy"><b>ייעוץ מהיר בוואטסאפ</b><small>מענה ראשוני ללא התחייבות</small></span><span class="hp-wa__brand" aria-hidden="true">H</span>';

  const mount = () => document.body.appendChild(bar);
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
