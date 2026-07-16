/**
 * Care map — the location index attached to the 3D body.
 *
 * Renders the gated map configuration (keyless leaflet-osm provider): a rich
 * base of real hospitals/clinics (OpenStreetMap data, attributed), premium
 * featured clients with disclosed commercial placement, visitor geolocation,
 * and live reaction to body-part selections (region → specialty spotlight).
 */
(() => {
  'use strict';

  const container = document.querySelector('[data-care-map]');
  const config = window.heaLthDirectoryMap && typeof window.heaLthDirectoryMap === 'object'
    ? window.heaLthDirectoryMap
    : null;

  if (!container || !config || config.status !== 'approved' || config.provider !== 'leaflet-osm' || !window.L) {
    return;
  }

  const L = window.L;
  const poiUrl = container.dataset.poiUrl || '';
  const statusLine = document.querySelector('[data-care-map-status]');

  const say = (text) => {
    if (statusLine) {
      statusLine.textContent = text;
    }
  };

  const map = L.map(container, {
    center: [32.08, 34.79],
    zoom: 12,
    scrollWheelZoom: false,
    attributionControl: true,
  });

  L.tileLayer(config.tiles, {
    maxZoom: 19,
    attribution: config.attribution,
  }).addTo(map);

  /* --- Rich base layer: real hospitals + clinics --------------------- */
  const poiRenderer = L.canvas({ padding: 0.4 });
  const poiLayer = L.layerGroup().addTo(map);

  const drawPois = (pois) => {
    pois.forEach((poi) => {
      const isHospital = poi.t === 'hospital';
      L.circleMarker([poi.lat, poi.lon], {
        renderer: poiRenderer,
        radius: isHospital ? 6 : 4,
        color: isHospital ? '#235a51' : '#7d958f',
        weight: 1.5,
        fillColor: isHospital ? '#2f7264' : '#a9beb7',
        fillOpacity: 0.75,
      }).bindPopup(
        '<div class="hp-map-pop"><strong>' + poi.n + '</strong><small>' + (isHospital ? 'בית חולים' : 'מרפאה') + '</small></div>',
        { closeButton: false }
      ).addTo(poiLayer);
    });
  };

  fetch(poiUrl, { credentials: 'same-origin' })
    .then((response) => (response.ok ? response.json() : null))
    .then((data) => {
      if (data && Array.isArray(data.pois)) {
        drawPois(data.pois);
        say('מוצגים ' + data.pois.length + ' בתי חולים ומרפאות מרחבי הארץ. הנתונים: © OpenStreetMap.');
      }
    })
    .catch(() => say('שכבת המוסדות אינה זמינה כרגע.'));

  /* --- Featured clients: premium, disclosed --------------------------- */
  const featured = Array.isArray(config.featuredProviders) ? config.featuredProviders : [];
  const featuredMarkers = [];

  const featuredIcon = L.divIcon({
    className: 'hp-map-featured',
    html: '<span class="hp-map-featured__pin">H</span>',
    iconSize: [38, 38],
    iconAnchor: [19, 36],
    popupAnchor: [0, -34],
  });

  featured.forEach((provider) => {
    const phoneDigits = (provider.phone || '').replace(/[^0-9+]/g, '');
    const marker = L.marker([provider.lat, provider.lon], { icon: featuredIcon, zIndexOffset: 900 });
    marker.bindPopup(
      '<div class="hp-map-pop hp-map-pop--featured">'
      + (provider.badge ? '<span class="hp-map-pop__badge">' + provider.badge + '</span>' : '')
      + '<strong>' + provider.name + '</strong>'
      + (provider.label ? '<em>' + provider.label + '</em>' : '')
      + (provider.address ? '<small>' + provider.address + '</small>' : '')
      + '<span class="hp-map-pop__actions">'
      + (phoneDigits ? '<a href="tel:' + phoneDigits + '">התקשרו</a>' : '')
      + (provider.url ? '<a href="' + provider.url + '" target="_blank" rel="noopener nofollow">לאתר</a>' : '')
      + '</span>'
      + (provider.disclosure ? '<small class="hp-map-pop__disclosure">' + provider.disclosure + '</small>' : '')
      + '</div>'
    );
    marker.specialty = provider.specialty || '';
    marker.addTo(map);
    featuredMarkers.push(marker);
  });

  /* --- Visitor location ------------------------------------------------ */
  const LocateControl = L.Control.extend({
    options: { position: 'topright' },
    onAdd() {
      const button = L.DomUtil.create('button', 'hp-map-locate');
      button.type = 'button';
      button.textContent = 'סביבי';
      L.DomEvent.on(button, 'click', (event) => {
        L.DomEvent.stop(event);
        if (!navigator.geolocation) {
          say('הדפדפן אינו משתף מיקום.');
          return;
        }
        say('מאתרים את מיקומכם…');
        navigator.geolocation.getCurrentPosition(
          (position) => {
            const here = [position.coords.latitude, position.coords.longitude];
            L.circleMarker(here, { radius: 8, color: '#805a14', fillColor: '#dfc17b', fillOpacity: 0.9, weight: 2 })
              .bindPopup('<div class="hp-map-pop"><strong>אתם כאן</strong></div>')
              .addTo(map);
            map.flyTo(here, 14);
            say('מוצגים מוסדות הבריאות סביב מיקומכם.');
          },
          () => say('שיתוף המיקום נדחה — אפשר להזיז את המפה ידנית.')
        );
      });
      return button;
    },
  });
  map.addControl(new LocateControl());

  /* --- The body drives the map ----------------------------------------- */
  const SPECIALTY_BY_REGION = {
    nose: 'plastic-surgery',
    'skin-face': 'aesthetic-medicine',
    chest: 'plastic-surgery',
    scalp: 'hair-transplant',
    movement: 'orthopedics',
  };

  // Direct model clicks report structure ids (anatomy:*) — translate them to
  // resolver regions the same way the selection card does.
  const REGION_BY_STRUCTURE = {
    nose: 'nose',
    lips: 'skin-face',
    'facial-muscles': 'skin-face',
    'jaw-muscles': 'skin-face',
    cranium: 'skin-face',
    mandible: 'skin-face',
    pectorals: 'chest',
    abdominals: 'chest',
  };

  const REGION_BY_STRUCTURE_FALLBACK = 'movement';

  const toRegion = (value) => {
    const key = String(value || '').replace(/^anatomy:/, '');
    if (Object.prototype.hasOwnProperty.call(REGION_BY_STRUCTURE, key)) {
      return REGION_BY_STRUCTURE[key];
    }
    return REGION_BY_STRUCTURE_FALLBACK;
  };

  const spotlight = (regionId) => {
    const specialty = SPECIALTY_BY_REGION[regionId] || '';
    const matches = featuredMarkers.filter(
      (marker) => marker.specialty === specialty || specialty.indexOf(marker.specialty) === 0 || marker.specialty.indexOf(specialty) === 0
    );

    if (matches.length) {
      const marker = matches[0];
      map.flyTo(marker.getLatLng(), 13);
      marker.openPopup();
      say('מוצגים שירותים מומלצים לאזור שנבחר — לצד כל המוסדות בסביבה.');
    }
  };

  window.addEventListener('hea-lth:anatomy-viewer-selection', (event) => {
    const detail = event.detail || {};
    spotlight(toRegion(detail.structureId || detail.regionId));
  });

  const regionControls = document.querySelector('[data-anatomy-region-controls]');
  if (regionControls && window.heaLthAnatomyDiscovery) {
    regionControls.addEventListener('click', () => {
      const selected = window.heaLthAnatomyDiscovery.getSelectedRegionId();
      if (selected) {
        spotlight(selected);
      }
    });
  }
})();
