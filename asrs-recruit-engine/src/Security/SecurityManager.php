<?php
/**
 * Anti-spam and Request Security Manager for job applications.
 *
 * @package ASRS\RecruitEngine\Security
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Security;

use ASRS\RecruitEngine\Core\Settings;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SecurityManager {

	/** Action name sent with reCAPTCHA v3 tokens and checked on the server. */
	public const RECAPTCHA_ACTION = 'asrs_recruit_apply';

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	/**
	 * @param array<string, mixed> $post
	 * @return true|WP_Error
	 */
	public function verify_application_request( array $post ) {
		$nonce = sanitize_text_field( (string) ( $post['asrs_recruit_nonce'] ?? '' ) );
		if ( ! wp_verify_nonce( $nonce, 'asrs_recruit_apply' ) ) {
			return new WP_Error( 'invalid_nonce', __( 'Security verification expired. Please refresh and try again.', 'asrs-recruit-engine' ) );
		}

		// Honeypot check.
		if ( ! empty( $post['asrs_website_hp'] ) ) {
			return new WP_Error( 'spam_detected', __( 'Spam check failed.', 'asrs-recruit-engine' ) );
		}

		// Minimum human dwell time check (2 seconds).
		$rendered_at = (int) ( $post['asrs_rendered_at'] ?? 0 );
		if ( $rendered_at > 0 && ( time() - $rendered_at ) < 2 ) {
			return new WP_Error( 'too_fast', __( 'Please wait a moment before submitting your application.', 'asrs-recruit-engine' ) );
		}

		// Optional Cloudflare Turnstile verification.
		if ( $this->settings->is_turnstile_enabled() ) {
			$token  = sanitize_text_field( (string) ( $post['cf-turnstile-response'] ?? '' ) );
			$secret = (string) $this->settings->get( 'turnstile_secret_key', '' );
			if ( '' === $token ) {
				return new WP_Error( 'turnstile_missing', __( 'Please complete the human verification challenge.', 'asrs-recruit-engine' ) );
			}

			$res = wp_remote_post(
				'https://challenges.cloudflare.com/turnstile/v0/siteverify',
				array(
					'timeout' => 10,
					'body'    => array(
						'secret'   => $secret,
						'response' => $token,
						'remoteip' => $this->get_client_ip(),
					),
				)
			);

			if ( ! is_wp_error( $res ) ) {
				$json = json_decode( (string) wp_remote_retrieve_body( $res ), true );
				if ( empty( $json['success'] ) ) {
					return new WP_Error( 'turnstile_failed', __( 'Human verification failed. Please try again.', 'asrs-recruit-engine' ) );
				}
			}
		}

		// Optional Google reCAPTCHA (v2 checkbox / v3 score) verification.
		if ( $this->settings->is_recaptcha_enabled() ) {
			$captcha = $this->verify_recaptcha( sanitize_text_field( (string) ( $post['g-recaptcha-response'] ?? '' ) ) );
			if ( is_wp_error( $captcha ) ) {
				return $captcha;
			}
		}

		return true;
	}

	/**
	 * Verify a Google reCAPTCHA token with the siteverify API.
	 *
	 * @return true|WP_Error
	 */
	private function verify_recaptcha( string $token ) {
		$version = $this->settings->get_recaptcha_version();

		if ( '' === trim( $token ) ) {
			return new WP_Error(
				'recaptcha_missing',
				'v3' === $version
					? __( 'Security verification could not be completed. Please refresh the page and try again.', 'asrs-recruit-engine' )
					: __( 'Please confirm that you are not a robot.', 'asrs-recruit-engine' )
			);
		}

		$body = array(
			'secret'   => (string) $this->settings->get( 'recaptcha_secret_key', '' ),
			'response' => $token,
		);
		$ip = $this->get_client_ip();
		if ( '' !== $ip ) {
			$body['remoteip'] = $ip;
		}

		$res = wp_remote_post(
			'https://www.google.com/recaptcha/api/siteverify',
			array(
				'timeout' => 10,
				'body'    => $body,
			)
		);

		if ( is_wp_error( $res ) ) {
			return new WP_Error( 'recaptcha_unreachable', __( 'Unable to verify the security check. Please try again.', 'asrs-recruit-engine' ) );
		}

		$json = json_decode( (string) wp_remote_retrieve_body( $res ), true );
		if ( 200 !== (int) wp_remote_retrieve_response_code( $res ) || ! is_array( $json ) || empty( $json['success'] ) ) {
			return new WP_Error( 'recaptcha_failed', __( 'Human verification failed. Please try again.', 'asrs-recruit-engine' ) );
		}

		if ( 'v3' === $version ) {
			$action    = isset( $json['action'] ) ? (string) $json['action'] : '';
			$score     = isset( $json['score'] ) ? (float) $json['score'] : 0.0;
			$min_score = max( 0.0, min( 1.0, (float) $this->settings->get( 'recaptcha_min_score', 0.5 ) ) );

			if ( '' !== $action && self::RECAPTCHA_ACTION !== $action ) {
				return new WP_Error( 'recaptcha_action', __( 'Human verification failed. Please try again.', 'asrs-recruit-engine' ) );
			}
			if ( $score < $min_score ) {
				return new WP_Error( 'recaptcha_low_score', __( 'Your application was flagged by our spam protection. Please try again or contact us directly.', 'asrs-recruit-engine' ) );
			}
		}

		return true;
	}

	public function get_client_ip(): string {
		$ip = sanitize_text_field( (string) ( $_SERVER['REMOTE_ADDR'] ?? '' ) );
		return filter_var( $ip, FILTER_VALIDATE_IP ) ? $ip : '';
	}
}
