<?php
/**
 * Admin AJAX Handlers: Test Connection, Grant Token Exchange, Sync Jobs, Retry Application, Delete Application.
 *
 * @package ASRS\RecruitEngine\Ajax
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Ajax;

use ASRS\RecruitEngine\Repositories\ApplicationRepository;
use ASRS\RecruitEngine\Zoho\RecruitAuthService;
use ASRS\RecruitEngine\Zoho\RecruitService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminAjaxHandler {

	private RecruitAuthService $auth;
	private RecruitService $recruit;
	private ApplicationRepository $applications;

	public function __construct(
		RecruitAuthService $auth,
		RecruitService $recruit,
		ApplicationRepository $applications
	) {
		$this->auth         = $auth;
		$this->recruit      = $recruit;
		$this->applications = $applications;
	}

	public function register(): void {
		add_action( 'wp_ajax_asrs_recruit_test_connection', array( $this, 'test_connection' ) );
		add_action( 'wp_ajax_asrs_recruit_exchange_grant', array( $this, 'exchange_grant' ) );
		add_action( 'wp_ajax_asrs_recruit_sync_jobs', array( $this, 'sync_jobs' ) );
		add_action( 'wp_ajax_asrs_recruit_retry_application', array( $this, 'retry_application' ) );
		add_action( 'wp_ajax_asrs_recruit_delete_application', array( $this, 'delete_application' ) );
	}

	private function authorize(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_send_json_error( array( 'message' => __( 'Unauthorized.', 'asrs-recruit-engine' ) ), 403 );
		}
		check_ajax_referer( 'asrs_recruit_admin_nonce', 'nonce' );
	}

	public function test_connection(): void {
		$this->authorize();
		$res = $this->recruit->test_connection();
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ) );
		}
		wp_send_json_success(
			array(
				'message' => sprintf(
					__( 'Connected to Zoho Recruit API v2 (%d modules accessible).', 'asrs-recruit-engine' ),
					(int) $res['modules_count']
				),
			)
		);
	}

	public function exchange_grant(): void {
		$this->authorize();
		$grant = sanitize_text_field( wp_unslash( (string) ( $_POST['grant_token'] ?? '' ) ) ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( '' === $grant ) {
			wp_send_json_error( array( 'message' => __( 'Please paste a Zoho Self Client Grant Token.', 'asrs-recruit-engine' ) ) );
		}

		$res = $this->auth->exchange_grant_token( $grant );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ) );
		}

		$test = $this->recruit->test_connection();
		if ( is_wp_error( $test ) ) {
			wp_send_json_success(
				array(
					'message' => __( 'Refresh token saved, but connection check returned: ', 'asrs-recruit-engine' ) . $test->get_error_message(),
				)
			);
		}

		wp_send_json_success(
			array(
				'message' => __( 'Zoho Recruit connected! Refresh token encrypted & saved, and API test passed.', 'asrs-recruit-engine' ),
			)
		);
	}

	public function sync_jobs(): void {
		$this->authorize();
		$jobs = $this->recruit->sync_job_openings();
		if ( is_wp_error( $jobs ) ) {
			wp_send_json_error( array( 'message' => $jobs->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'count'   => count( $jobs ),
				'message' => sprintf( __( 'Synced %d job opening(s) from Zoho Recruit.', 'asrs-recruit-engine' ), count( $jobs ) ),
			)
		);
	}

	public function retry_application(): void {
		$this->authorize();
		$app_id = (int) ( $_POST['application_id'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $app_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid application ID.', 'asrs-recruit-engine' ) ) );
		}

		$res = $this->recruit->sync_application( $app_id );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'message'       => __( 'Application synced to Zoho Recruit.', 'asrs-recruit-engine' ),
				'candidate_id'  => $res['candidate_id'] ?? '',
				'candidate_url' => $res['candidate_url'] ?? '',
			)
		);
	}

	public function delete_application(): void {
		$this->authorize();
		$app_id = (int) ( $_POST['application_id'] ?? 0 ); // phpcs:ignore WordPress.Security.NonceVerification.Missing
		if ( $app_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid application ID.', 'asrs-recruit-engine' ) ) );
		}

		$this->applications->delete( $app_id );
		wp_send_json_success( array( 'message' => __( 'Application deleted.', 'asrs-recruit-engine' ) ) );
	}
}
