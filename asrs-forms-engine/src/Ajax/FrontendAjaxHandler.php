<?php
/**
 * Public/Frontend AJAX handler for form submissions.
 *
 * @package ASRS\FormsEngine\Ajax
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Ajax;

use ASRS\FormsEngine\Services\SubmissionService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FrontendAjaxHandler {

	private SubmissionService $submissions;

	public function __construct( SubmissionService $submissions ) {
		$this->submissions = $submissions;
	}

	public function register(): void {
		add_action( 'wp_ajax_asrs_submit_form', array( $this, 'handle_submit' ) );
		add_action( 'wp_ajax_nopriv_asrs_submit_form', array( $this, 'handle_submit' ) );
	}

	public function handle_submit(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Missing -- Verified inside SecurityManager.
		$result = $this->submissions->handle( $_POST );

		if ( is_wp_error( $result ) ) {
			$data   = $result->get_error_data();
			$status = 'asrs_validation_failed' === $result->get_error_code() ? 422 : 400;

			wp_send_json_error(
				array(
					'code'         => $result->get_error_code(),
					'message'      => $result->get_error_message(),
					'field_errors' => is_array( $data ) && isset( $data['field_errors'] ) ? $data['field_errors'] : array(),
				),
				$status
			);
		}

		wp_send_json_success( $result );
	}
}
