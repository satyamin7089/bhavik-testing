<?php
/**
 * Core form submission processing engine.
 *
 * Flow: Visitor -> Security -> Validate -> Save Lead in WP -> Sync to Zoho CRM v7 (Triggers Zoho Workflows/Emails) -> Return Response.
 *
 * @package ASRS\FormsEngine\Services
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Forms\FormInterface;
use ASRS\FormsEngine\Forms\FormRegistry;
use ASRS\FormsEngine\Forms\FormValidator;
use ASRS\FormsEngine\Repositories\FormMappingRepository;
use ASRS\FormsEngine\Repositories\SubmissionRepository;
use ASRS\FormsEngine\Security\Sanitizer;
use ASRS\FormsEngine\Security\SecurityManager;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SubmissionService {

	private FormRegistry $registry;
	private FormValidator $validator;
	private SecurityManager $security;
	private SubmissionRepository $submissions;
	private SyncService $sync;
	private FormMappingRepository $mappings;
	private Settings $settings;

	public function __construct(
		FormRegistry $registry,
		FormValidator $validator,
		SecurityManager $security,
		SubmissionRepository $submissions,
		SyncService $sync,
		FormMappingRepository $mappings,
		Settings $settings
	) {
		$this->registry    = $registry;
		$this->validator   = $validator;
		$this->security    = $security;
		$this->submissions = $submissions;
		$this->sync        = $sync;
		$this->mappings    = $mappings;
		$this->settings    = $settings;
	}

	/**
	 * Execute the full submission pipeline.
	 *
	 * @param array<string, mixed> $request_post Raw $_POST payload.
	 * @return array<string, mixed>|WP_Error
	 */
	public function handle( array $request_post ) {
		$form_id = isset( $request_post['form_id'] ) ? sanitize_key( wp_unslash( (string) $request_post['form_id'] ) ) : '';
		$form    = $this->registry->get( $form_id );

		if ( ! $form instanceof FormInterface ) {
			return new WP_Error(
				'asrs_form_not_found',
				__( 'The requested form does not exist.', 'asrs-forms-engine' )
			);
		}

		// 1. Security checks (Nonce, Honeypot, Timing, Turnstile).
		$sec_check = $this->security->verify_submission_request( $form_id, $request_post );
		if ( is_wp_error( $sec_check ) ) {
			return $sec_check;
		}

		// 2. Extract and sanitize defined fields only.
		$raw_fields     = is_array( $request_post['fields'] ?? null ) ? $request_post['fields'] : $request_post;
		$sanitized_data = array();

		foreach ( $form->get_fields() as $field ) {
			$name = $field->get_name();
			$val  = $raw_fields[ $name ] ?? '';
			$val  = Sanitizer::sanitize_field_value( $val, $field );
			$val  = apply_filters( 'asrs_forms_field_value', $val, $field, $form );

			$sanitized_data[ $name ] = $val;
		}

		// 3. Validate all fields + form-level rules.
		$errors = $this->validator->validate( $form, $sanitized_data );
		if ( ! empty( $errors ) ) {
			return new WP_Error(
				'asrs_validation_failed',
				__( 'Please correct the highlighted fields and try again.', 'asrs-forms-engine' ),
				array( 'field_errors' => $errors )
			);
		}

		do_action( 'asrs_forms_before_submit', $form, $sanitized_data );

		// 4. Collect request metadata.
		$meta = array(
			'page_url'   => isset( $request_post['page_url'] ) ? esc_url_raw( wp_unslash( (string) $request_post['page_url'] ) ) : '',
			'referrer'   => isset( $_SERVER['HTTP_REFERER'] ) ? esc_url_raw( wp_unslash( (string) $_SERVER['HTTP_REFERER'] ) ) : '',
			'user_agent' => isset( $_SERVER['HTTP_USER_AGENT'] ) ? sanitize_text_field( wp_unslash( (string) $_SERVER['HTTP_USER_AGENT'] ) ) : '',
			'user_id'    => get_current_user_id() ?: null,
		);
		if ( (bool) $this->settings->get( 'store_ip_address', true ) ) {
			$meta['ip_address'] = Sanitizer::get_client_ip();
		}

		$mapping         = $this->mappings->get_effective_mapping( $form );
		$submission_uuid = wp_generate_uuid4();

		// 5. Save lead locally in WordPress first.
		$submission_id = $this->submissions->create(
			array(
				'submission_uuid' => $submission_uuid,
				'form_id'         => $form_id,
				'data'            => $sanitized_data,
				'meta'            => $meta,
				'sync_status'     => SubmissionRepository::STATUS_PENDING,
				'zoho_module'     => (string) ( $mapping['module'] ?? 'Leads' ),
			)
		);

		if ( $submission_id <= 0 ) {
			return new WP_Error(
				'asrs_db_insert_failed',
				__( 'Failed to store your submission. Please try again.', 'asrs-forms-engine' )
			);
		}

		// 6. Run form-level & WordPress post-save hooks.
		$form->after_submit( $submission_id, $sanitized_data );
		do_action( 'asrs_forms_after_submit', $submission_id, $form, $sanitized_data );

		// 7. Sync to Zoho CRM v7 (triggers Zoho Workflows, Email Alerts, Approvals & Blueprints).
		$sync_state = SubmissionRepository::STATUS_PENDING;
		$zoho_id    = '';
		$sync_res   = $this->sync->sync_submission( $submission_id, false );

		if ( is_wp_error( $sync_res ) ) {
			$sync_state = SubmissionRepository::STATUS_FAILED;
		} elseif ( ! empty( $sync_res['skipped'] ) ) {
			$sync_state = SubmissionRepository::STATUS_PENDING;
		} else {
			$sync_state = SubmissionRepository::STATUS_SYNCED;
			$zoho_id    = (string) ( $sync_res['record_id'] ?? '' );
		}

		$form_settings = $form->get_settings();

		return array(
			'submission_id'   => $submission_id,
			'submission_uuid' => $submission_uuid,
			'sync_status'     => $sync_state,
			'zoho_record_id'  => $zoho_id,
			'message'         => (string) ( $form_settings['success_message'] ?? __( 'Thank you! Your submission has been received.', 'asrs-forms-engine' ) ),
		);
	}
}
