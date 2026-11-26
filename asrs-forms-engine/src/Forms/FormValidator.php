<?php
/**
 * Server-side form validation engine.
 *
 * @package ASRS\FormsEngine\Forms
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormValidator {

	/**
	 * Validate sanitized submission data against form field definitions and form-level rules.
	 *
	 * @param FormInterface        $form Form definition.
	 * @param array<string, mixed> $data Sanitized field values.
	 * @return array<string, string> Associative map of [field_name => error_message].
	 */
	public function validate( FormInterface $form, array $data ): array {
		$errors = array();

		foreach ( $form->get_fields() as $field ) {
			$name  = $field->get_name();
			$label = $field->get_label();
			$value = $data[ $name ] ?? '';
			$rules = $field->get_rules();

			$is_empty = is_array( $value ) ? empty( $value ) : '' === trim( (string) $value );

			if ( $field->is_required() && $is_empty ) {
				$errors[ $name ] = sprintf(
					/* translators: %s: Field label */
					__( '%s is required.', 'asrs-forms-engine' ),
					$label
				);
				continue;
			}

			if ( $is_empty ) {
				continue;
			}

			$str_value = is_array( $value ) ? implode( ',', $value ) : (string) $value;

			switch ( $field->get_type() ) {
				case 'email':
					if ( ! is_email( $str_value ) ) {
						$errors[ $name ] = sprintf(
							/* translators: %s: Field label */
							__( 'Please enter a valid email address for %s.', 'asrs-forms-engine' ),
							$label
						);
					}
					break;

				case 'url':
					if ( ! filter_var( $str_value, FILTER_VALIDATE_URL ) ) {
						$errors[ $name ] = sprintf(
							/* translators: %s: Field label */
							__( 'Please enter a valid URL for %s.', 'asrs-forms-engine' ),
							$label
						);
					}
					break;

				case 'number':
					if ( ! is_numeric( $str_value ) ) {
						$errors[ $name ] = sprintf(
							/* translators: %s: Field label */
							__( '%s must be a valid number.', 'asrs-forms-engine' ),
							$label
						);
					}
					break;

				case 'select':
				case 'radio':
					$options = $field->get_options();
					if ( ! empty( $options ) && ! array_key_exists( $str_value, $options ) ) {
						$errors[ $name ] = sprintf(
							/* translators: %s: Field label */
							__( 'Invalid selection for %s.', 'asrs-forms-engine' ),
							$label
						);
					}
					break;
			}

			if ( isset( $errors[ $name ] ) ) {
				continue;
			}

			if ( isset( $rules['min_length'] ) && mb_strlen( $str_value ) < (int) $rules['min_length'] ) {
				$errors[ $name ] = sprintf(
					/* translators: 1: Field label, 2: minimum character count */
					__( '%1$s must be at least %2$d characters.', 'asrs-forms-engine' ),
					$label,
					(int) $rules['min_length']
				);
				continue;
			}

			if ( isset( $rules['max_length'] ) && mb_strlen( $str_value ) > (int) $rules['max_length'] ) {
				$errors[ $name ] = sprintf(
					/* translators: 1: Field label, 2: maximum character count */
					__( '%1$s must not exceed %2$d characters.', 'asrs-forms-engine' ),
					$label,
					(int) $rules['max_length']
				);
				continue;
			}

			if ( ! empty( $rules['pattern'] ) && 1 !== preg_match( (string) $rules['pattern'], $str_value ) ) {
				$errors[ $name ] = ! empty( $rules['pattern_message'] )
					? (string) $rules['pattern_message']
					: sprintf(
						/* translators: %s: Field label */
						__( '%s format is invalid.', 'asrs-forms-engine' ),
						$label
					);
			}
		}

		// Merge custom PHP form-level validation errors.
		$custom_errors = $form->validate( $data );
		if ( is_array( $custom_errors ) && ! empty( $custom_errors ) ) {
			foreach ( $custom_errors as $field_key => $message ) {
				if ( '' !== (string) $message ) {
					$errors[ sanitize_key( (string) $field_key ) ] = (string) $message;
				}
			}
		}

		/**
		 * Filter validation errors before returning to client.
		 *
		 * @param array<string, string> $errors Validation errors.
		 * @param FormInterface         $form   Form definition.
		 * @param array<string, mixed>  $data   Sanitized values.
		 */
		return apply_filters( 'asrs_forms_validation_errors', $errors, $form, $data );
	}
}
