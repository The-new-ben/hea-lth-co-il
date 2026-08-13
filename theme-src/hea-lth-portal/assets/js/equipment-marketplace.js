(() => {
  'use strict';

  const marketplace = document.querySelector('[data-equipment-marketplace]');
  if (!marketplace) {
    return;
  }

  const cards = Array.from(marketplace.querySelectorAll('[data-equipment-card]'));
  const choices = Array.from(marketplace.querySelectorAll('[data-compare-equipment]'));
  const search = marketplace.querySelector('[data-equipment-search]');
  const family = marketplace.querySelector('[data-equipment-family]');
  const supplier = marketplace.querySelector('[data-equipment-supplier]');
  const resultCount = marketplace.querySelector('[data-equipment-count]');
  const empty = marketplace.querySelector('[data-equipment-empty]');
  const panel = marketplace.querySelector('[data-comparison-panel]');
  const tableHead = marketplace.querySelector('[data-comparison-head]');
  const tableBody = marketplace.querySelector('[data-comparison-body]');
  const clear = marketplace.querySelector('[data-comparison-clear]');
  const status = marketplace.querySelector('[data-comparison-status]');
  const hiddenFields = document.querySelector('[data-equipment-hidden]');
  const selectedWrap = document.querySelector('[data-selected-equipment-wrap]');
  const selectedSummary = document.querySelector('[data-selected-equipment-summary]');
  const maximum = 4;

  const normalize = (value) => String(value || '').trim().toLocaleLowerCase('he');
  const selectedChoices = () => choices.filter((choice) => choice.checked);

  const appendCell = (row, tag, value, scope = '') => {
    const cell = document.createElement(tag);
    cell.textContent = value;
    if (scope) {
      cell.scope = scope;
    }
    row.appendChild(cell);
  };

  const renderComparison = () => {
    const selected = selectedChoices();
    panel.hidden = selected.length === 0;
    choices.forEach((choice) => {
      choice.disabled = selected.length >= maximum && !choice.checked;
    });

    if (status) {
      status.textContent = selected.length
        ? `${selected.length} מתוך ${maximum} מערכות נבחרו להשוואה`
        : 'אפשר לבחור עד ארבע מערכות להשוואה';
    }

    tableHead.replaceChildren();
    tableBody.replaceChildren();
    if (hiddenFields) {
      hiddenFields.replaceChildren();
    }
    if (selectedSummary) {
      selectedSummary.replaceChildren();
    }
    if (selectedWrap) {
      selectedWrap.hidden = selected.length === 0;
    }

    if (!selected.length) {
      return;
    }

    const headingRow = document.createElement('tr');
    appendCell(headingRow, 'th', 'מאפיין', 'col');
    selected.forEach((choice) => appendCell(headingRow, 'th', choice.dataset.title, 'col'));
    tableHead.appendChild(headingRow);

    [
      ['טכנולוגיה', 'technology'],
      ['תחום שימוש', 'familyLabel'],
      ['ספק', 'supplierLabel']
    ].forEach(([label, key]) => {
      const row = document.createElement('tr');
      appendCell(row, 'th', label, 'row');
      selected.forEach((choice) => appendCell(row, 'td', choice.dataset[key] || '—'));
      tableBody.appendChild(row);
    });

    selected.forEach((choice) => {
      if (hiddenFields) {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'equipment[]';
        input.value = choice.value;
        hiddenFields.appendChild(input);
      }
      if (selectedSummary) {
        const item = document.createElement('li');
        item.textContent = choice.dataset.title;
        selectedSummary.appendChild(item);
      }
    });
  };

  const filterCards = () => {
    const query = normalize(search ? search.value : '');
    const familyValue = family ? family.value : '';
    const supplierValue = supplier ? supplier.value : '';
    let visible = 0;

    cards.forEach((card) => {
      const matchesQuery = !query || normalize(card.textContent).includes(query);
      const matchesFamily = !familyValue || card.dataset.family === familyValue;
      const matchesSupplier = !supplierValue || card.dataset.supplier === supplierValue;
      card.hidden = !(matchesQuery && matchesFamily && matchesSupplier);
      if (!card.hidden) {
        visible += 1;
      }
    });

    if (resultCount) {
      resultCount.textContent = String(visible);
    }
    if (empty) {
      empty.hidden = visible !== 0;
    }
  };

  choices.forEach((choice) => {
    choice.addEventListener('change', () => {
      if (selectedChoices().length > maximum) {
        choice.checked = false;
      }
      renderComparison();
    });
  });

  [search, family, supplier].forEach((control) => {
    if (control) {
      control.addEventListener(control === search ? 'input' : 'change', filterCards);
    }
  });

  if (clear) {
    clear.addEventListener('click', () => {
      choices.forEach((choice) => {
        choice.checked = false;
      });
      renderComparison();
      choices[0]?.focus();
    });
  }

  filterCards();
  renderComparison();
})();
