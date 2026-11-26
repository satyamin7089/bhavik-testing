<?php
/**
 * Zoho Recruit OAuth 2.0 Token Management Service.
 *
 * @package ASRS\RecruitEngine\Zoho
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Zoho;

use ASRS\RecruitEngine\Core\Settings;
use ASRS\RecruitEngine\Security\Crypto;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RecruitAuthService {

	private const TOKEN_TRANSIENT = 'asrs_recruit_access_token';

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function has_credentials(): bool {
		$cfg = $this->settings->get_zoho_settings();
		return '' !== $cfg['client_id'] && '' !== $cfg['client_secret'] && '' !== $cfg['refresh_token'];
	}

	/**
	 * @return string|WP_Error
	 */
	public function get_access_token( bool $force_refresh = false ) {
		if ( ! $this->has_credentials() ) {
			return new WP_Error(
				'recruit_missing_credentials',
				__( 'Zoho Recruit OAuth credentials are not configured yet.', 'asrs-recruit-engine' )
			);
		}

		if ( ! $force_refresh ) {
			$cached = get_transient( self::TOKEN_TRANSIENT );
			if ( is_string( $cached ) && '' !== $cached ) {
				$decrypted = Crypto::decrypt( $cached );
				if ( '' !== $decrypted ) {
					return $decrypted;
				}
			}
		}

		return $this->refresh_access_token();
	}

	/**
	 * @return string|WP_Error
	 */
	public function refresh_access_token() {
		$cfg = $this->settings->get_zoho_settings();
		if ( '' === $cfg['client_id'] || '' === $cfg['client_secret'] || '' === $cfg['refresh_token'] ) {
			return new WP_Error( 'recruit_missing_credentials', __( 'Missing Client ID, Secret, or Refresh Token.', 'asrs-recruit-engine' ) );
		}

		$dc       = RecruitDataCenter::get( (string) $cfg['data_center'] );
		$endpoint = untrailingslashit( $dc['accounts'] ) . '/oauth/v2/token';

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 20,
				'body'    => array(
					'refresh_token' => $cfg['refresh_token'],
					'client_id'     => $cfg['client_id'],
					'client_secret' => $cfg['client_secret'],
					'grant_type'    => 'refresh_token',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			$this->settings->update_zoho_health( 'failed', $response->get_error_message() );
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$json = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $code || ! is_array( $json ) || empty( $json['access_token'] ) ) {
			$err = is_array( $json ) && ! empty( $json['error'] ) ? (string) $json['error'] : __( 'Failed to refresh Zoho Recruit access token.', 'asrs-recruit-engine' );
			$this->settings->update_zoho_health( 'failed', $err );
			return new WP_Error( 'recruit_oauth_refresh_failed', $err );
		}

		$access_token = (string) $json['access_token'];
		$expires_in   = max( 300, (int) ( $json['expires_in'] ?? 3600 ) );
		set_transient( self::TOKEN_TRANSIENT, Crypto::encrypt( $access_token ), max( 60, $expires_in - 120 ) );

		return $access_token;
	}

	/**
	 * @return array<string, mixed>|WP_Error
	 */
	public function exchange_grant_token( string $grant_token ) {
		$cfg = $this->settings->get_zoho_settings();
		if ( '' === $cfg['client_id'] || '' === $cfg['client_secret'] ) {
			return new WP_Error( 'recruit_missing_client', __( 'Save Client ID and Client Secret before exchanging a grant token.', 'asrs-recruit-engine' ) );
		}

		$dc       = RecruitDataCenter::get( (string) $cfg['data_center'] );
		$endpoint = untrailingslashit( $dc['accounts'] ) . '/oauth/v2/token';

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 20,
				'body'    => array(
					'code'          => trim( $grant_token ),
					'client_id'     => $cfg['client_id'],
					'client_secret' => $cfg['client_secret'],
					'grant_type'    => 'authorization_code',
				),
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$json = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $code || ! is_array( $json ) || empty( $json['refresh_token'] ) ) {
			$err = is_array( $json ) && ! empty( $json['error'] ) ? (string) $json['error'] : __( 'Grant token exchange failed.', 'asrs-recruit-engine' );
			return new WP_Error( 'recruit_grant_exchange_failed', $err );
		}

		$this->settings->save_zoho_settings(
			array_merge(
				$cfg,
				array(
					'refresh_token' => (string) $json['refresh_token'],
					'connected_at'  => current_time( 'mysql', true ),
				)
			)
		);

		if ( ! empty( $json['access_token'] ) ) {
			$expires_in = max( 300, (int) ( $json['expires_in'] ?? 3600 ) );
			set_transient( self::TOKEN_TRANSIENT, Crypto::encrypt( (string) $json['access_token'] ), max( 60, $expires_in - 120 ) );
		}

		return $json;
	}

	public function clear_cached_token(): void {
		delete_transient( self::TOKEN_TRANSIENT );
	}

	public function get_api_base(): string {
		$cfg = $this->settings->get_zoho_settings();
		$dc  = RecruitDataCenter::get( (string) $cfg['data_center'] );
		return untrailingslashit( $dc['recruit_api'] ) . '/recruit/v2';
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_token_status(): array {
		$cfg = $this->settings->get_zoho_settings();
		return array(
			'has_credentials'  => $this->has_credentials(),
			'enabled'          => (bool) $cfg['enabled'],
			'last_test_status' => (string) $cfg['last_test_status'],
			'last_tested_at'   => (string) $cfg['last_tested_at'],
			'last_test_error'  => (string) $cfg['last_test_error'],
			'api_base'         => $this->get_api_base(),
		);
	}
}
