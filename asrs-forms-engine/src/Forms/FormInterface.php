<?php
/**
 * Contract for all developer-defined PHP forms.
 *
 * @package ASRS\FormsEngine\Forms
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

interface FormInterface {

	public function get_id(): string;

	public function get_title(): string;

	public function get_description(): string;

	/**
	 * @return Field[]
	 */
	public function get_fields(): array;

	/**
	 * @return array<string, mixed>
	 */
	public function get_settings(): array;

	/**
	 * @return array<string, mixed>
	 */
	public function get_default_zoho_mapping(): array;

	/**
	 * @param array<string, mixed> $data Sanitized field values.
	 * @return array<string, string>
	 */
	public function validate( array $data ): array;

	/**
	 * @param int                  $submission_id Local DB submission ID.
	 * @param array<string, mixed> $data          Sanitized field values.
	 */
	public function after_submit( int $submission_id, array $data ): void;
}
