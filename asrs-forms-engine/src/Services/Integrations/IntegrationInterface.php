<?php
/**
 * Extensible integration contract for downstream CRM/webhook/ERP connectors.
 *
 * @package ASRS\FormsEngine\Services\Integrations
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services\Integrations;

use ASRS\FormsEngine\Forms\FormInterface;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface IntegrationInterface {

	/**
	 * Unique integration slug (e.g. 'zoho_crm').
	 */
	public function get_slug(): string;

	/**
	 * Display name for admin UI.
	 */
	public function get_name(): string;

	/**
	 * Whether this integration is enabled and configured for the given form.
	 *
	 * @param FormInterface $form Form instance.
	 */
	public function is_enabled_for_form( FormInterface $form ): bool;

	/**
	 * Push a submission to the external integration.
	 *
	 * @param int                  $submission_id Local submission ID.
	 * @param FormInterface        $form          Form instance.
	 * @param array<string, mixed> $data          Sanitized submission data.
	 * @return array<string, mixed>|WP_Error
	 */
	public function sync( int $submission_id, FormInterface $form, array $data );
}
