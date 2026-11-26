<?php
/**
 * Lightweight sync failure alert service (Client & Customer emails are handled natively by Zoho CRM Workflows).
 *
 * @package ASRS\FormsEngine\Services
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Forms\FormInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NotificationService {

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Alert administrator only if Zoho CRM sync fails, so no lead is missed.
	 */
	public function notify_sync_failure( int $submission_id, FormInterface $form, string $error ): void {
		if ( ! (bool) $this->settings->get( 'admin_notify_on_sync_fail', true ) ) {
			return;
		}

		$to = sanitize_email( (string) $this->settings->get( 'notification_email', get_option( 'admin_email', '' ) ) );
		if ( '' === $to || ! is_email( $to ) ) {
			return;
		}

		$subject = sprintf(
			'[%s] Zoho CRM Sync Failed - Submission #%d (%s)',
			get_bloginfo( 'name' ),
			$submission_id,
			$form->get_title()
		);

		$body = implode(
			"\n",
			array(
				sprintf( 'A lead submission (#%d) for "%s" was saved in WordPress, but failed to sync to Zoho CRM.', $submission_id, $form->get_title() ),
				'',
				'Error: ' . $error,
				'',
				'It will be retried automatically via WP-Cron, or you can retry manually here:',
				admin_url( 'admin.php?page=asrs-forms&view=detail&id=' . $submission_id ),
			)
		);

		wp_mail( $to, $subject, $body );
	}
}
