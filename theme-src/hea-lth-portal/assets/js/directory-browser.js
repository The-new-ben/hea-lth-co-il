(() => {
  'use strict';

  const browser = document.querySelector('[data-directory-browser]');
  if (!browser) {
    return;
  }

  const status = browser.querySelector('[data-directory-status]');
  const results = browser.querySelector('[data-directory-results]');
  const allowedFilterKeys = new Set(['specialty', 'region', 'service', 'body_region']);

  document.querySelectorAll('[data-directory-focus]').forEach((control) => {
    control.addEventListener('click', () => {
      const targetId = control.dataset.directoryFocus;
      const target = targetId ? document.getElementById(targetId) : null;

      if (!target) {
        return;
      }

      target.scrollIntoView({ behavior: 'smooth', block: 'center' });
      target.focus({ preventScroll: true });
    });
  });

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

  const stringList = (value) => (
    Array.isArray(value)
      ? value.filter((entry) => typeof entry === 'string' && entry.trim().length > 0)
      : []
  );

  const getFilters = () => {
    try {
      const parsed = JSON.parse(browser.dataset.directoryFilters || '{}');
      if (!parsed || typeof parsed !== 'object' || Array.isArray(parsed)) {
        return {};
      }

      return Object.entries(parsed).reduce((filters, [key, value]) => {
        if (allowedFilterKeys.has(key) && typeof value === 'string' && value.length > 0) {
          filters[key] = value;
        }
        return filters;
      }, {});
    } catch (error) {
      return {};
    }
  };

  const createFact = (label, values) => {
    const list = stringList(values);
    if (!list.length) {
      return null;
    }

    const item = createElement('li', 'hp-directory-result-card__fact');
    const key = createElement('span', '', label);
    const value = createElement('strong', '', list.join(' · '));
    item.append(key, value);
    return item;
  };

  const createCard = (item) => {
    if (!item || typeof item !== 'object' || typeof item.name !== 'string' || !item.name.trim()) {
      return null;
    }

    const card = createElement('article', 'hp-directory-result-card');
    const eyebrow = createElement('p', 'hp-directory-result-card__kind', item.kind === 'clinic' ? 'מרפאה או ארגון' : 'איש או אשת מקצוע');
    const heading = createElement('h3', '', item.name.trim());
    const facts = createElement('ul', 'hp-directory-result-card__facts');
    const specialty = createFact('תחום', item.specialties);
    const bodyRegion = createFact('אזור גוף', item.bodyRegions);
    const area = createFact('אזור שירות', item.areas);
    const service = createFact('שירות', item.services);

    [specialty, bodyRegion, area, service].filter(Boolean).forEach((fact) => facts.appendChild(fact));

    card.append(eyebrow, heading);

    if (typeof item.city === 'string' && item.city.trim()) {
      card.appendChild(createElement('p', 'hp-directory-result-card__city', item.city.trim()));
    }

    if (facts.childElementCount) {
      card.appendChild(facts);
    }

    if (typeof item.lastVerified === 'string' && item.lastVerified.trim()) {
      card.appendChild(createElement('p', 'hp-directory-result-card__verified', `נבדק להצגה: ${item.lastVerified.trim()}`));
    }

    if (typeof item.disclosure === 'string' && item.disclosure.trim()) {
      card.appendChild(createElement('p', 'hp-directory-result-card__disclosure', item.disclosure.trim()));
    }

    return card;
  };

  const renderEmpty = (heading, copy) => {
    if (!results) {
      return;
    }

    const empty = createElement('div', 'hp-directory-browser__empty');
    empty.append(
      createElement('strong', '', heading),
      createElement('p', '', copy)
    );
    results.replaceChildren(empty);
  };

  const renderItems = (items) => {
    if (!results) {
      return;
    }

    const cards = items.map(createCard).filter(Boolean);
    if (!cards.length) {
      renderEmpty(
        'אין כרגע רשומות מאומתות להצגה לפי הבחירה הזו.',
        'אפשר לעדכן את התחום או האזור, או לחזור למדריכים ולמסלולי הבחירה.'
      );
      return;
    }

    results.replaceChildren(...cards);
  };

  if (browser.dataset.directoryPreview === 'true') {
    setStatus('תצוגה מקומית: תוצאות יוצגו כאן רק לאחר חיבור WordPress ורשומות מאומתות.');
    renderEmpty(
      'זו תצוגת מבנה, ללא נתוני דמה.',
      'בסביבת WordPress התיבה קוראת רק את האינדקס המאומת.'
    );
    return;
  }

  const apiUrl = browser.dataset.apiUrl;
  if (!apiUrl || !results) {
    setStatus('לא הוגדר מקור נתונים לאינדקס.');
    return;
  }

  let requestUrl;
  try {
    requestUrl = new URL(apiUrl, window.location.origin);
  } catch (error) {
    setStatus('לא ניתן להכין את בקשת האינדקס.');
    return;
  }

  Object.entries(getFilters()).forEach(([key, value]) => requestUrl.searchParams.set(key, value));
  requestUrl.searchParams.set('limit', '12');
  browser.dataset.state = 'loading';
  setStatus('טוענים רשומות מאומתות.');

  window.fetch(requestUrl.toString(), { credentials: 'same-origin' })
    .then((response) => {
      if (!response.ok) {
        throw new Error(`Directory request returned ${response.status}`);
      }
      return response.json();
    })
    .then((payload) => {
      const items = payload && Array.isArray(payload.items) ? payload.items : [];
      browser.dataset.state = items.length ? 'ready' : 'empty';
      setStatus(items.length ? `נמצאו ${items.length} רשומות מאומתות להצגה.` : 'לא נמצאו רשומות מאומתות להצגה.');
      renderItems(items);
    })
    .catch(() => {
      browser.dataset.state = 'error';
      setStatus('לא ניתן לטעון את האינדקס כרגע. אפשר להמשיך במדריכים ובמסלולי הבחירה.');
      renderEmpty(
        'האינדקס אינו זמין כרגע.',
        'לא מוצגות כאן תוצאות חלופיות או פרופילים לא מאומתים.'
      );
    });
})();
