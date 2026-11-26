<?php
/**
 * WP-Cron scheduler for auto-retrying failed Zoho Recruit syncs and refreshing Job Openings.
 *
 * @package ASRS\RecruitEngine\Cron
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Cron;

use ASRS\RecruitEngine\Core\Settings;
use ASRS\RecruitEngine\Repositories\ApplicationRepository;
use ASRS\RecruitEngine\Zoho\RecruitAuthService;
use ASRS\RecruitEngine\Zoho\RecruitService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CronManager {

	public const HOOK_RETRY     = 'asrs_recruit_cron_retry_syncs';
	public const HOOK_SYNC_JOBS = 'asrs_recruit_cron_sync_jobs';

	private ApplicationRepository $applications;
	private RecruitService $recruit;
	private RecruitAuthService $auth;
	private Settings $settings;

	public function __construct(
		ApplicationRepository $applications,
		RecruitService $recruit,
		RecruitAuthService $auth,
		Settings $settings
	) {
		$this->applications = $applications;
		$this->recruit      = $recruit;
		$this->auth         = $auth;
		$this->settings     = $settings;
	}

	public function register(): void {
		add_filter( 'cron_schedules', array( $this, 'add_intervals' ) );
		add_action( self::HOOK_RETRY, array( $this, 'run_retry_queue' ) );
		add_action( self::HOOK_SYNC_JOBS, array( $this, 'run_job_sync' ) );
	}

	/**
	 * @param array<string, array<string, mixed>> $schedules
	 * @return array<string, array<string, mixed>>
	 */
	public function add_intervals( array $schedules ): array {
		if ( ! isset( $schedules['asrs_recruit_5min'] ) ) {
			$schedules['asrs_recruit_5min'] = array(
				'interval' => 5 * MINUTE_IN_SECONDS,
				'display'  => __( 'Every 5 Minutes (ASRS Recruit)', 'asrs-recruit-engine' ),
			);
		}
		return $schedules;
	}

	public static function schedule_events(): void {
		if ( ! wp_next_scheduled( self::HOOK_RETRY ) ) {
			wp_schedule_event( time() + 60, 'asrs_recruit_5min', self::HOOK_RETRY );
		}
		if ( ! wp_next_scheduled( self::HOOK_SYNC_JOBS ) ) {
			wp_schedule_event( time() + 120, 'hourly', self::HOOK_SYNC_JOBS );
		}
	}

	public static function clear_events(): void {
		wp_clear_scheduled_hook( self::HOOK_RETRY );
		wp_clear_scheduled_hook( self::HOOK_SYNC_JOBS );
	}

	public function run_retry_queue(): void {
		$cfg = $this->settings->get_zoho_settings();
		if ( empty( $cfg['enabled'] ) || ! $this->auth->has_credentials() ) {
			return;
		}

		$due = $this->applications->get_due_for_retry( 10 );
		foreach ( $due as $app ) {
			$this->recruit->sync_application( (int) $app['id'] );
		}
	}

	public function run_job_sync(): void {
		$cfg = $this->settings->get_zoho_settings();
		if ( empty( $cfg['enabled'] ) || ! $this->auth->has_credentials() ) {
			return;
		}

		$this->recruit->sync_job_openings();
	}
}
