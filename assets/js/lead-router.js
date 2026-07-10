(function () {
	'use strict';

	const forms = document.querySelectorAll('[data-hr-lead-form]');

	forms.forEach(function (form) {
		const status = form.querySelector('[data-hr-form-status]');

		form.addEventListener('submit', async function (event) {
			event.preventDefault();

			if (!window.heaLthLeadRouter || !window.heaLthLeadRouter.endpoint) {
				if (status) {
					status.textContent = 'מערכת הפניות עדיין לא זמינה. אפשר לנסות שוב מאוחר יותר.';
				}
				return;
			}

			const payload = {};
			const formData = new FormData(form);
			formData.forEach(function (value, key) {
				payload[key] = value;
			});
			payload.source_url = window.location.href;

			const params = new URLSearchParams(window.location.search);
			['utm_source', 'utm_medium', 'utm_campaign', 'utm_term', 'utm_content'].forEach(function (key) {
				if (params.has(key)) {
					payload[key] = params.get(key);
				}
			});

			const submit = form.querySelector('[type="submit"]');
			if (submit) {
				submit.disabled = true;
			}
			if (status) {
				status.textContent = 'שולחים את הפנייה בצורה מאובטחת...';
			}

			try {
				const response = await fetch(window.heaLthLeadRouter.endpoint, {
					method: 'POST',
					headers: {
						'Content-Type': 'application/json'
					},
					body: JSON.stringify(payload)
				});
				const result = await response.json();
				if (!response.ok) {
					throw new Error(result && result.message ? result.message : 'השליחה נכשלה.');
				}
				form.reset();
				if (status) {
					status.textContent = result.message || 'קיבלנו את הפנייה. נחזור אליכם אחרי בדיקת התאמה ראשונית.';
				}
			} catch (error) {
				if (status) {
					status.textContent = error.message || 'לא הצלחנו לשלוח את הפנייה. בדקו את הפרטים ונסו שוב.';
				}
			} finally {
				if (submit) {
					submit.disabled = false;
				}
			}
		});
	});
})();
