<?php
/**
 * Synchronization orchestrator for Zoho CRM and extensible integrations.
 *
 * @package ASRS\FormsEngine\Services
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Forms\FormInterface;
use ASRS\FormsEngine\Forms\FormRegistry;
use ASRS\FormsEngine\Repositories\FormMappingRepository;
use ASRS\FormsEngine\Repositories\SubmissionRepository;
use ASRS\FormsEngine\Services\Integrations\IntegrationInterface;
use ASRS\FormsEngine\Services\Integrations\ZohoCrmIntegration;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SyncService {

	private SubmissionRepository $submissions;
	private FormRegistry $registry;
	private FormMappingRepository $mappings;
	private ZohoCrmIntegration $zoho;
	private Settings $settings;
	private NotificationService $notifications;

	/**
	 * Additional third-party integrations registered via filter/method.
	 *
	 * @var IntegrationInterface[]
	 */
	private array $extra_integrations = array();

	public function __construct(
		SubmissionRepository $submissions,
		FormRegistry $registry,
		FormMappingRepository $mappings,
		ZohoCrmIntegration $zoho,
		Settings $settings,
		NotificationService $notifications
	) {
		$this->submissions   = $submissions;
		$this->registry      = $registry;
		$this->mappings      = $mappings;
		$this->zoho          = $zoho;
		$this->settings      = $settings;
		$this->notifications = $notifications;
	}

	/**
	 * Register a custom downstream integration.
	 *
	 * @param IntegrationInterface $integration Integration implementation.
	 */
	public function add_integration( IntegrationInterface $integration ): void {
		$this->extra_integrations[ $integration->get_slug() ] = $integration;
	}

	/**
	 * Synchronize a local submission to Zoho CRM and any additional integrations.
	 *
	 * @param int  $submission_id Local submission ID.
	 * @param bool $is_retry      Whether triggered by manual/cron retry.
	 * @return array<string, mixed>|WP_Error
	 */
	public function sync_submission( int $submission_id, bool $is_retry = false ) {
		$submission = $this->submissions->find( $submission_id );
		if ( ! $submission ) {
			return new WP_Error( 'asrs_submission_not_found', __( 'Submission not found.', 'asrs-forms-engine' ) );
		}

		$form = $this->registry->get( (string) $submission['form_id'] );
		if ( ! $form instanceof FormInterface ) {
			return new WP_Error( 'asrs_form_not_registered', __( 'Registered PHP form definition not found.', 'asrs-forms-engine' ) );
		}

		$data = is_array( $submission['data'] ) ? $submission['data'] : array();

		// Fire extensible third-party integrations.
		$integrations = apply_filters( 'asrs_forms_integrations', $this->extra_integrations, $form, $submission );
		if ( is_array( $integrations ) ) {
			foreach ( $integrations as $integration ) {
				if ( $integration instanceof IntegrationInterface && $integration->is_enabled_for_form( $form ) ) {
					$integration->sync( $submission_id, $form, $data );
				}
			}
		}

		if ( ! $this->zoho->is_enabled_for_form( $form ) ) {
			return array(
				'skipped' => true,
				'status'  => (string) $submission['sync_status'],
				'message' => __( 'Zoho CRM sync is disabled globally or for this form.', 'asrs-forms-engine' ),
			);
		}

		$mapping = $this->mappings->get_effective_mapping( $form );
		$module  = (string) ( $mapping['module'] ?? 'Leads' );

		$result = $this->zoho->sync( $submission_id, $form, $data );

		if ( is_wp_error( $result ) ) {
			$next_retry = $this->compute_next_retry_at( (int) $submission['sync_attempts'] + 1 );
			$raw_body   = $result->get_error_data();
			$this->submissions->mark_failed(
				$submission_id,
				$module,
				$result->get_error_message(),
				is_array( $raw_body ) ? $raw_body : array(),
				$next_retry
			);

			if ( ! $is_retry ) {
				$this->notifications->notify_sync_failure( $submission_id, $form, $result->get_error_message() );
			}

			do_action( 'asrs_forms_sync_failed', $submission_id, $form, $result );

			return $result;
		}

		$this->submissions->mark_synced(
			$submission_id,
			(string) $result['module'],
			(string) $result['record_id'],
			(string) $result['record_url'],
			is_array( $result['response'] ) ? $result['response'] : array()
		);

		do_action( 'asrs_forms_sync_succeeded', $submission_id, $form, $result );

		return $result;
	}

	/**
	 * Calculate exponential backoff timestamp for next automatic retry.
	 *
	 * @param int $attempt Current attempt count (1-based).
	 */
	private function compute_next_retry_at( int $attempt ): ?string {
		$max_attempts = (int) $this->settings->get( 'max_retry_attempts', 5 );
		if ( $attempt >= $max_attempts ) {
			return null;
		}

		$base_minutes = max( 1, (int) $this->settings->get( 'retry_base_delay_min', 5 ) );
		// Exponential backoff capped at 720 minutes (12 hours).
		$delay_minutes = min( 720, (int) ( $base_minutes * ( 2 ** max( 0, $attempt - 1 ) ) ) );

		return gmdate( 'Y-m-d H:i:s', time() + ( $delay_minutes * MINUTE_IN_SECONDS ) );
	}
}
