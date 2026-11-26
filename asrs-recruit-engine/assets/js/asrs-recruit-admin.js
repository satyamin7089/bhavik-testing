(function ($) {
	'use strict';

	var cfg = window.asrsRecruitAdmin || {};

	$('#asrs-test-conn-btn').on('click', function () {
		var $msg = $('#asrs-zoho-ajax-msg').text(cfg.i18n.testing || 'Testing...').css('color', '#1d2327');
		$.post(cfg.ajaxUrl, {
			action: 'asrs_recruit_test_connection',
			nonce: cfg.nonce
		}).done(function (res) {
			if (res && res.success) {
				$msg.text(res.data.message).css('color', '#065f46');
			} else {
				$msg.text((res && res.data && res.data.message) || 'Failed').css('color', '#b91c1c');
			}
		});
	});

	$('#asrs-exchange-grant-btn').on('click', function () {
		var grant = $('#asrs-grant-token').val().trim();
		var $msg = $('#asrs-zoho-ajax-msg').text(cfg.i18n.exchanging || 'Exchanging...').css('color', '#1d2327');
		$.post(cfg.ajaxUrl, {
			action: 'asrs_recruit_exchange_grant',
			nonce: cfg.nonce,
			grant_token: grant
		}).done(function (res) {
			if (res && res.success) {
				$msg.text(res.data.message).css('color', '#065f46');
				$('#asrs-grant-token').val('');
			} else {
				$msg.text((res && res.data && res.data.message) || 'Exchange failed').css('color', '#b91c1c');
			}
		});
	});

	$('#asrs-sync-jobs-btn').on('click', function () {
		var $btn = $(this).prop('disabled', true);
		var $notice = $('#asrs-sync-jobs-notice').hide().removeClass('notice-success notice-error');
		$.post(cfg.ajaxUrl, {
			action: 'asrs_recruit_sync_jobs',
			nonce: cfg.nonce
		}).done(function (res) {
			if (res && res.success) {
				$notice.addClass('notice-success').show().find('p').text(res.data.message);
				setTimeout(function () { window.location.reload(); }, 900);
			} else {
				$notice.addClass('notice-error').show().find('p').text((res && res.data && res.data.message) || 'Sync failed.');
			}
		}).always(function () {
			$btn.prop('disabled', false);
		});
	});

	$(document).on('click', '.asrs-retry-app', function () {
		var $btn = $(this).prop('disabled', true).text(cfg.i18n.retrying || 'Retrying...');
		var appId = $btn.data('app-id');
		$.post(cfg.ajaxUrl, {
			action: 'asrs_recruit_retry_application',
			nonce: cfg.nonce,
			application_id: appId
		}).done(function (res) {
			if (res && res.success) {
				window.location.reload();
			} else {
				alert((res && res.data && res.data.message) || 'Retry failed');
				$btn.prop('disabled', false).text('Retry Sync');
			}
		});
	});

	$(document).on('click', '.asrs-delete-app', function () {
		if (!window.confirm(cfg.i18n.confirmDel || 'Delete this application?')) {
			return;
		}
		var $btn = $(this);
		var appId = $btn.data('app-id');
		$.post(cfg.ajaxUrl, {
			action: 'asrs_recruit_delete_application',
			nonce: cfg.nonce,
			application_id: appId
		}).done(function (res) {
			if (res && res.success) {
				$('tr[data-app-row="' + appId + '"]').fadeOut(200, function () { $(this).remove(); });
			}
		});
	});
})(jQuery);
