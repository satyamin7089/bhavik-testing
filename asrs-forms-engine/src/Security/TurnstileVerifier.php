<?php
/**
 * Cloudflare Turnstile server-side verification service.
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

class TurnstileVerifier {

	private const VERIFY_ENDPOINT = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * Check whether Cloudflare Turnstile is configured and enabled.
	 */
	public function is_enabled(): bool {
		$site_key   = (string) $this->settings->get( 'turnstile_site_key', '' );
		$secret_key = (string) $this->settings->get( 'turnstile_secret_key', '' );

		return (bool) $this->settings->get( 'turnstile_enabled', false )
			&& '' !== $site_key
			&& '' !== $secret_key;
	}

	/**
	 * Get configured Turnstile site key.
	 */
	public function get_site_key(): string {
		return (string) $this->settings->get( 'turnstile_site_key', '' );
	}

	/**
	 * Verify Turnstile token against Cloudflare siteverify API.
	 *
	 * @param string $token Token from cf-turnstile-response field.
	 * @return true|WP_Error
	 */
	public function verify( string $token ) {
		if ( ! $this->is_enabled() ) {
			return true;
		}

		$token = trim( $token );
		if ( '' === $token ) {
			return new WP_Error(
				'asrs_turnstile_missing',
				__( 'Please complete the security verification challenge.', 'asrs-forms-engine' )
			);
		}

		$secret = (string) $this->settings->get( 'turnstile_secret_key', '' );

		$body = array(
			'secret'   => $secret,
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
				'asrs_turnstile_unreachable',
				__( 'Unable to verify security challenge. Please try again.', 'asrs-forms-engine' )
			);
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$payload     = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $status_code || ! is_array( $payload ) || empty( $payload['success'] ) ) {
			return new WP_Error(
				'asrs_turnstile_failed',
				__( 'Security verification failed. Please refresh and try again.', 'asrs-forms-engine' )
			);
		}

		return true;
	}
}
