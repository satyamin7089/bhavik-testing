<?php
/**
 * Zoho CRM integration adapter implementing IntegrationInterface.
 *
 * @package ASRS\FormsEngine\Services\Integrations
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services\Integrations;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Forms\FormInterface;
use ASRS\FormsEngine\Repositories\FormMappingRepository;
use ASRS\FormsEngine\Services\Zoho\ZohoCrmService;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoCrmIntegration implements IntegrationInterface {

	private Settings $settings;
	private ZohoCrmService $crm;
	private FormMappingRepository $mappings;

	public function __construct(
		Settings $settings,
		ZohoCrmService $crm,
		FormMappingRepository $mappings
	) {
		$this->settings = $settings;
		$this->crm      = $crm;
		$this->mappings = $mappings;
	}

	public function get_slug(): string {
		return 'zoho_crm';
	}

	public function get_name(): string {
		return __( 'Zoho CRM (API v7)', 'asrs-forms-engine' );
	}

	public function is_enabled_for_form( FormInterface $form ): bool {
		$zoho_settings = $this->settings->get_zoho_settings();
		if ( empty( $zoho_settings['enabled'] ) ) {
			return false;
		}

		$mapping = $this->mappings->get_effective_mapping( $form );
		return ! empty( $mapping['enabled'] );
	}

	/**
	 * Sync a submission to Zoho CRM.
	 *
	 * @param int                  $submission_id Local submission ID.
	 * @param FormInterface        $form          Form definition.
	 * @param array<string, mixed> $data          Submission fields.
	 * @return array<string, mixed>|WP_Error
	 */
	public function sync( int $submission_id, FormInterface $form, array $data ) {
		$zoho_settings = $this->settings->get_zoho_settings();
		$mapping       = $this->mappings->get_effective_mapping( $form );
		$module        = (string) ( $mapping['module'] ?: $zoho_settings['default_module'] ?: 'Leads' );
		$payload = $this->crm->build_mapped_payload( $form, $data, $mapping, $submission_id );
		if ( is_wp_error( $payload ) ) {
			return $payload;
		}

		$trigger = isset( $mapping['trigger_workflow'] ) ? (bool) $mapping['trigger_workflow'] : (bool) $zoho_settings['trigger_workflow'];

		$result = $this->crm->insert_record(
			$module,
			$payload,
			$trigger,
			$submission_id,
			'sync_submission'
		);

		if ( is_wp_error( $result ) ) {
			return $result;
		}

		return array(
			'module'     => $module,
			'record_id'  => $result['record_id'],
			'record_url' => $result['record_url'],
			'response'   => $result['response'],
		);
	}
}
