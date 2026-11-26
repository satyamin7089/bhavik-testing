<?php
/**
 * Google reCAPTCHA server-side verification service.
 *
 * Supports both v2 ("I'm not a robot" checkbox) and v3 (invisible, score-based)
 * keys. Mirrors the reCAPTCHA support in the Recruit Engine.
 *
 * @package ASRS\FormsEngine\Security
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Security;

use ASRS\FormsEngine\Core\Settings;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ReCaptchaVerifier {

	/** Action name sent with reCAPTCHA v3 tokens and checked on the server. */
	public const RECAPTCHA_ACTION = 'asrs_forms_submit';

	private const VERIFY_ENDPOINT = 'https://www.google.com/recaptcha/api/siteverify';

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Check whether Google reCAPTCHA is configured and enabled.
	 */
	public function is_enabled(): bool {
		$site_key   = $this->get_site_key();
		$secret_key = trim( (string) $this->settings->get( 'recaptcha_secret_key', '' ) );

		return (bool) $this->settings->get( 'recaptcha_enabled', false )
			&& '' !== $site_key
			&& '' !== $secret_key;
	}

	/**
	 * Return configuration status for WP Admin UI badges.
	 *
	 * @return string 'connected' | 'pending' | 'disconnected'
	 */
	public function get_status(): string {
		if ( ! (bool) $this->settings->get( 'recaptcha_enabled', false ) ) {
			return 'disconnected';
		}

		return $this->is_enabled() ? 'connected' : 'pending';
	}

	/**
	 * Get configured reCAPTCHA site key.
	 */
	public function get_site_key(): string {
		return trim( (string) $this->settings->get( 'recaptcha_site_key', '' ) );
	}

	/**
	 * Configured reCAPTCHA version: 'v2' (checkbox) or 'v3' (invisible score).
	 */
	public function get_version(): string {
		return 'v3' === (string) $this->settings->get( 'recaptcha_version', 'v2' ) ? 'v3' : 'v2';
	}

	/**
	 * Minimum acceptable v3 score (0.0 – 1.0).
	 */
	public function get_min_score(): float {
		return max( 0.0, min( 1.0, (float) $this->settings->get( 'recaptcha_min_score', 0.5 ) ) );
	}

	/**
	 * Action name expected from a v3 token.
	 */
	public function get_action(): string {
		return self::RECAPTCHA_ACTION;
	}

	/**
	 * Verify a Google reCAPTCHA token against the siteverify API.
	 *
	 * @param string $token Token from g-recaptcha-response / v3 execute().
	 * @return true|WP_Error
	 */
	public function verify( string $token ) {
		if ( ! $this->is_enabled() ) {
			return true;
		}

		$token   = trim( $token );
		$version = $this->get_version();

		if ( '' === $token ) {
			$msg = 'v3' === $version
				? __( 'Security verification could not be completed. Please refresh the page and try again.', 'asrs-forms-engine' )
				: __( 'Please confirm that you are not a robot.', 'asrs-forms-engine' );

			return new WP_Error(
				'asrs_recaptcha_missing',
				$msg,
				'v2' === $version ? array( 'field_errors' => array( 'recaptcha' => $msg ) ) : array()
			);
		}

		$body = array(
			'secret'   => trim( (string) $this->settings->get( 'recaptcha_secret_key', '' ) ),
			'response' => $token,
		);

		$ip = Sanitizer::get_client_ip();
		if ( '' !== $ip ) {
			$body['remoteip'] = $ip;
		}

		$response = wp_remote_post(
			self::VERIFY_ENDPOINT,
			array(
				'timeout' => 10,
				'body'    => $body,
			)
		);

		if ( is_wp_error( $response ) ) {
			return new WP_Error(
				'asrs_recaptcha_unreachable',
				__( 'Unable to verify the security check. Please try again.', 'asrs-forms-engine' )
			);
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$payload     = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status_code || ! is_array( $payload ) || empty( $payload['success'] ) ) {
			$msg = __( 'Human verification failed. Please try again.', 'asrs-forms-engine' );
			return new WP_Error(
				'asrs_recaptcha_failed',
				$msg,
				'v2' === $version ? array( 'field_errors' => array( 'recaptcha' => $msg ) ) : array()
			);
		}

		if ( 'v3' === $version ) {
			$action = isset( $payload['action'] ) ? (string) $payload['action'] : '';
			$score  = isset( $payload['score'] ) ? (float) $payload['score'] : 0.0;

			if ( '' !== $action && self::RECAPTCHA_ACTION !== $action ) {
				return new WP_Error(
					'asrs_recaptcha_action',
					__( 'Human verification failed. Please try again.', 'asrs-forms-engine' )
				);
			}

			if ( $score < $this->get_min_score() ) {
				return new WP_Error(
					'asrs_recaptcha_low_score',
					__( 'Your submission was flagged by our spam protection. Please try again or contact us directly.', 'asrs-forms-engine' )
				);
			}
		}

		return true;
	}
}
