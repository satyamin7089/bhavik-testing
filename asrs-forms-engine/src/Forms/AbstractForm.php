<?php
/**
 * Base class for developer-authored PHP forms.
 *
 * @package ASRS\FormsEngine\Forms
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class AbstractForm implements FormInterface {

	/**
	 * @var array<string, Field>|null
	 */
	private ?array $field_map = null;

	public function get_description(): string {
		return '';
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_settings(): array {
		return array(
			'submit_label'     => __( 'Submit', 'asrs-forms-engine' ),
			'submitting_label' => __( 'Submitting...', 'asrs-forms-engine' ),
			'success_message'  => __( 'Thank you! Your submission has been received.', 'asrs-forms-engine' ),
			'css_class'        => '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_default_zoho_mapping(): array {
		return array(
			'enabled'          => false,
			'module'           => 'Leads',
			'field_map'        => array(),
			'static_values'    => array(),
			'trigger_workflow' => true,
		);
	}

	public function validate( array $data ): array {
		return array();
	}

	public function after_submit( int $submission_id, array $data ): void {
		// Override for custom logic if needed.
	}

	/**
	 * @return array<string, Field>
	 */
	public function get_field_map(): array {
		if ( null === $this->field_map ) {
			$this->field_map = array();
			foreach ( $this->get_fields() as $field ) {
				if ( $field instanceof Field && '' !== $field->get_name() ) {
					$this->field_map[ $field->get_name() ] = $field;
				}
			}
		}

		return $this->field_map;
	}
}
