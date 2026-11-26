<?php
/**
 * WP-Cron scheduler for automatic retry of failed Zoho CRM syncs.
 *
 * @package ASRS\FormsEngine\Cron
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Cron;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Services\RetryService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CronManager {

	public const HOOK_RETRY_SYNCS = 'asrs_forms_cron_retry_syncs';

	private RetryService $retry;
	private Settings $settings;

	public function __construct( RetryService $retry, Settings $settings ) {
		$this->retry    = $retry;
		$this->settings = $settings;
	}

	public function init(): void {
		add_filter( 'cron_schedules', array( $this, 'register_intervals' ) );
		add_action( self::HOOK_RETRY_SYNCS, array( $this, 'run_retry_syncs' ) );

		self::schedule_events();
	}

	/**
	 * @param array<string, array<string, mixed>> $schedules Existing schedules.
	 * @return array<string, array<string, mixed>>
	 */
	public function register_intervals( array $schedules ): array {
		if ( ! isset( $schedules['asrs_five_minutes'] ) ) {
			$schedules['asrs_five_minutes'] = array(
				'interval' => 5 * MINUTE_IN_SECONDS,
				'display'  => __( 'Every 5 Minutes (ASRS Forms)', 'asrs-forms-engine' ),
			);
		}
		return $schedules;
	}

	public static function schedule_events(): void {
		if ( ! wp_next_scheduled( self::HOOK_RETRY_SYNCS ) ) {
			wp_schedule_event( time() + 60, 'asrs_five_minutes', self::HOOK_RETRY_SYNCS );
		}
	}

	public static function unschedule_events(): void {
		foreach ( array( self::HOOK_RETRY_SYNCS, 'asrs_forms_cron_abandoned_drafts', 'asrs_forms_cron_cleanup' ) as $hook ) {
			$timestamp = wp_next_scheduled( $hook );
			while ( $timestamp ) {
				wp_unschedule_event( $timestamp, $hook );
				$timestamp = wp_next_scheduled( $hook );
			}
		}
	}

	public function run_retry_syncs(): void {
		if ( ! (bool) $this->settings->get( 'auto_retry_enabled', true ) ) {
			return;
		}
		$this->retry->process_queue( 25, false );
	}
}
