<?php
/**
 * Centralized settings manager for ASRS Recruit Engine.
 *
 * @package ASRS\RecruitEngine\Core
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Core;

use ASRS\RecruitEngine\Security\Crypto;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Settings {

	public const OPTION_GENERAL = 'asrs_recruit_settings';
	public const OPTION_ZOHO    = 'asrs_recruit_zoho_settings';

	/**
	 * @return array<string, mixed>
	 */
	public static function default_general(): array {
		return array(
			'careers_heading'          => __( 'Open Positions', 'asrs-recruit-engine' ),
			'careers_subtitle'         => __( 'Explore current opportunities and apply directly online.', 'asrs-recruit-engine' ),
			'honeypot_enabled'         => true,
			'min_submit_seconds'       => 2,
			'turnstile_enabled'        => false,
			'turnstile_site_key'       => '',
			'turnstile_secret_key'     => '',
			'recaptcha_enabled'        => false,
			'recaptcha_version'        => 'v2',
			'recaptcha_site_key'       => '',
			'recaptcha_secret_key'     => '',
			'recaptcha_min_score'      => 0.5,
			'max_resume_mb'            => 5,
			'only_published_jobs'      => false,
			'allowed_job_statuses'     => 'In-progress, Waiting for approval, Open',
			'auto_retry_enabled'       => true,
			'max_retry_attempts'       => 5,
			'notify_on_sync_failure'   => true,
			'sync_failure_email'       => (string) get_option( 'admin_email', '' ),
			'delete_data_on_uninstall' => false,
		);
	}

	/**
	 * @return array<string, mixed>
	 */
	public static function default_zoho(): array {
		return array(
			'enabled'          => false,
			'data_center'      => 'in',
			'client_id'        => '',
			'client_secret'    => '',
			'refresh_token'    => '',
			'trigger_workflow' => true,
			'candidate_source' => 'Website Careers',
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
	 * @param mixed $default Fallback default.
	 * @return mixed
	 */
	public function get( string $key, $default = null ) {
		$all = $this->get_all_general();
		return $all[ $key ] ?? $default;
	}

	public function get_all(): array {
		return $this->get_all_general();
	}

	/**
	 * @param array<string, mixed> $input Raw POST array.
	 * @return array<string, mixed>
	 */
	public function save_all( array $input ): array {
		return $this->save_general( $input );
	}

	public function is_turnstile_enabled(): bool {
		$all = $this->get_all_general();
		return ! empty( $all['turnstile_enabled'] ) && '' !== (string) $all['turnstile_site_key'] && '' !== (string) $all['turnstile_secret_key'];
	}

	public function is_recaptcha_enabled(): bool {
		$all = $this->get_all_general();
		return ! empty( $all['recaptcha_enabled'] ) && '' !== trim( (string) $all['recaptcha_site_key'] ) && '' !== (string) $all['recaptcha_secret_key'];
	}

	/**
	 * Configured reCAPTCHA version: 'v2' (checkbox) or 'v3' (invisible score).
	 */
	public function get_recaptcha_version(): string {
		return 'v3' === (string) $this->get( 'recaptcha_version', 'v2' ) ? 'v3' : 'v2';
	}

	/**
	 * @param array<string, mixed> $input Raw POST array.
	 * @return array<string, mixed>
	 */
	public function save_general( array $input ): array {
		$current = $this->get_all_general();

		$clean = array(
			'careers_heading'          => sanitize_text_field( (string) ( $input['careers_heading'] ?? $current['careers_heading'] ) ),
			'careers_subtitle'         => sanitize_text_field( (string) ( $input['careers_subtitle'] ?? $current['careers_subtitle'] ) ),
			'honeypot_enabled'         => array_key_exists( 'honeypot_enabled', $input ) ? ! empty( $input['honeypot_enabled'] ) : (bool) $current['honeypot_enabled'],
			'min_submit_seconds'       => max( 0, min( 30, (int) ( $input['min_submit_seconds'] ?? $current['min_submit_seconds'] ) ) ),
			'turnstile_enabled'        => ! empty( $input['turnstile_enabled'] ),
			'turnstile_site_key'       => sanitize_text_field( (string) ( $input['turnstile_site_key'] ?? '' ) ),
			'turnstile_secret_key'     => $this->resolve_secret(
				(string) ( $input['turnstile_secret_key'] ?? '' ),
				(string) $current['turnstile_secret_key']
			),
			'recaptcha_enabled'        => array_key_exists( 'recaptcha_enabled', $input ) ? ! empty( $input['recaptcha_enabled'] ) : (bool) $current['recaptcha_enabled'],
			'recaptcha_version'        => 'v3' === (string) ( $input['recaptcha_version'] ?? $current['recaptcha_version'] ) ? 'v3' : 'v2',
			'recaptcha_site_key'       => sanitize_text_field( (string) ( $input['recaptcha_site_key'] ?? $current['recaptcha_site_key'] ) ),
			'recaptcha_secret_key'     => $this->resolve_secret(
				(string) ( $input['recaptcha_secret_key'] ?? '' ),
				(string) $current['recaptcha_secret_key']
			),
			'recaptcha_min_score'      => round( max( 0.0, min( 1.0, (float) ( $input['recaptcha_min_score'] ?? $current['recaptcha_min_score'] ) ) ), 2 ),
			'max_resume_mb'            => max( 1, min( 20, (int) ( $input['max_resume_mb'] ?? 5 ) ) ),
			'only_published_jobs'      => ! empty( $input['only_published_jobs'] ),
			'allowed_job_statuses'     => sanitize_text_field( (string) ( $input['allowed_job_statuses'] ?? '' ) ),
			'auto_retry_enabled'       => array_key_exists( 'auto_retry_enabled', $input ) ? ! empty( $input['auto_retry_enabled'] ) : (bool) $current['auto_retry_enabled'],
			'max_retry_attempts'       => max( 1, min( 20, (int) ( $input['max_retry_attempts'] ?? 5 ) ) ),
			'notify_on_sync_failure'   => ! empty( $input['notify_on_sync_failure'] ),
			'sync_failure_email'       => sanitize_email( (string) ( $input['sync_failure_email'] ?? '' ) ),
			'delete_data_on_uninstall' => ! empty( $input['delete_data_on_uninstall'] ),
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
	 * @param array<string, mixed> $input Raw Zoho settings POST.
	 * @return array<string, mixed>
	 */
	public function save_zoho_settings( array $input ): array {
		$current     = $this->get_zoho_settings();
		$allowed_dcs = array( 'com', 'in', 'eu', 'com.au', 'jp', 'com.cn' );
		$dc          = sanitize_text_field( (string) ( $input['data_center'] ?? $current['data_center'] ) );
		if ( ! in_array( $dc, $allowed_dcs, true ) ) {
			$dc = 'in';
		}

		$clean = array(
			'enabled'          => ! empty( $input['enabled'] ),
			'data_center'      => $dc,
			'client_id'        => sanitize_text_field( (string) ( $input['client_id'] ?? $current['client_id'] ) ),
			'client_secret'    => $this->resolve_secret(
				(string) ( $input['client_secret'] ?? '' ),
				(string) $current['client_secret']
			),
			'refresh_token'    => $this->resolve_secret(
				(string) ( $input['refresh_token'] ?? '' ),
				(string) $current['refresh_token']
			),
			'trigger_workflow' => ! empty( $input['trigger_workflow'] ),
			'candidate_source' => sanitize_text_field( (string) ( $input['candidate_source'] ?? 'Website Careers' ) ),
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

	private function resolve_secret( string $submitted, string $existing ): string {
		$submitted = trim( $submitted );
		if ( '' === $submitted || str_starts_with( $submitted, '••••' ) ) {
			return $existing;
		}
		return sanitize_text_field( $submitted );
	}
}
