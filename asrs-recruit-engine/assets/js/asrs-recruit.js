(function () {
	'use strict';

	// 1. Careers directory live filtering.
	document.querySelectorAll('[data-asrs-careers]').forEach(function (portal) {
		var cards = Array.prototype.slice.call(portal.querySelectorAll('[data-asrs-job-card]'));
		if (!cards.length) {
			return;
		}

		var searchEl = portal.querySelector('[data-asrs-filter="search"]');
		var deptEl = portal.querySelector('[data-asrs-filter="department"]');
		var locEl = portal.querySelector('[data-asrs-filter="location"]');
		var typeEl = portal.querySelector('[data-asrs-filter="type"]');
		var countEl = portal.querySelector('[data-asrs-visible-count]');
		var emptyEl = portal.querySelector('[data-asrs-no-results]');

		function applyFilters() {
			var q = searchEl ? searchEl.value.trim().toLowerCase() : '';
			var dept = deptEl ? deptEl.value : '';
			var loc = locEl ? locEl.value : '';
			var type = typeEl ? typeEl.value : '';
			var visible = 0;

			cards.forEach(function (card) {
				var matchQ = !q || (card.getAttribute('data-keywords') || '').indexOf(q) !== -1;
				var matchDept = !dept || card.getAttribute('data-department') === dept;
				var matchLoc = !loc || card.getAttribute('data-location') === loc;
				var matchType = !type || card.getAttribute('data-type') === type;
				var show = matchQ && matchDept && matchLoc && matchType;
				card.style.display = show ? '' : 'none';
				if (show) {
					visible++;
				}
			});

			if (countEl) {
				countEl.textContent = String(visible);
			}
			if (emptyEl) {
				emptyEl.hidden = visible > 0;
			}
		}

		[searchEl, deptEl, locEl, typeEl].forEach(function (el) {
			if (el) {
				el.addEventListener('input', applyFilters);
				el.addEventListener('change', applyFilters);
			}
		});
	});

	// Optional Google reCAPTCHA (configured in Recruit → Settings).
	var rcCfg = (window.asrsRecruitConfig && window.asrsRecruitConfig.recaptcha) || {};
	var rcOn = !!(rcCfg.enabled && rcCfg.siteKey);

	function whenRecaptchaReady(cb, tries) {
		tries = tries || 0;
		if (window.grecaptcha && typeof window.grecaptcha.ready === 'function') {
			window.grecaptcha.ready(cb);
			return;
		}
		if (tries < 150) {
			setTimeout(function () { whenRecaptchaReady(cb, tries + 1); }, 200);
		}
	}

	function recaptchaWidgetId(form) {
		var el = form.querySelector('.asrs-g-recaptcha');
		if (!el) {
			return null;
		}
		var id = el.getAttribute('data-widget-id');
		return id === null ? null : parseInt(id, 10);
	}

	if (rcOn && rcCfg.version !== 'v3') {
		whenRecaptchaReady(function () {
			if (typeof window.grecaptcha.render !== 'function') {
				return;
			}
			document.querySelectorAll('.asrs-recruit-form .asrs-g-recaptcha').forEach(function (el) {
				if (el.getAttribute('data-widget-id') !== null) {
					return;
				}
				try {
					var id = window.grecaptcha.render(el, { sitekey: el.getAttribute('data-sitekey') || rcCfg.siteKey });
					el.setAttribute('data-widget-id', String(id));
				} catch (err) {}
			});
		});
	}

	function getRecaptchaToken(form) {
		if (!rcOn) {
			return Promise.resolve(null);
		}
		if (rcCfg.version === 'v3') {
			return new Promise(function (resolve) {
				if (!window.grecaptcha || typeof window.grecaptcha.ready !== 'function') {
					resolve('');
					return;
				}
				window.grecaptcha.ready(function () {
					try {
						window.grecaptcha.execute(rcCfg.siteKey, { action: rcCfg.action || 'asrs_recruit_apply' })
							.then(function (token) { resolve(token || ''); }, function () { resolve(''); });
					} catch (err) {
						resolve('');
					}
				});
			});
		}
		var id = recaptchaWidgetId(form);
		var token = '';
		if (window.grecaptcha && id !== null) {
			try { token = window.grecaptcha.getResponse(id) || ''; } catch (err) {}
		}
		return Promise.resolve(token);
	}

	function resetRecaptcha(form) {
		if (!rcOn || rcCfg.version === 'v3' || !window.grecaptcha) {
			return;
		}
		var id = recaptchaWidgetId(form);
		if (id !== null) {
			try { window.grecaptcha.reset(id); } catch (err) {}
		}
	}

	// 2. Application form multipart AJAX submission.
	document.querySelectorAll('.asrs-recruit-form').forEach(function (form) {
		var fileInput = form.querySelector('.asrs-file-input');
		var fileLabel = form.querySelector('[data-asrs-file-label]');
		var submitBtn = form.querySelector('.asrs-recruit-submit');
		var btnLabel = submitBtn ? submitBtn.querySelector('.asrs-btn-label') : null;
		var feedbackEl = form.querySelector('[data-asrs-feedback]');

		if (fileInput && fileLabel) {
			fileInput.addEventListener('change', function () {
				var file = fileInput.files && fileInput.files[0];
				if (!file) {
					return;
				}
				var maxMb = (window.asrsRecruitConfig && window.asrsRecruitConfig.maxResumeMb) ? window.asrsRecruitConfig.maxResumeMb : 5;
				if (file.size > maxMb * 1024 * 1024) {
					var errEl = form.querySelector('[data-error-for="resume"]');
					if (errEl) {
						errEl.textContent = (window.asrsRecruitConfig && window.asrsRecruitConfig.fileTooLarge) || 'File is too large.';
					}
					fileInput.value = '';
					return;
				}
				var sizeKb = Math.max(1, Math.round(file.size / 1024));
				fileLabel.textContent = file.name + ' (' + sizeKb + ' KB)';
				var resumeErr = form.querySelector('[data-error-for="resume"]');
				if (resumeErr) {
					resumeErr.textContent = '';
				}
			});
		}

		form.addEventListener('submit', function (e) {
			e.preventDefault();

			form.querySelectorAll('.asrs-field-error').forEach(function (el) {
				el.textContent = '';
			});
			if (feedbackEl) {
				feedbackEl.hidden = true;
				feedbackEl.className = 'asrs-form-feedback';
				feedbackEl.textContent = '';
			}

			var cfg = window.asrsRecruitConfig || {};
			if (submitBtn) {
				submitBtn.disabled = true;
			}
			if (btnLabel && cfg.submitting) {
				btnLabel.textContent = cfg.submitting;
			}

			var formData = new FormData(form);

			getRecaptchaToken(form)
				.then(function (recaptchaToken) {
					if (recaptchaToken !== null) {
						formData.set('g-recaptcha-response', recaptchaToken);
					}
					return fetch(cfg.ajaxUrl || '/wp-admin/admin-ajax.php', {
						method: 'POST',
						body: formData,
						credentials: 'same-origin'
					});
				})
				.then(function (res) {
					return res.json();
				})
				.then(function (payload) {
					if (payload && payload.success) {
						if (feedbackEl) {
							feedbackEl.hidden = false;
							feedbackEl.classList.add('is-success');
							feedbackEl.textContent = (payload.data && payload.data.message) || 'Application submitted.';
						}
						form.reset();
						if (fileLabel) {
							fileLabel.textContent = 'Click to choose your resume file';
						}
					} else {
						var data = (payload && payload.data) || {};
						if (data.fields) {
							Object.keys(data.fields).forEach(function (key) {
								var fieldErr = form.querySelector('[data-error-for="' + key + '"]');
								if (fieldErr) {
									fieldErr.textContent = data.fields[key];
								}
							});
						}
						if (feedbackEl) {
							feedbackEl.hidden = false;
							feedbackEl.classList.add('is-error');
							feedbackEl.textContent = data.message || cfg.genericError || 'Submission failed.';
						}
					}
				})
				.catch(function () {
					if (feedbackEl) {
						feedbackEl.hidden = false;
						feedbackEl.classList.add('is-error');
						feedbackEl.textContent = cfg.genericError || 'Network error.';
					}
				})
				.finally(function () {
					if (submitBtn) {
						submitBtn.disabled = false;
					}
					if (btnLabel && cfg.submitLabel) {
						btnLabel.textContent = cfg.submitLabel;
					}
					resetRecaptcha(form);
				});
		});
	});
})();
