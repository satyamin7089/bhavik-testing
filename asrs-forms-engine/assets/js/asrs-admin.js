/**
 * ASRS Forms Engine - WordPress Admin Interactions
 */
(function ($) {
	'use strict';

	if (typeof window.ASRSAdminConfig === 'undefined') {
		return;
	}

	var cfg = window.ASRSAdminConfig;

	function showBanner(containerSelector, type, message) {
		var $el = $(containerSelector);
		if (!$el.length) {
			window.alert(message);
			return;
		}
		var cls = type === 'success' ? 'notice-success' : 'notice-error';
		$el.html('<div class="notice ' + cls + ' is-dismissible"><p>' + $('<div>').text(message).html() + '</p></div>');
	}

	// Copy shortcode button
	$(document).on('click', '.asrs-copy-btn', function () {
		var $btn = $(this);
		var text = $btn.data('copy');
		if (!text) return;

		var orig = $btn.text();
		if (navigator.clipboard && navigator.clipboard.writeText) {
			navigator.clipboard.writeText(String(text)).then(function () {
				$btn.text(cfg.i18n.copied);
				setTimeout(function () { $btn.text(orig); }, 1500);
			});
		} else {
			var $tmp = $('<input>').val(String(text)).appendTo('body').select();
			document.execCommand('copy');
			$tmp.remove();
			$btn.text(cfg.i18n.copied);
			setTimeout(function () { $btn.text(orig); }, 1500);
		}
	});

	// Bulk select-all checkbox
	$(document).on('change', '.asrs-select-all', function () {
		var checked = $(this).is(':checked');
		$(this).closest('table').find('tbody input[type="checkbox"]').prop('checked', checked);
	});

	// reCAPTCHA version selector: show v3 minimum score row only when v3 is selected
	$(document).on('change', '.asrs-recaptcha-version-select', function () {
		var isV3 = $(this).val() === 'v3';
		var $table = $(this).closest('table');
		$table.find('.asrs-recaptcha-v3-row').toggle(isV3);
	});

	// Zoho: Test Connection
	$('#asrs-test-zoho-btn').on('click', function () {
		var $btn = $(this);
		var orig = $btn.text();
		$btn.prop('disabled', true).text(cfg.i18n.testing);

		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_test_zoho_connection',
			nonce: cfg.nonce
		}).done(function (res) {
			if (res && res.success) {
				showBanner('#asrs-zoho-action-feedback', 'success', res.data.message);
				if (res.data.org) {
					$('#asrs-org-company').text(res.data.org.company_name || '—');
					$('#asrs-org-type').text((res.data.org.type || '—').toUpperCase());
					$('#asrs-org-tz').text((res.data.org.time_zone || '') + ' / ' + (res.data.org.currency || ''));
				}
			} else {
				showBanner('#asrs-zoho-action-feedback', 'error', (res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).fail(function () {
			showBanner('#asrs-zoho-action-feedback', 'error', cfg.i18n.errorGeneric);
		}).always(function () {
			$btn.prop('disabled', false).text(orig);
		});
	});

	// Zoho: Sync Modules
	$('#asrs-sync-modules-btn').on('click', function () {
		var $btn = $(this);
		var orig = $btn.text();
		$btn.prop('disabled', true).text(cfg.i18n.syncingModules);

		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_sync_zoho_modules',
			nonce: cfg.nonce
		}).done(function (res) {
			if (res && res.success) {
				showBanner('#asrs-zoho-action-feedback', 'success', res.data.message + ' Reloading...');
				setTimeout(function () { window.location.reload(); }, 900);
			} else {
				showBanner('#asrs-zoho-action-feedback', 'error', (res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).fail(function () {
			showBanner('#asrs-zoho-action-feedback', 'error', cfg.i18n.errorGeneric);
		}).always(function () {
			$btn.prop('disabled', false).text(orig);
		});
	});

	// Zoho: Exchange Grant Token
	$('#asrs-exchange-grant-btn').on('click', function () {
		var $btn = $(this);
		var grantToken = $('#asrs-grant-token-input').val().trim();
		if (!grantToken) return;

		$btn.prop('disabled', true);
		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_exchange_grant_token',
			nonce: cfg.nonce,
			grant_token: grantToken
		}).done(function (res) {
			if (res && res.success) {
				showBanner('#asrs-zoho-action-feedback', 'success', res.data.message);
				setTimeout(function () { window.location.reload(); }, 1000);
			} else {
				showBanner('#asrs-zoho-action-feedback', 'error', (res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).always(function () {
			$btn.prop('disabled', false);
		});
	});

	// Zoho: Create Test Record
	$('#asrs-create-test-record-btn').on('click', function () {
		var $btn = $(this);
		var orig = $btn.text();
		var mod = $('#asrs-test-record-module').val() || 'Leads';

		$btn.prop('disabled', true).text(cfg.i18n.creatingRecord);
		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_create_test_record',
			nonce: cfg.nonce,
			module: mod
		}).done(function (res) {
			if (res && res.success) {
				showBanner('#asrs-zoho-action-feedback', 'success', res.data.message);
			} else {
				showBanner('#asrs-zoho-action-feedback', 'error', (res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).always(function () {
			$btn.prop('disabled', false).text(orig);
		});
	});

	// Form Mapping: Sync discovered field dropdown -> input
	$(document).on('change', '.asrs-zoho-field-picker', function () {
		var val = $(this).val();
		var targetName = $(this).data('target');
		if (val && targetName) {
			$('input[name="' + targetName + '"]').val(val);
		}
	});

	// Form Mapping: Fetch Module Fields from Zoho
	$('#asrs-refresh-module-fields-btn').on('click', function () {
		var $btn = $(this);
		var orig = $btn.text();
		var mod = $('#asrs-map-module').val() || 'Leads';

		$btn.prop('disabled', true).text(cfg.i18n.loadingFields);
		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_fetch_module_fields',
			nonce: cfg.nonce,
			module: mod,
			force: 1
		}).done(function (res) {
			if (res && res.success && res.data.fields) {
				$('.asrs-zoho-field-picker').each(function () {
					var $sel = $(this);
					var currentTarget = $sel.data('target');
					var currentVal = $('input[name="' + currentTarget + '"]').val();
					$sel.empty().append('<option value="">— Select discovered Zoho field —</option>');
					res.data.fields.forEach(function (f) {
						var lkp = (f.data_type === 'lookup' && f.lookup_module) ? ' [Lookup -> ' + f.lookup_module + ']' : '';
						var label = f.field_label + ' (' + f.api_name + ')' + lkp + (f.system_mandatory ? ' *' : '');
						var $opt = $('<option>').val(f.api_name).text(label);
						if (currentVal === f.api_name) $opt.prop('selected', true);
						$sel.append($opt);
					});
				});
				$('#asrs-mapping-feedback').css('color', '#007017').text('Loaded ' + res.data.fields.length + ' fields for ' + mod);
			} else {
				$('#asrs-mapping-feedback').css('color', '#b32d2e').text((res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).always(function () {
			$btn.prop('disabled', false).text(orig);
		});
	});

	// Form Mapping: Add/Remove Static Rows
	$('#asrs-add-static-row').on('click', function () {
		$('#asrs-static-values-container').append(
			'<div class="asrs-static-row">' +
			'<input type="text" class="asrs-static-key" placeholder="Zoho API Name" />' +
			'<input type="text" class="asrs-static-val" placeholder="Value" />' +
			'<button type="button" class="button button-small asrs-remove-static-row">&times;</button>' +
			'</div>'
		);
	});

	$(document).on('click', '.asrs-remove-static-row', function () {
		$(this).closest('.asrs-static-row').remove();
	});

	// Form Mapping: Save via AJAX
	$('#asrs-form-mapping-editor').on('submit', function (e) {
		e.preventDefault();
		var $form = $(this);
		var formId = $form.data('form-id');
		var fieldMap = {};

		$form.find('.asrs-zoho-api-input').each(function () {
			var name = $(this).attr('name');
			var m = name && name.match(/^field_map\[(.+)\]$/);
			if (m && $(this).val().trim() !== '') {
				fieldMap[m[1]] = $(this).val().trim();
			}
		});

		var staticValues = {};
		$('#asrs-static-values-container .asrs-static-row').each(function () {
			var k = $(this).find('.asrs-static-key').val().trim();
			var v = $(this).find('.asrs-static-val').val().trim();
			if (k && v) {
				staticValues[k] = v;
			}
		});

		var $btn = $('#asrs-save-mapping-btn').prop('disabled', true);
		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_save_form_mapping',
			nonce: cfg.nonce,
			form_id: formId,
			zoho_enabled: $('#asrs-map-enabled').is(':checked') ? 1 : 0,
			zoho_module: $('#asrs-map-module').val(),
			trigger_workflow: $('#asrs-map-workflow').is(':checked') ? 1 : 0,
			field_map: fieldMap,
			static_values: staticValues
		}).done(function (res) {
			if (res && res.success) {
				$('#asrs-mapping-feedback').css('color', '#007017').text(res.data.message);
			} else {
				$('#asrs-mapping-feedback').css('color', '#b32d2e').text((res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).always(function () {
			$btn.prop('disabled', false);
		});
	});

	// Entries: Retry single submission
	$(document).on('click', '.asrs-retry-one-btn', function () {
		var $btn = $(this);
		var id = $btn.data('id');
		var orig = $btn.text();
		$btn.prop('disabled', true).text(cfg.i18n.retrying);

		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_retry_submission',
			nonce: cfg.nonce,
			submission_id: id
		}).done(function (res) {
			if (res && res.success) {
				window.alert(res.data.message);
				window.location.reload();
			} else {
				window.alert((res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).always(function () {
			$btn.prop('disabled', false).text(orig);
		});
	});

	// Entries: Retry all failed syncs
	$('#asrs-run-retry-queue-btn').on('click', function () {
		var $btn = $(this);
		var orig = $btn.text();
		$btn.prop('disabled', true).text(cfg.i18n.retrying);

		$.post(cfg.ajaxUrl, {
			action: 'asrs_admin_run_retry_queue',
			nonce: cfg.nonce
		}).done(function (res) {
			if (res && res.success) {
				showBanner('#asrs-retry-queue-feedback', 'success', res.data.message);
				setTimeout(function () { window.location.reload(); }, 1000);
			} else {
				showBanner('#asrs-retry-queue-feedback', 'error', (res && res.data && res.data.message) || cfg.i18n.errorGeneric);
			}
		}).always(function () {
			$btn.prop('disabled', false).text(orig);
		});
	});
})(jQuery);
