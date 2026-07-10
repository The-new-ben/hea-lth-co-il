(function () {
	'use strict';

	const toggle = document.querySelector('[data-hr-menu-toggle]');
	const menu = document.querySelector('[data-hr-menu]');

	if (!toggle || !menu) {
		return;
	}

	toggle.addEventListener('click', function () {
		const expanded = toggle.getAttribute('aria-expanded') === 'true';
		toggle.setAttribute('aria-expanded', expanded ? 'false' : 'true');
		menu.classList.toggle('is-open', !expanded);
		document.documentElement.classList.toggle('hr-menu-open', !expanded);
	});
})();
