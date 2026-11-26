<?php
/**
 * Low-level HTTP client for Zoho CRM API v7 with automatic 401 token refresh.
 *
 * @package ASRS\FormsEngine\Services\Zoho
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services\Zoho;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoClient {

	private const API_VERSION = 'v7';

	private ZohoAuthService $auth;

	public function __construct( ZohoAuthService $auth ) {
		$this->auth = $auth;
	}

	/**
	 * Execute an authenticated Zoho CRM API v7 request.
	 *
	 * @param string                    $method       HTTP verb (GET, POST, PUT, PATCH, DELETE).
	 * @param string                    $path         API path relative to /crm/v7.
	 * @param array<string, mixed>|null $payload      Optional JSON body.
	 * @param array<string, mixed>      $context      Optional context.
	 * @param bool                      $retry_on_401 Refresh token and retry once on HTTP 401.
	 * @return array{code: int, body: array<string, mixed>, raw: string}|WP_Error
	 */
	public function request(
		string $method,
		string $path,
		?array $payload = null,
		array $context = array(),
		bool $retry_on_401 = true
	) {
		$token = $this->auth->get_access_token();
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$url  = $this->auth->get_api_domain() . '/crm/' . self::API_VERSION . '/' . ltrim( $path, '/' );
		$args = array(
			'method'  => strtoupper( $method ),
			'timeout' => 25,
			'headers' => array(
				'Authorization' => 'Zoho-oauthtoken ' . $token,
				'Accept'        => 'application/json',
			),
		);

		if ( null !== $payload ) {
			$args['headers']['Content-Type'] = 'application/json';
			$args['body']                    = wp_json_encode( $payload );
		}

		$response = wp_remote_request( $url, $args );
		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$status_code = (int) wp_remote_retrieve_response_code( $response );
		$raw_body    = (string) wp_remote_retrieve_body( $response );
		$decoded     = json_decode( $raw_body, true );
		if ( ! is_array( $decoded ) ) {
			$decoded = array();
		}

		// Handle expired/invalid access token once automatically.
		if ( 401 === $status_code && $retry_on_401 ) {
			$this->auth->clear_cached_token();
			$refreshed = $this->auth->get_access_token( true );
			if ( ! is_wp_error( $refreshed ) ) {
				return $this->request( $method, $path, $payload, $context, false );
			}
		}

		if ( $status_code < 200 || $status_code >= 300 ) {
			$error_msg = $this->extract_error_message( $decoded, $status_code );
			return new WP_Error(
				'zoho_api_http_error',
				$error_msg,
				array(
					'status_code' => $status_code,
					'body'        => $decoded,
				)
			);
		}

		return array(
			'code' => $status_code,
			'body' => $decoded,
			'raw'  => $raw_body,
		);
	}

	/**
	 * @param array<string, mixed> $decoded     Decoded JSON body.
	 * @param int                  $status_code HTTP status code.
	 */
	private function extract_error_message( array $decoded, int $status_code ): string {
		if ( ! empty( $decoded['message'] ) ) {
			$code = ! empty( $decoded['code'] ) ? '[' . $decoded['code'] . '] ' : '';
			return $code . (string) $decoded['message'];
		}

		if ( ! empty( $decoded['data'][0]['message'] ) ) {
			$code = ! empty( $decoded['data'][0]['code'] ) ? '[' . $decoded['data'][0]['code'] . '] ' : '';
			return $code . (string) $decoded['data'][0]['message'];
		}

		return sprintf( 'Zoho API HTTP %d error', $status_code );
	}
}
