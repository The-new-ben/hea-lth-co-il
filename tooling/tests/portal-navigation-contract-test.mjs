/**
 * Browser-independent interaction contract for the portal navigation.
 *
 * This executes the real portal script against a deliberately small DOM
 * fixture. It does not replace Chrome visual QA, but it proves the critical
 * keyboard and mobile state transitions before a browser is attached.
 */

import assert from 'node:assert/strict';
import { readFileSync } from 'node:fs';
import vm from 'node:vm';

class MockClassList {
  #values = new Set();

  add(name) {
    this.#values.add(name);
  }

  remove(name) {
    this.#values.delete(name);
  }

  toggle(name, force) {
    const shouldAdd = typeof force === 'boolean' ? force : !this.#values.has(name);

    if (shouldAdd) {
      this.add(name);
    } else {
      this.remove(name);
    }

    return shouldAdd;
  }

  contains(name) {
    return this.#values.has(name);
  }
}

class MockElement {
  constructor({ attributes = {}, focusables = [], tagName = 'div' } = {}) {
    this.attributes = new Map(Object.entries(attributes));
    this.focusables = focusables;
    this.tagName = tagName;
    this.hidden = false;
    this.focusCount = 0;
    this.classList = new MockClassList();
    this.listeners = new Map();
  }

  getAttribute(name) {
    return this.attributes.has(name) ? this.attributes.get(name) : null;
  }

  setAttribute(name, value) {
    this.attributes.set(name, String(value));
  }

  removeAttribute(name) {
    this.attributes.delete(name);
  }

  toggleAttribute(name, force) {
    if (force) {
      this.attributes.set(name, '');
      return true;
    }

    this.attributes.delete(name);
    return false;
  }

  addEventListener(type, listener) {
    const listeners = this.listeners.get(type) || [];
    listeners.push(listener);
    this.listeners.set(type, listeners);
  }

  dispatch(type, event = {}) {
    const payload = {
      preventDefault: () => {
        payload.defaultPrevented = true;
      },
      target: this,
      ...event,
    };

    (this.listeners.get(type) || []).forEach((listener) => listener(payload));
    return payload;
  }

  querySelectorAll() {
    return this.focusables;
  }

  querySelector() {
    return null;
  }

  closest(selector) {
    if (selector === 'a[href]' && this.tagName === 'a' && this.getAttribute('href')) {
      return this;
    }

    return null;
  }

  contains(target) {
    return target === this || this.focusables.includes(target);
  }

  focus() {
    this.focusCount += 1;
  }
}

const documentListeners = new Map();
const mobileMedia = {
  matches: true,
  listeners: [],
  addEventListener(type, listener) {
    if (type === 'change') {
      this.listeners.push(listener);
    }
  },
  emitChange() {
    this.listeners.forEach((listener) => listener({ matches: this.matches }));
  },
};

const header = new MockElement();
const mobileToggle = new MockElement({ attributes: { 'aria-expanded': 'false' }, tagName: 'button' });
const primaryLink = new MockElement({ attributes: { href: '/treatments/' }, tagName: 'a' });
const panelOneLink = new MockElement({ attributes: { href: '/aesthetic-medicine-treatments/' }, tagName: 'a' });
const panelTwoLink = new MockElement({ attributes: { href: '/diagnostics/' }, tagName: 'a' });
const triggerOne = new MockElement({ attributes: { 'aria-controls': 'mega-treatments', 'aria-expanded': 'false' }, tagName: 'button' });
const triggerTwo = new MockElement({ attributes: { 'aria-controls': 'mega-diagnostics', 'aria-expanded': 'false' }, tagName: 'button' });
const panelOne = new MockElement({ focusables: [panelOneLink] });
const panelTwo = new MockElement({ focusables: [panelTwoLink] });
const primaryNavigation = new MockElement({ focusables: [triggerOne, primaryLink, triggerTwo] });
const searchDrawer = new MockElement();
searchDrawer.hidden = true;

