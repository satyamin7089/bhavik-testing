<?php
/**
 * Low-level Zoho Recruit API v2 HTTP Client (JSON + Multipart Resume Upload + 401 Auto-Refresh).
 *
 * @package ASRS\RecruitEngine\Zoho
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Zoho;

use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RecruitClient {

	private RecruitAuthService $auth;

	public function __construct( RecruitAuthService $auth ) {
		$this->auth = $auth;
	}

	/**
	 * Send an authenticated JSON request to Zoho Recruit API v2.
	 *
	 * @param string                    $method   GET, POST, PUT, DELETE.
	 * @param string                    $endpoint Path relative to /recruit/v2 (e.g. /JobOpenings).
	 * @param array<string, mixed>|null $payload  JSON body.
	 * @return array{code: int, body: array<string, mixed>, raw: string}|WP_Error
	 */
	public function request( string $method, string $endpoint, ?array $payload = null, bool $allow_retry = true ) {
		$token = $this->auth->get_access_token( false );
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$url  = $this->auth->get_api_base() . '/' . ltrim( $endpoint, '/' );
		$args = array(
			'method'  => strtoupper( $method ),
			'timeout' => 25,
			'headers' => array(
				'Authorization' => 'Zoho-oauthtoken ' . $token,
				'Accept'        => 'application/json',
			),
		);

		if ( null !== $payload ) {
			$args['headers']['Content-Type'] = 'application/json; charset=utf-8';
			$args['body']                    = wp_json_encode( $payload );
		}

		$res = wp_remote_request( $url, $args );
		if ( is_wp_error( $res ) ) {
			return $res;
		}

		$code = (int) wp_remote_retrieve_response_code( $res );
		$raw  = (string) wp_remote_retrieve_body( $res );
		$json = json_decode( $raw, true );

		if ( 401 === $code && $allow_retry ) {
			$this->auth->clear_cached_token();
			$refreshed = $this->auth->get_access_token( true );
			if ( ! is_wp_error( $refreshed ) ) {
				return $this->request( $method, $endpoint, $payload, false );
			}
		}

		if ( $code < 200 || $code >= 300 ) {
			$msg = is_array( $json ) && ! empty( $json['message'] )
				? (string) $json['message']
				: sprintf( 'Zoho Recruit API HTTP %d', $code );
			if ( is_array( $json ) && ! empty( $json['code'] ) ) {
				$msg = '[' . (string) $json['code'] . '] ' . $msg;
			}
			return new WP_Error( 'recruit_api_error', $msg, $json );
		}

		return array(
			'code' => $code,
			'body' => is_array( $json ) ? $json : array(),
			'raw'  => $raw,
		);
	}

	/**
	 * Upload a file attachment via multipart/form-data to Zoho Recruit API v2.
	 *
	 * @return array{code: int, body: array<string, mixed>, raw: string}|WP_Error
	 */
	public function upload_file( string $endpoint, string $file_path, string $file_name, bool $allow_retry = true ) {
		if ( ! file_exists( $file_path ) || ! is_readable( $file_path ) ) {
			return new WP_Error( 'recruit_file_missing', __( 'Resume file could not be read from disk.', 'asrs-recruit-engine' ) );
		}

		$token = $this->auth->get_access_token( false );
		if ( is_wp_error( $token ) ) {
			return $token;
		}

		$contents = file_get_contents( $file_path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_get_contents_file_get_contents
		if ( false === $contents ) {
			return new WP_Error( 'recruit_file_read_error', __( 'Unable to read resume contents.', 'asrs-recruit-engine' ) );
		}

		$boundary = '----ASRSRecruit' . wp_generate_password( 16, false );
		$mime     = wp_check_filetype( $file_name )['type'] ?: 'application/octet-stream';

		$body  = '--' . $boundary . "\r\n";
		$body .= 'Content-Disposition: form-data; name="file"; filename="' . str_replace( '"', '', $file_name ) . "\"\r\n";
		$body .= 'Content-Type: ' . $mime . "\r\n\r\n";
		$body .= $contents . "\r\n";
		$body .= '--' . $boundary . "--\r\n";

		$url = $this->auth->get_api_base() . '/' . ltrim( $endpoint, '/' );
		$res = wp_remote_post(
			$url,
			array(
				'timeout' => 40,
				'headers' => array(
					'Authorization' => 'Zoho-oauthtoken ' . $token,
					'Content-Type'  => 'multipart/form-data; boundary=' . $boundary,
				),
				'body'    => $body,
			)
		);

		if ( is_wp_error( $res ) ) {
			return $res;
		}

		$code = (int) wp_remote_retrieve_response_code( $res );
		$raw  = (string) wp_remote_retrieve_body( $res );
		$json = json_decode( $raw, true );

		if ( 401 === $code && $allow_retry ) {
			$this->auth->clear_cached_token();
			$refreshed = $this->auth->get_access_token( true );
			if ( ! is_wp_error( $refreshed ) ) {
				return $this->upload_file( $endpoint, $file_path, $file_name, false );
			}
		}

		if ( $code < 200 || $code >= 300 ) {
			$msg = is_array( $json ) && ! empty( $json['message'] )
				? (string) $json['message']
				: sprintf( 'Resume upload failed (HTTP %d)', $code );
			return new WP_Error( 'recruit_upload_error', $msg, $json );
		}

		return array(
			'code' => $code,
			'body' => is_array( $json ) ? $json : array(),
			'raw'  => $raw,
		);
	}
}
