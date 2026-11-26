<?php
/**
 * Zoho CRM OAuth 2.0 Token Management Service.
 *
 * @see https://www.zoho.com/crm/developer/docs/api/v7/refresh.html
 * @package ASRS\FormsEngine\Services\Zoho
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services\Zoho;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Security\Crypto;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoAuthService {

	private const TOKEN_TRANSIENT = 'asrs_forms_zoho_access_token';
	private const API_DOMAIN_OPT  = 'asrs_forms_zoho_api_domain';

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function has_credentials(): bool {
		$cfg = $this->settings->get_zoho_settings();
		return '' !== $cfg['client_id'] && '' !== $cfg['client_secret'] && '' !== $cfg['refresh_token'];
	}

	/**
	 * Get a valid OAuth access token, refreshing automatically when expired.
	 *
	 * @return string|WP_Error
	 */
	public function get_access_token( bool $force_refresh = false ) {
		if ( ! $this->has_credentials() ) {
			return new WP_Error(
				'zoho_missing_credentials',
				__( 'Zoho CRM OAuth credentials (Client ID, Client Secret, Refresh Token) are not configured.', 'asrs-forms-engine' )
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
	 * Refresh the OAuth access token using the stored Refresh Token.
	 *
	 * @return string|WP_Error
	 */
	public function refresh_access_token() {
		$cfg = $this->settings->get_zoho_settings();
		if ( '' === $cfg['client_id'] || '' === $cfg['client_secret'] || '' === $cfg['refresh_token'] ) {
			return new WP_Error(
				'zoho_missing_credentials',
				__( 'Missing Zoho Client ID, Client Secret, or Refresh Token.', 'asrs-forms-engine' )
			);
		}

		$dc       = ZohoDataCenter::get( (string) $cfg['data_center'] );
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
			$err_msg = is_array( $json ) && ! empty( $json['error'] )
				? (string) $json['error']
				: __( 'Failed to refresh Zoho OAuth access token.', 'asrs-forms-engine' );

			$this->settings->update_zoho_health( 'failed', $err_msg );
			return new WP_Error( 'zoho_oauth_refresh_failed', $err_msg );
		}

		$access_token = (string) $json['access_token'];
		$expires_in   = max( 300, (int) ( $json['expires_in'] ?? 3600 ) );
		set_transient( self::TOKEN_TRANSIENT, Crypto::encrypt( $access_token ), max( 60, $expires_in - 120 ) );

		if ( ! empty( $json['api_domain'] ) ) {
			update_option( self::API_DOMAIN_OPT, esc_url_raw( (string) $json['api_domain'] ), false );
		}

		return $access_token;
	}

	/**
	 * Exchange a Zoho Self-Client Grant Token for permanent Refresh + Access tokens.
	 *
	 * @return array<string, mixed>|WP_Error
	 */
	public function exchange_grant_token( string $grant_token, string $redirect_uri = '' ) {
		$cfg = $this->settings->get_zoho_settings();
		if ( '' === $cfg['client_id'] || '' === $cfg['client_secret'] ) {
			return new WP_Error(
				'zoho_missing_client',
				__( 'Save Client ID and Client Secret before exchanging a grant token.', 'asrs-forms-engine' )
			);
		}

		$dc       = ZohoDataCenter::get( (string) $cfg['data_center'] );
		$endpoint = untrailingslashit( $dc['accounts'] ) . '/oauth/v2/token';
		$body     = array(
			'code'          => trim( $grant_token ),
			'client_id'     => $cfg['client_id'],
			'client_secret' => $cfg['client_secret'],
			'grant_type'    => 'authorization_code',
		);

		if ( '' !== trim( $redirect_uri ) ) {
			$body['redirect_uri'] = esc_url_raw( trim( $redirect_uri ) );
		}

		$response = wp_remote_post(
			$endpoint,
			array(
				'timeout' => 20,
				'body'    => $body,
			)
		);

		if ( is_wp_error( $response ) ) {
			return $response;
		}

		$code = (int) wp_remote_retrieve_response_code( $response );
		$json = json_decode( (string) wp_remote_retrieve_body( $response ), true );

		if ( 200 !== $code || ! is_array( $json ) || empty( $json['refresh_token'] ) ) {
			$err = is_array( $json ) && ! empty( $json['error'] )
				? (string) $json['error']
				: __( 'Grant token exchange failed. Ensure the grant token has not expired.', 'asrs-forms-engine' );

			return new WP_Error( 'zoho_grant_exchange_failed', $err );
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

		if ( ! empty( $json['api_domain'] ) ) {
			update_option( self::API_DOMAIN_OPT, esc_url_raw( (string) $json['api_domain'] ), false );
		}

		return $json;
	}

	public function clear_cached_token(): void {
		delete_transient( self::TOKEN_TRANSIENT );
	}

	public function get_api_domain(): string {
		$saved = (string) get_option( self::API_DOMAIN_OPT, '' );
		if ( '' !== $saved ) {
			return untrailingslashit( $saved );
		}

		$cfg = $this->settings->get_zoho_settings();
		$dc  = ZohoDataCenter::get( (string) $cfg['data_center'] );

		return untrailingslashit( $dc['api'] );
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_token_status(): array {
		$cfg       = $this->settings->get_zoho_settings();
		$has_token = false;
		$cached    = get_transient( self::TOKEN_TRANSIENT );
		if ( is_string( $cached ) && '' !== $cached && '' !== Crypto::decrypt( $cached ) ) {
			$has_token = true;
		}

		return array(
			'has_credentials'   => $this->has_credentials(),
			'has_cached_access' => $has_token,
			'enabled'           => (bool) $cfg['enabled'],
			'last_test_status'  => (string) $cfg['last_test_status'],
			'last_tested_at'    => (string) $cfg['last_tested_at'],
			'last_test_error'   => (string) $cfg['last_test_error'],
			'api_domain'        => $this->get_api_domain(),
		);
	}
}