const selectorMap = new Map([
  ['[data-site-header]', header],
  ['[data-primary-navigation]', primaryNavigation],
  ['[data-mobile-toggle]', mobileToggle],
  ['[data-search-drawer]', searchDrawer],
  ['[data-search-toggle]', null],
  ['[data-search-close]', null],
  ['#portal-search-field', null],
  ['[data-explorer]', null],
  ['[data-anatomy-teaser]', null],
]);

const document = {
  body: new MockElement(),
  querySelector(selector) {
    return selectorMap.get(selector) || null;
  },
  querySelectorAll(selector) {
    if (selector === '[data-mega-trigger]') {
      return [triggerOne, triggerTwo];
    }

    if (selector === '[data-mega-panel]') {
      return [panelOne, panelTwo];
    }

    return [];
  },
  getElementById(id) {
    return id === 'mega-treatments' ? panelOne : id === 'mega-diagnostics' ? panelTwo : null;
  },
  addEventListener(type, listener) {
    const listeners = documentListeners.get(type) || [];
    listeners.push(listener);
    documentListeners.set(type, listeners);
  },
  dispatch(type, event = {}) {
    const payload = {
      preventDefault: () => {
        payload.defaultPrevented = true;
      },
      ...event,
    };

    (documentListeners.get(type) || []).forEach((listener) => listener(payload));
    return payload;
  },
};

const window = {
  matchMedia: () => mobileMedia,
  setTimeout: (callback) => callback(),
};

const source = readFileSync(new URL('../../theme-src/hea-lth-portal/assets/js/portal.js', import.meta.url), 'utf8');
vm.runInNewContext(source, { Array, Element: MockElement, console, document, window }, { filename: 'portal.js' });

assert.equal(primaryNavigation.getAttribute('aria-hidden'), 'true', 'Closed mobile navigation must be hidden from assistive technology.');
assert.notEqual(primaryNavigation.getAttribute('inert'), null, 'Closed mobile navigation must be inert.');
assert.equal(mobileToggle.getAttribute('aria-expanded'), 'false', 'Mobile toggle must begin closed.');

mobileToggle.dispatch('click');
assert.equal(mobileToggle.getAttribute('aria-expanded'), 'true', 'Mobile toggle must expose open state.');
assert.equal(primaryNavigation.getAttribute('aria-hidden'), 'false', 'Open mobile navigation must be exposed.');
assert.equal(primaryNavigation.getAttribute('inert'), null, 'Open mobile navigation cannot remain inert.');
assert.equal(primaryNavigation.classList.contains('is-mobile-open'), true, 'Open mobile navigation must carry its visual state.');
assert.equal(triggerOne.focusCount, 1, 'Opening the mobile navigation must move focus to its first action.');

primaryNavigation.dispatch('click', { target: primaryLink });
assert.equal(mobileToggle.getAttribute('aria-expanded'), 'false', 'Following a mobile navigation link must close the menu.');

const arrowDown = triggerOne.dispatch('keydown', { key: 'ArrowDown' });
assert.equal(arrowDown.defaultPrevented, true, 'ArrowDown must claim the mega-menu interaction.');
assert.equal(triggerOne.getAttribute('aria-expanded'), 'true', 'ArrowDown must open the matching mega panel.');
assert.equal(panelOne.hidden, false, 'The matching mega panel must be visible.');
assert.equal(panelOneLink.focusCount, 1, 'ArrowDown must move focus into the mega panel.');

document.dispatch('keydown', { key: 'Escape' });
assert.equal(triggerOne.getAttribute('aria-expanded'), 'false', 'Escape must close an open mega panel.');
assert.equal(panelOne.hidden, true, 'Escape must hide the open mega panel.');
assert.equal(triggerOne.focusCount, 2, 'Escape must return focus to the mega trigger.');

mobileMedia.matches = false;
mobileMedia.emitChange();
assert.equal(primaryNavigation.getAttribute('aria-hidden'), null, 'Desktop navigation must not retain mobile-only aria-hidden state.');
assert.equal(primaryNavigation.getAttribute('inert'), null, 'Desktop navigation must not retain mobile-only inert state.');

console.log('Portal navigation interaction contract passed.');
