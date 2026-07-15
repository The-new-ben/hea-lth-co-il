/**
 * Native accessibility panel — replaces the legacy third-party toolbar.
 *
 * Adjustments follow IS 5568 / WCAG 2.1 AA good practice: text scaling,
 * higher contrast, link underlining, and motion stop. State persists per
 * visitor in localStorage and is applied before first paint by the inline
 * boot snippet printed from functions.php.
 */
(() => {
  'use strict';

  const STORAGE_KEY = 'hea-lth-a11y';
  const FLAGS = [
    { id: 'font-110', className: 'hp-a11y-font-110', label: 'טקסט מוגדל', exclusive: 'font' },
    { id: 'font-125', className: 'hp-a11y-font-125', label: 'טקסט גדול מאוד', exclusive: 'font' },
    { id: 'contrast', className: 'hp-a11y-contrast', label: 'ניגודיות מוגברת' },
    { id: 'underline', className: 'hp-a11y-underline', label: 'הדגשת קישורים' },
    { id: 'no-motion', className: 'hp-a11y-no-motion', label: 'עצירת תנועה' }
  ];

  const root = document.documentElement;

  const readState = () => {
    try {
      const raw = window.localStorage.getItem(STORAGE_KEY);
      const parsed = raw ? JSON.parse(raw) : {};
      return parsed && typeof parsed === 'object' ? parsed : {};
    } catch (error) {
      return {};
    }
  };

  const writeState = (state) => {
    try {
      window.localStorage.setItem(STORAGE_KEY, JSON.stringify(state));
    } catch (error) {
      /* private mode — adjustments still apply for this view */
    }
  };

  let state = readState();

  const applyState = () => {
    FLAGS.forEach((flag) => {
      root.classList.toggle(flag.className, Boolean(state[flag.id]));
    });
  };

  const container = document.createElement('div');
  container.className = 'hp-a11y';

  const toggle = document.createElement('button');
  toggle.type = 'button';
  toggle.className = 'hp-a11y__toggle';
  toggle.setAttribute('aria-expanded', 'false');
  toggle.setAttribute('aria-haspopup', 'true');
  toggle.setAttribute('aria-label', 'התאמות נגישות');
  // Universally recognized accessibility symbol (person, arms spread, in a ring)
  // plus an always-visible "נגישות" label so the affordance is unmistakable.
  toggle.innerHTML = '<span class="hp-a11y__icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><circle cx="12" cy="12" r="11.5" fill="none" stroke="currentColor" stroke-width="1.4" opacity="0.55"/><circle cx="12" cy="5.6" r="1.85" fill="currentColor"/><path d="M5.6 8.3c1.95.55 4.15.9 6.4.9s4.45-.35 6.4-.9l.45 1.85c-1.6.46-3.35.76-5.15.88l1.8 7.85-1.9.5-1.65-5.95h-.9l-1.65 5.95-1.9-.5 1.8-7.85c-1.8-.12-3.55-.42-5.15-.88z" fill="currentColor"/></svg></span><span class="hp-a11y__toggle-label">נגישות</span>';

  const panel = document.createElement('div');
  panel.className = 'hp-a11y__panel';
  panel.setAttribute('role', 'group');
  panel.setAttribute('aria-label', 'התאמות נגישות');
  panel.hidden = true;

  const heading = document.createElement('p');
  heading.className = 'hp-a11y__heading';
  heading.textContent = 'התאמות נגישות';
  panel.appendChild(heading);

  const buttons = new Map();

  const refreshButtons = () => {
    buttons.forEach((button, id) => {
      button.setAttribute('aria-pressed', String(Boolean(state[id])));
    });
  };

  FLAGS.forEach((flag) => {
    const button = document.createElement('button');
    button.type = 'button';
    button.className = 'hp-a11y__option';
    button.textContent = flag.label;
    button.setAttribute('aria-pressed', 'false');
    button.addEventListener('click', () => {
      const nextValue = !state[flag.id];
      if (flag.exclusive) {
        FLAGS.filter((other) => other.exclusive === flag.exclusive).forEach((other) => {
          delete state[other.id];
        });
      }
      if (nextValue) {
        state[flag.id] = true;
      } else {
        delete state[flag.id];
      }
      writeState(state);
      applyState();
      refreshButtons();
    });
    buttons.set(flag.id, button);
    panel.appendChild(button);
  });

  const reset = document.createElement('button');
  reset.type = 'button';
  reset.className = 'hp-a11y__reset';
  reset.textContent = 'איפוס התאמות';
  reset.addEventListener('click', () => {
    state = {};
    writeState(state);
    applyState();
    refreshButtons();
  });
  panel.appendChild(reset);

  const statementUrl = container.dataset.statementUrl || (window.heaLthA11y && window.heaLthA11y.statementUrl);
  const statement = document.createElement('a');
  statement.className = 'hp-a11y__statement';
  statement.href = statementUrl || '/accessibility/';
  statement.textContent = 'הצהרת נגישות';
  panel.appendChild(statement);

  const setOpen = (open) => {
    panel.hidden = !open;
    toggle.setAttribute('aria-expanded', String(open));
    if (open) {
      refreshButtons();
      const first = panel.querySelector('button');
      if (first) {
        first.focus();
      }
    }
  };

  toggle.addEventListener('click', () => {
    setOpen(panel.hidden);
    if (panel.hidden) {
      toggle.focus();
    }
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && !panel.hidden) {
      setOpen(false);
      toggle.focus();
    }
  });

  document.addEventListener('click', (event) => {
    if (!panel.hidden && !container.contains(event.target)) {
      setOpen(false);
    }
  });

  container.append(toggle, panel);

  const mount = () => {
    document.body.appendChild(container);
    applyState();
  };

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', mount);
  } else {
    mount();
  }
})();
