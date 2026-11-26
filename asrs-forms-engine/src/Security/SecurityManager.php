<?php
/**
 * Request security, CSRF, Honeypot, Timing, Turnstile, and Google reCAPTCHA enforcement.
 *
 * @package ASRS\FormsEngine\Security
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Security;

use ASRS\FormsEngine\Core\Capabilities;
use ASRS\FormsEngine\Core\Settings;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SecurityManager {

	private Settings $settings;
	private TurnstileVerifier $turnstile;
	private ReCaptchaVerifier $recaptcha;

	public function __construct(
		Settings $settings,
		TurnstileVerifier $turnstile,
		ReCaptchaVerifier $recaptcha
	) {
		$this->settings  = $settings;
		$this->turnstile = $turnstile;
		$this->recaptcha = $recaptcha;
	}

	/**
	 * Verify frontend form submission security controls.
	 *
	 * @param string               $form_id Form identifier.
	 * @param array<string, mixed> $post    Request payload.
	 * @return true|WP_Error
	 */
	public function verify_submission_request( string $form_id, array $post ) {
		$ajax_nonce = isset( $post['nonce'] ) ? sanitize_text_field( wp_unslash( (string) $post['nonce'] ) ) : '';
		$form_nonce = isset( $post['asrs_form_nonce'] ) ? sanitize_text_field( wp_unslash( (string) $post['asrs_form_nonce'] ) ) : '';

		if ( ! wp_verify_nonce( $ajax_nonce, 'asrs_forms_frontend_nonce' ) ) {
			return new WP_Error(
				'asrs_invalid_nonce',
				__( 'Security check failed (invalid session token). Please reload the page.', 'asrs-forms-engine' )
			);
		}

		if ( '' !== $form_nonce && ! wp_verify_nonce( $form_nonce, 'asrs_form_submit_' . $form_id ) ) {
			return new WP_Error(
				'asrs_invalid_form_nonce',
				__( 'Form security token expired. Please reload the page and try again.', 'asrs-forms-engine' )
			);
		}

		if ( (bool) $this->settings->get( 'honeypot_enabled', true ) ) {
			$honeypot = isset( $post['asrs_hp_website'] ) ? trim( (string) wp_unslash( $post['asrs_hp_website'] ) ) : '';
			if ( '' !== $honeypot ) {
				return new WP_Error(
					'asrs_spam_detected',
					__( 'Your submission could not be processed.', 'asrs-forms-engine' )
				);
			}

			$min_seconds = (int) $this->settings->get( 'min_submit_seconds', 2 );
			if ( $min_seconds > 0 && isset( $post['asrs_rendered_at'] ) ) {
				$rendered_at = (int) $post['asrs_rendered_at'];
				if ( $rendered_at > 0 && ( time() - $rendered_at ) < $min_seconds ) {
					return new WP_Error(
						'asrs_too_fast',
						__( 'Please take a moment before submitting the form.', 'asrs-forms-engine' )
					);
				}
			}
		}

		if ( $this->turnstile->is_enabled() ) {
			$token      = isset( $post['cf-turnstile-response'] ) ? sanitize_text_field( wp_unslash( (string) $post['cf-turnstile-response'] ) ) : '';
			$turn_check = $this->turnstile->verify( $token );
			if ( is_wp_error( $turn_check ) ) {
				return $turn_check;
			}
		}

		if ( $this->recaptcha->is_enabled() ) {
			$rc_token = isset( $post['g-recaptcha-response'] ) ? sanitize_text_field( wp_unslash( (string) $post['g-recaptcha-response'] ) ) : '';
			$rc_check = $this->recaptcha->verify( $rc_token );
			if ( is_wp_error( $rc_check ) ) {
				return $rc_check;
			}
		}

		return true;
	}

	/**
	 * Enforce admin capability + nonce for admin AJAX requests.
	 */
	public function verify_admin_ajax( string $nonce_action = 'asrs_forms_admin_nonce', string $nonce_field = 'nonce' ): void {
		if ( ! Capabilities::current_user_can_manage() ) {
			wp_send_json_error(
				array( 'message' => __( 'Insufficient permissions.', 'asrs-forms-engine' ) ),
				403
			);
		}

		if ( ! check_ajax_referer( $nonce_action, $nonce_field, false ) ) {
			wp_send_json_error(
				array( 'message' => __( 'Security token expired. Refresh the page and retry.', 'asrs-forms-engine' ) ),
				403
			);
		}
	}
}
