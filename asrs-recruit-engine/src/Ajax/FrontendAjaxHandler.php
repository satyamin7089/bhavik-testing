<?php
/**
 * Frontend AJAX Handler for Job Application Submissions (multipart/form-data).
 *
 * @package ASRS\RecruitEngine\Ajax
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Ajax;

use ASRS\RecruitEngine\Services\ApplicationService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FrontendAjaxHandler {

	private ApplicationService $service;

	public function __construct( ApplicationService $service ) {
		$this->service = $service;
	}

	public function register(): void {
		add_action( 'wp_ajax_asrs_submit_application', array( $this, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_asrs_submit_application', array( $this, 'handle_submit' ) );
	}

	public function handle_submit(): void {
		$post  = wp_unslash( $_POST );  // phpcs:ignore WordPress.Security.NonceVerification.Missing
		$files = $_FILES;               // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$result = $this->service->process( is_array( $post ) ? $post : array(), is_array( $files ) ? $files : array() );

		if ( is_wp_error( $result ) ) {
			$data = $result->get_error_data();
			wp_send_json_error(
				array(
					'message' => $result->get_error_message(),
					'fields'  => is_array( $data ) && isset( $data['fields'] ) ? $data['fields'] : array(),
				),
				400
			);
		}

		wp_send_json_success( $result );
	}
}
