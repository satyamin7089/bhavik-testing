/**
 * ASRS Forms Engine - Lean Frontend AJAX Submission (with Turnstile & Google reCAPTCHA v2/v3)
 */
(function () {
	'use strict';

	if (typeof window.ASRSFormsConfig === 'undefined') {
		return;
	}

	var config = window.ASRSFormsConfig;
	var rcCfg = config.recaptcha || {};
	var rcOn = !!(rcCfg.enabled && rcCfg.siteKey);

	function whenRecaptchaReady(cb, maxTries, onTimeout) {
		var limit = typeof maxTries === 'number' ? maxTries : 150;
		var tries = 0;

		function check() {
			if (window.grecaptcha && typeof window.grecaptcha.ready === 'function') {
				window.grecaptcha.ready(cb);
				return;
			}
			tries += 1;
			if (tries < limit) {
				setTimeout(check, 200);
			} else if (typeof onTimeout === 'function') {
				onTimeout();
			}
		}

		check();
	}

	function getRecaptchaWidgetId(formEl) {
		var el = formEl.querySelector('.asrs-g-recaptcha');
		if (!el) return null;
		var raw = el.getAttribute('data-widget-id');
		if (raw === null || raw === '') return null;
		var parsed = parseInt(raw, 10);
		return isNaN(parsed) ? null : parsed;
	}

	function renderRecaptchaV2(formEl) {
		if (!rcOn || rcCfg.version === 'v3') return;
		var el = formEl.querySelector('.asrs-g-recaptcha');
		if (!el) return;

		whenRecaptchaReady(function () {
			if (typeof window.grecaptcha.render !== 'function') return;
			if (el.getAttribute('data-widget-id') !== null) return;
			try {
				var id = window.grecaptcha.render(el, {
					sitekey: el.getAttribute('data-sitekey') || rcCfg.siteKey,
					callback: function () {
						var group = formEl.querySelector('.asrs-field-group[data-field-name="recaptcha"]');
						if (group) {
							group.classList.remove('has-error');
							var err = group.querySelector('.asrs-field-error');
							if (err) err.textContent = '';
						}
					}
				});
				el.setAttribute('data-widget-id', String(id));
			} catch (err) {}
		}, 150);
	}

	function getRecaptchaToken(formEl, formData) {
		if (!rcOn) {
			return Promise.resolve(null);
		}

		if (rcCfg.version === 'v3') {
			return new Promise(function (resolve) {
				whenRecaptchaReady(
					function () {
						try {
							window.grecaptcha
								.execute(rcCfg.siteKey, { action: rcCfg.action || 'asrs_forms_submit' })
								.then(
									function (token) {
										var hidden = formEl.querySelector('.asrs-recaptcha-token');
										if (hidden) hidden.value = token || '';
										resolve(token || '');
									},
									function () {
										resolve('');
									}
								);
						} catch (err) {
							resolve('');
						}
					},
					20,
					function () {
						resolve('');
					}
				);
			});
		}

		var widgetId = getRecaptchaWidgetId(formEl);
		var token = '';
		if (window.grecaptcha && widgetId !== null && typeof window.grecaptcha.getResponse === 'function') {
			try {
				token = window.grecaptcha.getResponse(widgetId) || '';
			} catch (err) {}
		}
		if (!token && formData) {
			token = formData.get('g-recaptcha-response') || '';
		}
		return Promise.resolve(token);
	}

	function resetRecaptcha(formEl) {
		if (!rcOn || rcCfg.version === 'v3' || !window.grecaptcha) return;
		var widgetId = getRecaptchaWidgetId(formEl);
		if (widgetId !== null && typeof window.grecaptcha.reset === 'function') {
			try {
				window.grecaptcha.reset(widgetId);
			} catch (err) {}
		}
	}

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

		renderRecaptchaV2(formEl);

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

			var formData = new FormData(formEl);

			if (rcOn && rcCfg.version !== 'v3') {
				var v2WidgetId = getRecaptchaWidgetId(formEl);
				var v2Token = '';
				if (window.grecaptcha && v2WidgetId !== null && typeof window.grecaptcha.getResponse === 'function') {
					try {
						v2Token = window.grecaptcha.getResponse(v2WidgetId) || '';
					} catch (err) {}
				}
				if (!v2Token) {
					v2Token = formData.get('g-recaptcha-response') || '';
				}
				if (!v2Token) {
					var missingMsg = (config.i18n && config.i18n.recaptchaMissing) || 'Please confirm that you are not a robot.';
					showFieldErrors(formEl, { recaptcha: missingMsg });
					setNotice(messagesEl, 'error', missingMsg);
					return;
				}
			}

			isSubmitting = true;

			if (submitBtn) {
				submitBtn.disabled = true;
				setButtonLabel(submitBtn, submitBtn.getAttribute('data-loading-label') || 'Sending...');
			}

			getRecaptchaToken(formEl, formData)
				.then(function (recaptchaToken) {
					var payload = {
						form_id: formId,
						asrs_form_nonce: formData.get('asrs_form_nonce') || '',
						asrs_rendered_at: formData.get('asrs_rendered_at') || '',
						asrs_hp_website: formData.get('asrs_hp_website') || '',
						'cf-turnstile-response': formData.get('cf-turnstile-response') || '',
						fields: collectFields(formEl)
					};

					if (recaptchaToken !== null) {
						payload['g-recaptcha-response'] = recaptchaToken;
					}

					return postAjax('asrs_submit_form', payload);
				})
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
					resetRecaptcha(formEl);
				});
		});
	}

	document.addEventListener('DOMContentLoaded', function () {
		document.querySelectorAll('.asrs-form-wrapper').forEach(initWrapper);
	});
})();
