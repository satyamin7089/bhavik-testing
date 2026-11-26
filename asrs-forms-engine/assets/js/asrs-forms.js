/**
 * ASRS Forms Engine - Lean Frontend AJAX Submission
 */
(function () {
	'use strict';

	if (typeof window.ASRSFormsConfig === 'undefined') {
		return;
	}

	var config = window.ASRSFormsConfig;

	function setNotice(container, type, text) {
		if (!container) return;
		container.innerHTML = '';
		if (!text) return;
		var box = document.createElement('div');
		box.className = 'asrs-notice-' + type;
		box.textContent = text;
		container.appendChild(box);
	}

	function clearFieldErrors(formEl) {
		formEl.querySelectorAll('.asrs-field-group').forEach(function (group) {
			group.classList.remove('has-error');
			var err = group.querySelector('.asrs-field-error');
			if (err) err.textContent = '';
		});
	}

	function showFieldErrors(formEl, fieldErrors) {
		if (!fieldErrors) return;
		Object.keys(fieldErrors).forEach(function (fieldName) {
			var group = formEl.querySelector('.asrs-field-group[data-field-name="' + fieldName + '"]');
			if (!group) return;
			group.classList.add('has-error');
			var err = group.querySelector('.asrs-field-error');
			if (err) err.textContent = fieldErrors[fieldName];
		});
	}

	function collectFields(formEl) {
		var data = {};
		formEl.querySelectorAll('[name^="fields["]').forEach(function (el) {
			var match = el.name.match(/^fields\[([^\]]+)\]$/);
			if (!match) return;
			var key = match[1];
			if (el.type === 'checkbox') {
				data[key] = el.checked ? '1' : '';
			} else if (el.type === 'radio') {
				if (el.checked) data[key] = el.value;
			} else {
				data[key] = el.value;
			}
		});
		return data;
	}

	function postAjax(action, payload) {
		var body = new URLSearchParams();
		body.append('action', action);
		body.append('nonce', config.nonce);
		body.append('page_url', window.location.href);

		Object.keys(payload).forEach(function (key) {
			var val = payload[key];
			if (val !== null && typeof val === 'object') {
				Object.keys(val).forEach(function (subKey) {
					body.append(key + '[' + subKey + ']', val[subKey]);
				});
			} else if (val !== undefined && val !== null) {
				body.append(key, String(val));
			}
		});

		return fetch(config.ajaxUrl, {
			method: 'POST',
			credentials: 'same-origin',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
			},
			body: body.toString()
		}).then(function (res) {
			return res.json();
		});
	}

	function syncSelectState(selectEl) {
		if (!selectEl) return;
		if (selectEl.value === '') {
			selectEl.classList.add('asrs-select-placeholder');
		} else {
			selectEl.classList.remove('asrs-select-placeholder');
		}
	}

	function setButtonLabel(btn, text) {
		if (!btn) return;
		var labelEl = btn.querySelector('.asrs-btn-label');
		if (labelEl) {
			labelEl.textContent = text;
		} else {
			btn.textContent = text;
		}
	}

	function initWrapper(wrapper) {
		var formId = wrapper.getAttribute('data-form-id');
		var formEl = wrapper.querySelector('form.asrs-form');
		if (!formEl) return;

		var messagesEl = formEl.querySelector('.asrs-form-messages');
		var submitBtn = formEl.querySelector('.asrs-submit-btn');
		var isSubmitting = false;

		formEl.querySelectorAll('select.asrs-select').forEach(function (sel) {
			syncSelectState(sel);
			sel.addEventListener('change', function () {
				syncSelectState(sel);
			});
		});

		formEl.addEventListener('submit', function (e) {
			e.preventDefault();
			if (isSubmitting) return;

			clearFieldErrors(formEl);
			setNotice(messagesEl, '', '');
			isSubmitting = true;

			if (submitBtn) {
				submitBtn.disabled = true;
				setButtonLabel(submitBtn, submitBtn.getAttribute('data-loading-label') || 'Sending...');
			}

			var formData = new FormData(formEl);
			var payload = {
				form_id: formId,
				asrs_form_nonce: formData.get('asrs_form_nonce') || '',
				asrs_rendered_at: formData.get('asrs_rendered_at') || '',
				asrs_hp_website: formData.get('asrs_hp_website') || '',
				'cf-turnstile-response': formData.get('cf-turnstile-response') || '',
				fields: collectFields(formEl)
			};

			postAjax('asrs_submit_form', payload)
				.then(function (resp) {
					if (resp && resp.success) {
						setNotice(messagesEl, 'success', resp.data.message);
						formEl.reset();
						formEl.querySelectorAll('select.asrs-select').forEach(syncSelectState);
					} else {
						var errData = resp && resp.data ? resp.data : {};
						setNotice(messagesEl, 'error', errData.message || config.i18n.genericError);
						if (errData.field_errors) {
							showFieldErrors(formEl, errData.field_errors);
						}
					}
				})
				.catch(function () {
					setNotice(messagesEl, 'error', config.i18n.genericError);
				})
				.finally(function () {
					isSubmitting = false;
					if (submitBtn) {
						submitBtn.disabled = false;
						setButtonLabel(submitBtn, submitBtn.getAttribute('data-default-label') || 'Send Enquiry');
					}
					if (typeof window.turnstile !== 'undefined') {
						try {
							window.turnstile.reset();
						} catch (err) {}
					}
				});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.asrs-form-wrapper').forEach(initWrapper);
	});
})();
