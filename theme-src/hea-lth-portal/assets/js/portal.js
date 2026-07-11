(() => {
  'use strict';

  const header = document.querySelector('[data-site-header]');
  const primaryNavigation = document.querySelector('[data-primary-navigation]');
  const mobileToggle = document.querySelector('[data-mobile-toggle]');
  const searchDrawer = document.querySelector('[data-search-drawer]');
  const searchToggle = document.querySelector('[data-search-toggle]');
  const searchClose = document.querySelector('[data-search-close]');
  const searchField = document.querySelector('#portal-search-field');
  const megaTriggers = Array.from(document.querySelectorAll('[data-mega-trigger]'));
  const megaPanels = Array.from(document.querySelectorAll('[data-mega-panel]'));
  const mobileMedia = typeof window.matchMedia === 'function'
    ? window.matchMedia('(max-width: 900px)')
    : null;

  const getFocusableElements = (container) => {
    if (!container) {
      return [];
    }

    return Array.from(container.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), select:not([disabled]), textarea:not([disabled])'))
      .filter((element) => !element.hidden && element.getAttribute('aria-hidden') !== 'true');
  };

  const closeMegaPanels = (returnFocus = false) => {
    let openTrigger = null;

    megaTriggers.forEach((trigger) => {
      if (trigger.getAttribute('aria-expanded') === 'true') {
        openTrigger = trigger;
      }
      trigger.setAttribute('aria-expanded', 'false');
    });

    megaPanels.forEach((panel) => {
      panel.hidden = true;
    });

    if (returnFocus && openTrigger) {
      openTrigger.focus();
    }
  };

  const openMegaPanel = (trigger, focusFirstItem = false) => {
    const panelId = trigger ? trigger.getAttribute('aria-controls') : null;
    const panel = panelId ? document.getElementById(panelId) : null;

    if (!trigger || !panel) {
      return;
    }

    closeMegaPanels();
    trigger.setAttribute('aria-expanded', 'true');
    panel.hidden = false;

    if (focusFirstItem) {
      const firstItem = getFocusableElements(panel)[0];
      if (firstItem) {
        window.setTimeout(() => firstItem.focus(), 0);
      }
    }
  };

  const closeSearch = (returnFocus = false) => {
    if (!searchDrawer || searchDrawer.hidden) {
      return;
    }

    searchDrawer.hidden = true;
    if (searchToggle) {
      searchToggle.setAttribute('aria-expanded', 'false');
      if (returnFocus) {
        searchToggle.focus();
      }
    }
  };

  const setMobileNavigation = (isOpen, focusFirstItem = false) => {
    if (!primaryNavigation || !mobileToggle) {
      return;
    }

    const isMobileViewport = !mobileMedia || mobileMedia.matches;

    if (!isMobileViewport) {
      primaryNavigation.classList.remove('is-mobile-open');
      primaryNavigation.removeAttribute('aria-hidden');
      primaryNavigation.removeAttribute('inert');
      mobileToggle.setAttribute('aria-expanded', 'false');
      document.body.classList.remove('hp-navigation-open');
      return;
    }

    primaryNavigation.classList.toggle('is-mobile-open', isOpen);
    primaryNavigation.setAttribute('aria-hidden', String(!isOpen));
    primaryNavigation.toggleAttribute('inert', !isOpen);
    mobileToggle.setAttribute('aria-expanded', String(isOpen));
    document.body.classList.toggle('hp-navigation-open', isOpen);

    if (!isOpen) {
      closeMegaPanels();
      return;
    }

    closeSearch();

    if (focusFirstItem) {
      const firstItem = getFocusableElements(primaryNavigation)[0];
      if (firstItem) {
        window.setTimeout(() => firstItem.focus(), 0);
      }
    }
  };

  const syncMobileNavigation = () => {
    setMobileNavigation(false);
  };

  if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
      const isOpen = mobileToggle.getAttribute('aria-expanded') !== 'true';
      setMobileNavigation(isOpen, isOpen);
    });
  }

  megaTriggers.forEach((trigger) => {
    const panelId = trigger.getAttribute('aria-controls');
    const panel = panelId ? document.getElementById(panelId) : null;

    if (!panel) {
      return;
    }

    trigger.addEventListener('click', () => {
      const shouldOpen = trigger.getAttribute('aria-expanded') !== 'true';

      if (shouldOpen) {
        openMegaPanel(trigger);
      } else {
        closeMegaPanels();
      }
    });

    trigger.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown') {
        event.preventDefault();
        openMegaPanel(trigger, true);
      }
    });
  });

  if (primaryNavigation) {
    primaryNavigation.addEventListener('click', (event) => {
      const target = event.target instanceof Element ? event.target.closest('a[href]') : null;

      if (target && (!mobileMedia || mobileMedia.matches)) {
        setMobileNavigation(false);
      }
    });
  }

  if (mobileMedia) {
    if (typeof mobileMedia.addEventListener === 'function') {
      mobileMedia.addEventListener('change', syncMobileNavigation);
    } else if (typeof mobileMedia.addListener === 'function') {
      mobileMedia.addListener(syncMobileNavigation);
    }
  }

  syncMobileNavigation();

  if (searchToggle && searchDrawer) {
    searchToggle.addEventListener('click', () => {
      const shouldOpen = searchDrawer.hidden;
      closeMegaPanels();
      searchDrawer.hidden = !shouldOpen;
      searchToggle.setAttribute('aria-expanded', String(shouldOpen));

      if (shouldOpen && searchField) {
        window.setTimeout(() => searchField.focus(), 40);
      }
    });
  }

  if (searchClose) {
    searchClose.addEventListener('click', () => closeSearch(true));
  }

  document.addEventListener('pointerdown', (event) => {
    if (header && !header.contains(event.target)) {
      closeMegaPanels();
      closeSearch();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
      return;
    }

    if (searchDrawer && !searchDrawer.hidden) {
      closeSearch(true);
      return;
    }

    if (megaTriggers.some((trigger) => trigger.getAttribute('aria-expanded') === 'true')) {
      closeMegaPanels(true);
      return;
    }

    if (mobileToggle && mobileToggle.getAttribute('aria-expanded') === 'true') {
      setMobileNavigation(false);
      mobileToggle.focus();
    }
  });

  const explorer = document.querySelector('[data-explorer]');
  if (explorer) {
    const explorerTitle = explorer.querySelector('[data-explorer-title]');
    const explorerCopy = explorer.querySelector('[data-explorer-copy]');
    const explorerOptions = Array.from(explorer.querySelectorAll('[data-explorer-option]'));

    explorerOptions.forEach((option) => {
      option.addEventListener('click', () => {
        explorerOptions.forEach((item) => item.classList.remove('is-active'));
        option.classList.add('is-active');

        if (explorerTitle) {
          explorerTitle.textContent = option.dataset.title || option.textContent.trim();
        }

        if (explorerCopy) {
          explorerCopy.textContent = option.dataset.copy || '';
        }
      });
    });
  }

  const anatomyTeaser = document.querySelector('[data-anatomy-teaser]');
  if (anatomyTeaser) {
    const output = anatomyTeaser.querySelector('[data-anatomy-output]');
    const regions = Array.from(anatomyTeaser.querySelectorAll('[data-anatomy-region]'));

    regions.forEach((region) => {
      region.addEventListener('click', () => {
        regions.forEach((item) => item.classList.remove('is-active'));
        region.classList.add('is-active');

        if (output) {
          output.textContent = region.dataset.region || region.textContent.trim();
        }
      });
    });
  }
})();
