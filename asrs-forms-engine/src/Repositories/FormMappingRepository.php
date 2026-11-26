<?php
/**
 * Repository for form-specific Zoho CRM module and field mappings.
 *
 * @package ASRS\FormsEngine\Repositories
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Repositories;

use ASRS\FormsEngine\Database\Schema;
use ASRS\FormsEngine\Forms\FormInterface;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormMappingRepository extends AbstractRepository {

	protected function table(): string {
		return Schema::table( 'form_mappings' );
	}

	/**
	 * Get saved mapping for a form, falling back to the form's PHP default mapping.
	 *
	 * @param FormInterface $form Form instance.
	 * @return array<string, mixed>
	 */
	public function get_effective_mapping( FormInterface $form ): array {
		$form_id  = $form->get_id();
		$defaults = $form->get_default_zoho_mapping();
		$saved    = $this->find_by_form_id( $form_id );

		if ( null === $saved ) {
			return wp_parse_args(
				$defaults,
				array(
					'enabled'          => false,
					'module'           => 'Leads',
					'field_map'        => array(),
					'static_values'    => array(),
					'trigger_workflow' => true,
					'is_custom_saved'  => false,
				)
			);
		}

		return array(
			'enabled'          => (bool) $saved['zoho_enabled'],
			'module'           => (string) $saved['zoho_module'],
			'field_map'        => $saved['field_map'],
			'static_values'    => $saved['static_values'],
			'trigger_workflow' => (bool) $saved['trigger_workflow'],
			'is_custom_saved'  => true,
			'updated_at'       => $saved['updated_at'],
		);
	}

	/**
	 * Fetch raw saved mapping by form ID.
	 *
	 * @param string $form_id Form slug.
	 * @return array<string, mixed>|null
	 */
	public function find_by_form_id( string $form_id ): ?array {
		$table = $this->table();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $this->db->get_row( $this->db->prepare( "SELECT * FROM `{$table}` WHERE form_id = %s", sanitize_key( $form_id ) ), ARRAY_A );

		if ( ! is_array( $row ) ) {
			return null;
		}

		$row['field_map']     = json_decode( (string) ( $row['field_map_json'] ?? '{}' ), true ) ?: array();
		$row['static_values'] = json_decode( (string) ( $row['static_values_json'] ?? '{}' ), true ) ?: array();

		return $row;
	}

	/**
	 * Save or update form-specific Zoho CRM mapping.
	 *
	 * @param string               $form_id Form slug.
	 * @param array<string, mixed> $config  Mapping payload.
	 */
	public function save( string $form_id, array $config ): bool {
		$safe_id = sanitize_key( $form_id );
		$now     = current_time( 'mysql', true );
		$table   = $this->table();

		$clean_map = array();
		if ( ! empty( $config['field_map'] ) && is_array( $config['field_map'] ) ) {
			foreach ( $config['field_map'] as $form_field => $zoho_field ) {
				$ff = sanitize_key( (string) $form_field );
				$zf = sanitize_text_field( (string) $zoho_field );
				if ( '' !== $ff && '' !== $zf ) {
					$clean_map[ $ff ] = $zf;
				}
			}
		}

		$clean_static = array();
		if ( ! empty( $config['static_values'] ) && is_array( $config['static_values'] ) ) {
			foreach ( $config['static_values'] as $zoho_field => $val ) {
				$zf = sanitize_text_field( (string) $zoho_field );
				$sv = sanitize_text_field( (string) $val );
				if ( '' !== $zf && '' !== $sv ) {
					$clean_static[ $zf ] = $sv;
				}
			}
		}

		$data = array(
			'zoho_enabled'       => ! empty( $config['enabled'] ) ? 1 : 0,
			'zoho_module'        => sanitize_text_field( (string) ( $config['module'] ?? 'Leads' ) ),
			'field_map_json'     => wp_json_encode( $clean_map ),
			'static_values_json' => wp_json_encode( $clean_static ),
			'trigger_workflow'   => ! empty( $config['trigger_workflow'] ) ? 1 : 0,
			'updated_by'         => get_current_user_id() ?: null,
			'updated_at'         => $now,
		);

		$existing = $this->find_by_form_id( $safe_id );
		if ( $existing ) {
			// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
			return false !== $this->db->update( $table, $data, array( 'form_id' => $safe_id ) );
		}

		$data['form_id']    = $safe_id;
		$data['created_at'] = $now;

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		return false !== $this->db->insert( $table, $data );
	}
}
