<?php
/**
 * Sync Failure Alert Notification Service (Candidate & HR notifications are handled by Zoho Recruit).
 *
 * @package ASRS\RecruitEngine\Services
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Services;

use ASRS\RecruitEngine\Core\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class NotificationService {

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @param array<string, mixed> $application
	 */
	public function notify_sync_failure( array $application, string $error ): void {
		if ( ! (bool) $this->settings->get( 'notify_on_sync_failure', true ) ) {
			return;
		}

		$to = (string) $this->settings->get( 'sync_failure_email', get_option( 'admin_email' ) );
		if ( ! is_email( $to ) ) {
			return;
		}

		$subject = sprintf(
			'[%s] Job Application Sync Failed (#%d)',
			wp_specialchars_decode( get_bloginfo( 'name' ), ENT_QUOTES ),
			(int) $application['id']
		);

		$body = sprintf(
			"A job application was saved locally in WordPress, but syncing to Zoho Recruit failed.\n\n" .
			"Application ID: #%d\n" .
			"Position: %s\n" .
			"Candidate: %s %s (%s)\n" .
			"Error: %s\n\n" .
			"Automatic retry is scheduled, or you can retry manually in WordPress -> ASRS Careers -> Applications.",
			(int) $application['id'],
			(string) $application['job_title'],
			(string) $application['first_name'],
			(string) $application['last_name'],
			(string) $application['email'],
			$error
		);

		wp_mail( $to, $subject, $body );
	}
}
