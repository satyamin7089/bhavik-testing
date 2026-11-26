<?php
/**
 * Input sanitization and safe output helpers.
 *
 * @package ASRS\FormsEngine\Security
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Security;

use ASRS\FormsEngine\Forms\Field;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Sanitizer {

	/**
	 * Sanitize a single field value according to its Field definition.
	 *
	 * @param mixed $raw_value Raw input value.
	 * @param Field $field     Field schema definition.
	 * @return mixed
	 */
	public static function sanitize_field_value( $raw_value, Field $field ) {
		if ( is_array( $raw_value ) ) {
			return array_values(
				array_map(
					static fn( $item ) => sanitize_text_field( wp_unslash( (string) $item ) ),
					$raw_value
				)
			);
		}

		$value = wp_unslash( (string) $raw_value );

		switch ( $field->get_type() ) {
			case 'email':
				return sanitize_email( trim( $value ) );

			case 'url':
				return esc_url_raw( trim( $value ) );

			case 'textarea':
				return sanitize_textarea_field( trim( $value ) );

			case 'number':
				return '' === trim( $value ) ? '' : (string) filter_var( $value, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION );

			case 'checkbox':
				return in_array( strtolower( trim( $value ) ), array( '1', 'true', 'yes', 'on' ), true ) ? '1' : '';

			case 'tel':
				return preg_replace( '/[^0-9+\-\s().]/', '', trim( $value ) ) ?? '';

			case 'select':
			case 'radio':
			case 'hidden':
			case 'text':
			default:
				return sanitize_text_field( trim( $value ) );
		}
	}

	/**
	 * Sanitize an arbitrary raw submission payload map (e.g., for draft auto-save).
	 *
	 * @param array<string, mixed> $input Raw field map.
	 * @return array<string, mixed>
	 */
	public static function sanitize_generic_map( array $input ): array {
		$clean = array();
		foreach ( $input as $key => $value ) {
			$safe_key = sanitize_key( (string) $key );
			if ( '' === $safe_key ) {
				continue;
			}
			if ( is_array( $value ) ) {
				$clean[ $safe_key ] = array_map(
					static fn( $v ) => sanitize_text_field( wp_unslash( (string) $v ) ),
					$value
				);
			} else {
				$str = wp_unslash( (string) $value );
				$clean[ $safe_key ] = str_contains( $str, "\n" )
					? sanitize_textarea_field( $str )
					: sanitize_text_field( $str );
			}
		}

		return $clean;
	}

	/**
	 * Prevent CSV/Excel formula injection.
	 *
	 * @param string $cell Cell text.
	 */
	public static function escape_csv_cell( string $cell ): string {
		if ( '' === $cell ) {
			return '';
		}

		$first = $cell[0];
		if ( in_array( $first, array( '=', '+', '-', '@', "\t", "\r" ), true ) ) {
			return "'" . $cell;
		}

		return $cell;
	}

	/**
	 * Resolve visitor IP address safely.
	 */
	public static function get_client_ip(): string {
		$candidates = array(
			'HTTP_CF_CONNECTING_IP',
			'HTTP_X_FORWARDED_FOR',
			'HTTP_X_REAL_IP',
			'REMOTE_ADDR',
		);

		foreach ( $candidates as $header ) {
			if ( empty( $_SERVER[ $header ] ) ) {
				continue;
			}
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$raw   = wp_unslash( (string) $_SERVER[ $header ] );
			$parts = explode( ',', $raw );
			$ip    = trim( $parts[0] );
			if ( filter_var( $ip, FILTER_VALIDATE_IP ) ) {
				return $ip;
			}
		}

		return '';
	}
}
