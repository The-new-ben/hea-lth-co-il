(() => {
  'use strict';

  const panel = document.querySelector('[data-anatomy-directory-map]');
  if (!panel) {
    return;
  }

  const canvas = panel.querySelector('[data-directory-map-canvas]');
  const status = panel.querySelector('[data-directory-map-status]');
  const results = panel.querySelector('[data-directory-map-results]');
  const mapConfig = window.heaLthDirectoryMap && typeof window.heaLthDirectoryMap === 'object'
    ? window.heaLthDirectoryMap
    : { status: 'configuration-gated', provider: 'none', reason: 'missing-public-config' };
  const endpoint = panel.dataset.mapEndpoint || '';
  const state = {
    map: null,
    mapClasses: null,
    mapPromise: null,
    markers: [],
    requestId: 0
  };
  const filterKeys = {
    specialty: 'specialty',
    bodyRegion: 'body_region',
    region: 'region',
    service: 'service'
  };

  const setStatus = (message) => {
    if (status) {
      status.textContent = message;
    }
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

  const isApprovedConfig = () => (
    mapConfig.status === 'approved'
    && mapConfig.provider === 'google-maps-js'
    && typeof mapConfig.browserKey === 'string'
    && /^[A-Za-z0-9_-]{20,}$/.test(mapConfig.browserKey)
    && typeof mapConfig.mapId === 'string'
    && /^[A-Za-z0-9_-]{6,}$/.test(mapConfig.mapId)
    && mapConfig.region === 'IL'
    && mapConfig.language === 'he'
  );

  const renderPanelMessage = (heading, copy) => {
    if (!results) {
      return;
    }

    const message = createElement('div', 'hp-anatomy-directory-map__message');
    message.append(
      createElement('strong', '', heading),
      createElement('p', '', copy)
    );
    results.replaceChildren(message);
  };

  const renderConfigurationGate = () => {
    panel.dataset.state = 'configuration-gated';
    if (canvas) {
      canvas.hidden = true;
    }
    setStatus('המפה תופעל רק לאחר אישור מפתח מוגבל, נתוני מיקום ושקיפות מסחרית.');
    renderPanelMessage(
      'המפה עדיין אינה פעילה.',
      'בחירת אזור גוף ממשיכה לעדכן מידע ומסלולי בחירה, ללא טעינת ספק מפות או נתוני מיקום לא מאומתים.'
    );
  };

  const safeEndpoint = () => {
    try {
      const url = new URL(endpoint, window.location.origin);
      return url.origin === window.location.origin ? url : null;
    } catch (error) {
      return null;
    }
  };

  const normalizedRouting = (routing) => {
    if (!routing || typeof routing !== 'object' || !routing.map || typeof routing.map !== 'object') {
      return {};
    }

    return Object.entries(filterKeys).reduce((filters, [routingKey, parameter]) => {
      const value = routing.map[routingKey];
      if (typeof value === 'string' && /^[a-z0-9-]+$/.test(value)) {
        filters[parameter] = value;
      }
      return filters;
    }, {});
  };

  const importGoogleLibraries = async () => {
    if (!window.google || !window.google.maps || typeof window.google.maps.importLibrary !== 'function') {
      throw new Error('Google Maps importLibrary is unavailable.');
    }

    const [mapsLibrary, markerLibrary] = await Promise.all([
      window.google.maps.importLibrary('maps'),
      window.google.maps.importLibrary('marker')
    ]);

    return {
      Map: mapsLibrary.Map,
      LatLngBounds: window.google.maps.LatLngBounds,
      AdvancedMarkerElement: markerLibrary.AdvancedMarkerElement
    };
  };

  const loadGoogleMaps = () => {
    if (state.mapPromise) {
      return state.mapPromise;
    }

    state.mapPromise = new Promise((resolve, reject) => {
      if (window.google && window.google.maps && typeof window.google.maps.importLibrary === 'function') {
        importGoogleLibraries().then(resolve).catch(reject);
        return;
      }

      const existing = document.querySelector('script[data-hea-lth-google-maps]');
      if (existing) {
        reject(new Error('Google Maps bootstrap is already present without an available importLibrary.'));
        return;
      }

      const source = new URL('https://maps.googleapis.com/maps/api/js');
      const callbackName = '__heaLthDirectoryMapBootstrap';
      source.searchParams.set('key', mapConfig.browserKey);
      source.searchParams.set('v', 'weekly');
      source.searchParams.set('loading', 'async');
      source.searchParams.set('language', mapConfig.language);
      source.searchParams.set('region', mapConfig.region);
      source.searchParams.set('auth_referrer_policy', 'origin');
      source.searchParams.set('map_ids', mapConfig.mapId);
      source.searchParams.set('callback', callbackName);

      const script = document.createElement('script');
      script.src = source.toString();
      script.async = true;
      script.setAttribute('data-hea-lth-google-maps', 'true');
      window[callbackName] = () => {
        delete window[callbackName];
        importGoogleLibraries().then(resolve).catch(reject);
      };
      script.addEventListener('error', () => {
        delete window[callbackName];
        reject(new Error('Google Maps script failed to load.'));
      }, { once: true });
      document.head.appendChild(script);
    }).then((classes) => {
      state.mapClasses = classes;
      return classes;
    }).catch((error) => {
      state.mapPromise = null;
      throw error;
    });

    return state.mapPromise;
  };

  const ensureMap = async () => {
    if (!canvas) {
      throw new Error('Map canvas is unavailable.');
    }

    if (state.map) {
      return state.map;
    }

    const classes = await loadGoogleMaps();
    state.map = new classes.Map(canvas, {
      center: { lat: 31.7683, lng: 35.2137 },
      zoom: 7,
      mapId: mapConfig.mapId,
      language: mapConfig.language,
      region: mapConfig.region,
      clickableIcons: false,
      fullscreenControl: true,
      streetViewControl: false,
      mapTypeControl: false
    });
    panel.dataset.state = 'ready';
    canvas.hidden = false;
    return state.map;
  };

  const clearMarkers = () => {
    state.markers.forEach((marker) => {
      marker.map = null;
    });
    state.markers = [];
  };

  const positionFor = (item) => {
    const latitude = Number(item && item.latitude);
    const longitude = Number(item && item.longitude);
    if (!Number.isFinite(latitude) || !Number.isFinite(longitude) || latitude < -90 || latitude > 90 || longitude < -180 || longitude > 180) {
      return null;
    }

    return { lat: latitude, lng: longitude };
  };

  const focusPosition = (position) => {
    if (!state.map || !position) {
      return;
    }

    state.map.panTo(position);
    state.map.setZoom(Math.max(state.map.getZoom() || 0, 13));
  };

  const renderItems = (items) => {
    if (!results) {
      return;
    }

    if (!items.length) {
      renderPanelMessage(
        'לא נמצאו מיקומים מאומתים לבחירה הזו.',
        'לא מוצגים מיקומים חלופיים, כתובות לא בדוקות או תוצאות ממומנות במקום התאמה רלוונטית.'
      );
      return;
    }

    const cards = items.map((item) => {
      const position = positionFor(item);
      if (!position || typeof item.name !== 'string' || !item.name.trim()) {
        return null;
      }

      const card = createElement('button', 'hp-anatomy-directory-map__card');
      card.type = 'button';
      card.append(
        createElement('span', '', item.kind === 'clinic' ? 'מרפאה או ארגון' : 'איש או אשת מקצוע'),
        createElement('strong', '', item.name.trim())
      );

      if (typeof item.city === 'string' && item.city.trim()) {
        card.appendChild(createElement('small', '', item.city.trim()));
      }

      const precision = item.precision === 'exact' ? 'מיקום מאושר להצגה' : 'מיקום ברמת עיר שאושר להצגה';
      card.appendChild(createElement('em', '', precision));
      card.addEventListener('click', () => focusPosition(position));
      return card;
    }).filter(Boolean);

    if (!cards.length) {
      renderPanelMessage(
        'לא נמצאו מיקומים תקינים להצגה.',
        'רשומות עם נתוני מיקום חסרים או לא תקינים אינן מוצגות על המפה.'
      );
      return;
    }

    results.replaceChildren(...cards);
  };

  const placeMarkers = (items) => {
    if (!state.map || !state.mapClasses) {
      return;
    }

    clearMarkers();
    const bounds = new state.mapClasses.LatLngBounds();
    let count = 0;

    items.forEach((item) => {
      const position = positionFor(item);
      if (!position || typeof item.name !== 'string' || !item.name.trim()) {
        return;
      }

      const marker = new state.mapClasses.AdvancedMarkerElement({
        map: state.map,
        position,
        title: item.name.trim()
      });
      state.markers.push(marker);
      bounds.extend(position);
      count += 1;
    });

    if (count === 1) {
      const single = positionFor(items.find((item) => positionFor(item)));
      if (single) {
        state.map.setCenter(single);
        state.map.setZoom(13);
      }
    } else if (count > 1) {
      state.map.fitBounds(bounds, 44);
    }
  };

  const requestItems = async (filters) => {
    const requestUrl = safeEndpoint();
    if (!requestUrl) {
      throw new Error('Map endpoint is not same-origin.');
    }

    Object.entries(filters).forEach(([key, value]) => requestUrl.searchParams.set(key, value));
    requestUrl.searchParams.set('limit', '12');
    const response = await window.fetch(requestUrl.toString(), { credentials: 'same-origin' });
    if (!response.ok) {
      throw new Error(`Map request returned ${response.status}`);
    }

    const payload = await response.json();
    return payload && Array.isArray(payload.items) ? payload.items : [];
  };

  const updateFromResolution = async (detail) => {
    const filters = normalizedRouting(detail && detail.routing);
    if (!Object.keys(filters).length) {
      setStatus('לא נבחר הקשר מפה למיקום הנוכחי. אפשר להמשיך במידע ובאינדקס.');
      renderPanelMessage('אין מפת שירותים להקשר הזה.', 'המערכת אינה מנחשת התאמות גאוגרפיות או מסלולי שירות.');
      return;
    }

    const requestId = ++state.requestId;
    panel.dataset.state = 'loading';
    setStatus('טוענים מיקומים מאומתים לפי האזור וההקשר שנבחרו.');

    try {
      await ensureMap();
      const items = await requestItems(filters);
      if (requestId !== state.requestId) {
        return;
      }

      panel.dataset.state = items.length ? 'ready' : 'empty';
      setStatus(items.length ? `נמצאו ${items.length} מיקומים מאומתים להצגה.` : 'לא נמצאו מיקומים מאומתים להצגה.');
      placeMarkers(items);
      renderItems(items);
    } catch (error) {
      if (requestId !== state.requestId) {
        return;
      }

      panel.dataset.state = 'error';
      if (canvas) {
        canvas.hidden = true;
      }
      setStatus('לא ניתן לטעון את המפה כרגע. מסלולי המידע והאינדקס נשארים זמינים.');
      renderPanelMessage('המפה אינה זמינה כרגע.', 'לא מוצגים נתוני מיקום חלופיים או מיקומים לא מאומתים.');
    }
  };

  if (!isApprovedConfig()) {
    renderConfigurationGate();
    return;
  }

  panel.dataset.state = 'waiting';
  if (canvas) {
    canvas.hidden = true;
  }
  setStatus('בחרו אזור גוף והקשר כדי לעדכן את מפת השירותים.');
  renderPanelMessage('המפה מוכנה לבחירה.', 'ספק המפה ייטען רק לאחר בחירת הקשר עם פילטרים סמנטיים מאושרים.');

  window.addEventListener('hea-lth:anatomy-resolution-updated', (event) => {
    void updateFromResolution(event.detail);
  });
})();
