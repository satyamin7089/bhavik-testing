<?php
/**
 * Centralized settings manager for ASRS Forms Engine.
 *
 * @package ASRS\FormsEngine\Core
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Core;

use ASRS\FormsEngine\Security\Crypto;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	public const OPTION_GENERAL = 'asrs_forms_settings';
	public const OPTION_ZOHO    = 'asrs_forms_zoho_settings';

	/**
	 * @return array<string, mixed>
	 */
	public static function default_general(): array {
		return array(
			'honeypot_enabled'          => true,
			'min_submit_seconds'        => 2,
			'turnstile_enabled'         => false,
			'turnstile_site_key'        => '',
			'turnstile_secret_key'      => '',
			'recaptcha_enabled'         => false,
			'recaptcha_version'         => 'v2',
			'recaptcha_site_key'        => '',
			'recaptcha_secret_key'      => '',
			'recaptcha_min_score'       => 0.5,
			'store_ip_address'          => true,
			'auto_retry_enabled'        => true,
			'max_retry_attempts'        => 5,
			'retry_base_delay_min'      => 5,
			'admin_notify_on_sync_fail' => true,
			'notification_email'        => (string) get_option( 'admin_email', '' ),
			'delete_data_on_uninstall'  => false,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function default_zoho(): array {
		return array(
			'enabled'          => false,
			'connection_name'  => 'Primary Zoho CRM',
			'data_center'      => 'in',
			'client_id'        => '',
			'client_secret'    => '',
			'refresh_token'    => '',
			'default_module'   => 'Leads',
			'trigger_workflow' => true,
			'connected_at'     => '',
			'last_tested_at'   => '',
			'last_test_status' => 'unknown',
			'last_test_error'  => '',
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_all_general(): array {
		$saved = get_option( self::OPTION_GENERAL, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$merged = wp_parse_args( $saved, self::default_general() );
		if ( ! empty( $merged['turnstile_secret_key'] ) ) {
			$merged['turnstile_secret_key'] = Crypto::decrypt( (string) $merged['turnstile_secret_key'] );
		}
		if ( ! empty( $merged['recaptcha_secret_key'] ) ) {
			$merged['recaptcha_secret_key'] = Crypto::decrypt( (string) $merged['recaptcha_secret_key'] );
		}

		return $merged;
	}

	/**
	 * @param string $key     Setting key.
	 * @param mixed  $default Fallback default.
	 * @return mixed
	 */
	public function get( string $key, $default = null ) {
		$all = $this->get_all_general();
		return $all[ $key ] ?? $default;
	}

	/**
	 * @param array<string, mixed> $input Raw form POST input.
	 * @return array<string, mixed>
	 */
	public function save_general( array $input ): array {
		$current = $this->get_all_general();

		$clean = array(
			'honeypot_enabled'          => ! empty( $input['honeypot_enabled'] ),
			'min_submit_seconds'        => max( 0, min( 30, (int) ( $input['min_submit_seconds'] ?? 2 ) ) ),
			'turnstile_enabled'         => ! empty( $input['turnstile_enabled'] ),
			'turnstile_site_key'        => sanitize_text_field( (string) ( $input['turnstile_site_key'] ?? '' ) ),
			'turnstile_secret_key'      => $this->resolve_secret_input(
				(string) ( $input['turnstile_secret_key'] ?? '' ),
				(string) $current['turnstile_secret_key']
			),
			'recaptcha_enabled'         => ! empty( $input['recaptcha_enabled'] ),
			'recaptcha_version'         => 'v3' === (string) ( $input['recaptcha_version'] ?? 'v2' ) ? 'v3' : 'v2',
			'recaptcha_site_key'        => sanitize_text_field( (string) ( $input['recaptcha_site_key'] ?? '' ) ),
			'recaptcha_secret_key'      => $this->resolve_secret_input(
				(string) ( $input['recaptcha_secret_key'] ?? '' ),
				(string) $current['recaptcha_secret_key']
			),
			'recaptcha_min_score'       => round( max( 0.0, min( 1.0, (float) ( $input['recaptcha_min_score'] ?? 0.5 ) ) ), 2 ),
			'store_ip_address'          => ! empty( $input['store_ip_address'] ),
			'auto_retry_enabled'        => ! empty( $input['auto_retry_enabled'] ),
			'max_retry_attempts'        => max( 1, min( 20, (int) ( $input['max_retry_attempts'] ?? 5 ) ) ),
			'retry_base_delay_min'      => max( 1, min( 120, (int) ( $input['retry_base_delay_min'] ?? 5 ) ) ),
			'admin_notify_on_sync_fail' => ! empty( $input['admin_notify_on_sync_fail'] ),
			'notification_email'        => sanitize_email( (string) ( $input['notification_email'] ?? get_option( 'admin_email', '' ) ) ),
			'delete_data_on_uninstall'  => ! empty( $input['delete_data_on_uninstall'] ),
		);

		$stored = $clean;
		if ( '' !== $stored['turnstile_secret_key'] ) {
			$stored['turnstile_secret_key'] = Crypto::encrypt( $stored['turnstile_secret_key'] );
		}
		if ( '' !== $stored['recaptcha_secret_key'] ) {
			$stored['recaptcha_secret_key'] = Crypto::encrypt( $stored['recaptcha_secret_key'] );
		}

		update_option( self::OPTION_GENERAL, $stored, false );

		return $clean;
	}

	/**
	 * Save only Google reCAPTCHA integration fields while preserving all other general settings.
	 *
	 * @param array<string, mixed> $input Raw form POST input.
	 * @return array<string, mixed>
	 */
	public function save_recaptcha_settings( array $input ): array {
		$current = $this->get_all_general();

		$current['recaptcha_enabled']    = ! empty( $input['recaptcha_enabled'] );
		$current['recaptcha_version']    = 'v3' === (string) ( $input['recaptcha_version'] ?? 'v2' ) ? 'v3' : 'v2';
		$current['recaptcha_site_key']   = sanitize_text_field( (string) ( $input['recaptcha_site_key'] ?? '' ) );
		$current['recaptcha_secret_key'] = $this->resolve_secret_input(
			(string) ( $input['recaptcha_secret_key'] ?? '' ),
			(string) $current['recaptcha_secret_key']
		);
		$current['recaptcha_min_score']  = round( max( 0.0, min( 1.0, (float) ( $input['recaptcha_min_score'] ?? 0.5 ) ) ), 2 );

		$stored = $current;
		if ( '' !== (string) $stored['turnstile_secret_key'] ) {
			$stored['turnstile_secret_key'] = Crypto::encrypt( (string) $stored['turnstile_secret_key'] );
		}
		if ( '' !== (string) $stored['recaptcha_secret_key'] ) {
			$stored['recaptcha_secret_key'] = Crypto::encrypt( (string) $stored['recaptcha_secret_key'] );
		}

		update_option( self::OPTION_GENERAL, $stored, false );

		return $current;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_zoho_settings(): array {
		$saved = get_option( self::OPTION_ZOHO, array() );
		if ( ! is_array( $saved ) ) {
			$saved = array();
		}

		$merged = wp_parse_args( $saved, self::default_zoho() );

		if ( ! empty( $merged['client_secret'] ) ) {
			$merged['client_secret'] = Crypto::decrypt( (string) $merged['client_secret'] );
		}
		if ( ! empty( $merged['refresh_token'] ) ) {
			$merged['refresh_token'] = Crypto::decrypt( (string) $merged['refresh_token'] );
		}

		return $merged;
	}

	/**
	 * @param array<string, mixed> $input Raw Zoho settings input.
	 * @return array<string, mixed>
	 */
	public function save_zoho_settings( array $input ): array {
		$current = $this->get_zoho_settings();

		$allowed_dcs = array( 'com', 'eu', 'in', 'com.au', 'jp', 'com.cn', 'ca', 'sa' );
		$dc          = sanitize_text_field( (string) ( $input['data_center'] ?? $current['data_center'] ) );
		if ( ! in_array( $dc, $allowed_dcs, true ) ) {
			$dc = 'com';
		}

		$clean = array(
			'enabled'          => ! empty( $input['enabled'] ),
			'connection_name'  => sanitize_text_field( (string) ( $input['connection_name'] ?? 'Primary Zoho CRM' ) ),
			'data_center'      => $dc,
			'client_id'        => sanitize_text_field( (string) ( $input['client_id'] ?? $current['client_id'] ) ),
			'client_secret'    => $this->resolve_secret_input(
				(string) ( $input['client_secret'] ?? '' ),
				(string) $current['client_secret']
			),
			'refresh_token'    => $this->resolve_secret_input(
				(string) ( $input['refresh_token'] ?? '' ),
				(string) $current['refresh_token']
			),
			'default_module'   => sanitize_text_field( (string) ( $input['default_module'] ?? 'Leads' ) ),
			'trigger_workflow' => ! empty( $input['trigger_workflow'] ),
			'connected_at'     => (string) ( $input['connected_at'] ?? $current['connected_at'] ),
			'last_tested_at'   => (string) ( $input['last_tested_at'] ?? $current['last_tested_at'] ),
			'last_test_status' => sanitize_key( (string) ( $input['last_test_status'] ?? $current['last_test_status'] ) ),
			'last_test_error'  => sanitize_text_field( (string) ( $input['last_test_error'] ?? $current['last_test_error'] ) ),
		);

		$stored = $clean;
		if ( '' !== $stored['client_secret'] ) {
			$stored['client_secret'] = Crypto::encrypt( $stored['client_secret'] );
		}
		if ( '' !== $stored['refresh_token'] ) {
			$stored['refresh_token'] = Crypto::encrypt( $stored['refresh_token'] );
		}

		update_option( self::OPTION_ZOHO, $stored, false );

		return $clean;
	}

	public function update_zoho_health( string $status, string $error = '' ): void {
		$current                     = $this->get_zoho_settings();
		$current['last_tested_at']   = current_time( 'mysql', true );
		$current['last_test_status'] = sanitize_key( $status );
		$current['last_test_error']  = sanitize_text_field( $error );
		if ( 'connected' === $status && empty( $current['connected_at'] ) ) {
			$current['connected_at'] = current_time( 'mysql', true );
		}

		$stored = $current;
		if ( '' !== $stored['client_secret'] ) {
			$stored['client_secret'] = Crypto::encrypt( $stored['client_secret'] );
		}
		if ( '' !== $stored['refresh_token'] ) {
			$stored['refresh_token'] = Crypto::encrypt( $stored['refresh_token'] );
		}

		update_option( self::OPTION_ZOHO, $stored, false );
	}

	private function resolve_secret_input( string $submitted, string $existing ): string {
		$submitted = trim( $submitted );
		if ( '' === $submitted || str_starts_with( $submitted, '••••' ) ) {
			return $existing;
		}
		if ( '__CLEAR__' === $submitted ) {
			return '';
		}
		return sanitize_text_field( $submitted );
	}
}
