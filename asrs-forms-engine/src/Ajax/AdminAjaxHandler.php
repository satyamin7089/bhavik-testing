<?php
/**
 * Admin AJAX handlers for Zoho CRM connection, module/field discovery, mapping, and retries.
 *
 * @package ASRS\FormsEngine\Ajax
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Ajax;

use ASRS\FormsEngine\Repositories\FormMappingRepository;
use ASRS\FormsEngine\Security\SecurityManager;
use ASRS\FormsEngine\Services\RetryService;
use ASRS\FormsEngine\Services\Zoho\ZohoAuthService;
use ASRS\FormsEngine\Services\Zoho\ZohoCrmService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminAjaxHandler {

	private SecurityManager $security;
	private ZohoCrmService $zoho_crm;
	private ZohoAuthService $zoho_auth;
	private FormMappingRepository $mappings;
	private RetryService $retry;

	public function __construct(
		SecurityManager $security,
		ZohoCrmService $zoho_crm,
		ZohoAuthService $zoho_auth,
		FormMappingRepository $mappings,
		RetryService $retry
	) {
		$this->security  = $security;
		$this->zoho_crm  = $zoho_crm;
		$this->zoho_auth = $zoho_auth;
		$this->mappings  = $mappings;
		$this->retry     = $retry;
	}

	public function register(): void {
		$actions = array(
			'asrs_admin_test_zoho_connection' => 'test_zoho_connection',
			'asrs_admin_sync_zoho_modules'    => 'sync_zoho_modules',
			'asrs_admin_fetch_module_fields'  => 'fetch_module_fields',
			'asrs_admin_create_test_record'   => 'create_test_record',
			'asrs_admin_exchange_grant_token' => 'exchange_grant_token',
			'asrs_admin_save_form_mapping'    => 'save_form_mapping',
			'asrs_admin_retry_submission'     => 'retry_submission',
			'asrs_admin_run_retry_queue'      => 'run_retry_queue',
		);

		foreach ( $actions as $action => $method ) {
			add_action( 'wp_ajax_' . $action, array( $this, $method ) );
		}
	}

	public function test_zoho_connection(): void {
		$this->security->verify_admin_ajax();

		$result = $this->zoho_crm->test_connection();
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'message' => sprintf(
					/* translators: %s: Zoho Org Company Name */
					__( 'Connected successfully to Zoho CRM (%s).', 'asrs-forms-engine' ),
					$result['company_name'] ?: $result['id']
				),
				'org'     => $result,
			)
		);
	}

	public function sync_zoho_modules(): void {
		$this->security->verify_admin_ajax();

		$modules = $this->zoho_crm->discover_modules();
		if ( is_wp_error( $modules ) ) {
			wp_send_json_error( array( 'message' => $modules->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'message' => sprintf(
					/* translators: %d: module count */
					__( 'Discovered %d API-supported Zoho CRM modules.', 'asrs-forms-engine' ),
					count( $modules )
				),
				'modules' => $modules,
			)
		);
	}

	public function fetch_module_fields(): void {
		$this->security->verify_admin_ajax();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$module = isset( $_POST['module'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['module'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$force = ! empty( $_POST['force'] );

		$fields = $this->zoho_crm->get_module_fields( $module, $force );
		if ( is_wp_error( $fields ) ) {
			wp_send_json_error( array( 'message' => $fields->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'module' => $module,
				'fields' => $fields,
			)
		);
	}

	public function create_test_record(): void {
		$this->security->verify_admin_ajax();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$module = isset( $_POST['module'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['module'] ) ) : 'Leads';
		$result = $this->zoho_crm->create_test_record( $module );

		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'message'    => sprintf(
					/* translators: 1: Zoho module, 2: Zoho record ID */
					__( 'Test record created in %1$s (Record ID: %2$s).', 'asrs-forms-engine' ),
					$module,
					$result['record_id']
				),
				'record_id'  => $result['record_id'],
				'record_url' => $result['record_url'],
			)
		);
	}

	public function exchange_grant_token(): void {
		$this->security->verify_admin_ajax();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$grant_token = isset( $_POST['grant_token'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['grant_token'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$redirect_uri = isset( $_POST['redirect_uri'] ) ? esc_url_raw( wp_unslash( (string) $_POST['redirect_uri'] ) ) : '';

		if ( '' === $grant_token ) {
			wp_send_json_error( array( 'message' => __( 'Grant token is required.', 'asrs-forms-engine' ) ) );
		}

		$res = $this->zoho_auth->exchange_grant_token( $grant_token, $redirect_uri );
		if ( is_wp_error( $res ) ) {
			wp_send_json_error( array( 'message' => $res->get_error_message() ) );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Grant token exchanged and Refresh Token stored securely!', 'asrs-forms-engine' ),
			)
		);
	}

	public function save_form_mapping(): void {
		$this->security->verify_admin_ajax();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$form_id = isset( $_POST['form_id'] ) ? sanitize_key( wp_unslash( (string) $_POST['form_id'] ) ) : '';
		if ( '' === $form_id ) {
			wp_send_json_error( array( 'message' => __( 'Form ID is required.', 'asrs-forms-engine' ) ) );
		}

		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$field_map = isset( $_POST['field_map'] ) && is_array( $_POST['field_map'] ) ? wp_unslash( $_POST['field_map'] ) : array();
		// phpcs:ignore WordPress.Security.NonceVerification.Missing, WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
		$static_values = isset( $_POST['static_values'] ) && is_array( $_POST['static_values'] ) ? wp_unslash( $_POST['static_values'] ) : array();

		$saved = $this->mappings->save(
			$form_id,
			array(
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				'enabled'          => ! empty( $_POST['zoho_enabled'] ),
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				'module'           => isset( $_POST['zoho_module'] ) ? sanitize_text_field( wp_unslash( (string) $_POST['zoho_module'] ) ) : 'Leads',
				'field_map'        => $field_map,
				'static_values'    => $static_values,
				// phpcs:ignore WordPress.Security.NonceVerification.Missing
				'trigger_workflow' => ! empty( $_POST['trigger_workflow'] ),
			)
		);

		if ( ! $saved ) {
			wp_send_json_error( array( 'message' => __( 'Failed to save Zoho field mapping.', 'asrs-forms-engine' ) ) );
		}

		wp_send_json_success(
			array(
				'message' => __( 'Zoho CRM field mapping saved successfully.', 'asrs-forms-engine' ),
			)
		);
	}

	public function retry_submission(): void {
		$this->security->verify_admin_ajax();

		// phpcs:ignore WordPress.Security.NonceVerification.Missing
		$submission_id = isset( $_POST['submission_id'] ) ? (int) $_POST['submission_id'] : 0;
		if ( $submission_id <= 0 ) {
			wp_send_json_error( array( 'message' => __( 'Invalid submission ID.', 'asrs-forms-engine' ) ) );
		}

		$result = $this->retry->retry_one( $submission_id );
		if ( is_wp_error( $result ) ) {
			wp_send_json_error( array( 'message' => $result->get_error_message() ) );
		}

		if ( ! empty( $result['skipped'] ) ) {
			wp_send_json_error( array( 'message' => (string) $result['message'] ) );
		}

		wp_send_json_success(
			array(
				'message'    => sprintf(
					/* translators: %s: Zoho record ID */
					__( 'Submission synced to Zoho CRM (Record ID: %s).', 'asrs-forms-engine' ),
					(string) ( $result['record_id'] ?? '' )
				),
				'record_id'  => (string) ( $result['record_id'] ?? '' ),
				'record_url' => (string) ( $result['record_url'] ?? '' ),
			)
		);
	}

	public function run_retry_queue(): void {
		$this->security->verify_admin_ajax();

		$summary = $this->retry->process_queue( 50, true );

		wp_send_json_success(
			array(
				'message' => sprintf(
					/* translators: 1: processed, 2: succeeded, 3: failed */
					__( 'Retry run complete. Processed: %1$d | Succeeded: %2$d | Failed: %3$d', 'asrs-forms-engine' ),
					$summary['processed'],
					$summary['succeeded'],
					$summary['failed']
				),
				'summary' => $summary,
			)
		);
	}
}
