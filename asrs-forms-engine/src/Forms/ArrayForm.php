<?php
/**
 * Adapter turning simple PHP config arrays in /forms/*.php into FormInterface objects.
 *
 * @package ASRS\FormsEngine\Forms
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ArrayForm extends AbstractForm {

	/**
	 * @var array<string, mixed>
	 */
	private array $config;

	/**
	 * @var Field[]|null
	 */
	private ?array $fields = null;

	/**
	 * @param array<string, mixed> $config Form definition array.
	 */
	public function __construct( array $config ) {
		$this->config = $config;
	}

	public function get_id(): string {
		return sanitize_key( (string) ( $this->config['id'] ?? '' ) );
	}

	public function get_title(): string {
		return (string) ( $this->config['title'] ?? ucwords( str_replace( '-', ' ', $this->get_id() ) ) );
	}

	public function get_description(): string {
		return (string) ( $this->config['description'] ?? '' );
	}

	/**
	 * @return Field[]
	 */
	public function get_fields(): array {
		if ( null !== $this->fields ) {
			return $this->fields;
		}

		$this->fields = array();
		$raw_fields   = is_array( $this->config['fields'] ?? null ) ? $this->config['fields'] : array();

		foreach ( $raw_fields as $key => $field_cfg ) {
			if ( $field_cfg instanceof Field ) {
				$this->fields[] = $field_cfg;
				continue;
			}
			if ( ! is_array( $field_cfg ) ) {
				continue;
			}
			if ( empty( $field_cfg['name'] ) && is_string( $key ) ) {
				$field_cfg['name'] = $key;
			}
			$this->fields[] = new Field( $field_cfg );
		}

		return $this->fields;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_settings(): array {
		$base = parent::get_settings();
		$over = is_array( $this->config['settings'] ?? null ) ? $this->config['settings'] : array();

		return array_merge( $base, $over );
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_default_zoho_mapping(): array {
		$base = parent::get_default_zoho_mapping();
		$over = is_array( $this->config['zoho'] ?? null ) ? $this->config['zoho'] : array();

		return array_merge( $base, $over );
	}

	public function validate( array $data ): array {
		if ( isset( $this->config['validate'] ) && is_callable( $this->config['validate'] ) ) {
			$res = ( $this->config['validate'] )( $data );
			return is_array( $res ) ? $res : array();
		}
		return array();
	}

	public function after_submit( int $submission_id, array $data ): void {
		if ( isset( $this->config['after_submit'] ) && is_callable( $this->config['after_submit'] ) ) {
			( $this->config['after_submit'] )( $submission_id, $data );
		}
	}
}
