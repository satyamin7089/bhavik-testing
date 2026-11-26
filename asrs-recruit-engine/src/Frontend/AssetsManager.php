<?php
/**
 * Frontend & Admin Asset Enqueue Manager with filemtime() cache-busting.
 *
 * @package ASRS\RecruitEngine\Frontend
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Frontend;

use ASRS\RecruitEngine\Core\Settings;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AssetsManager {

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function register(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_frontend_assets' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_assets' ) );
	}

	public function register_frontend_assets(): void {
		$css_path = ASRS_RECRUIT_PATH . 'assets/css/asrs-recruit.css';
		$js_path  = ASRS_RECRUIT_PATH . 'assets/js/asrs-recruit.js';

		$css_ver = file_exists( $css_path ) ? (string) filemtime( $css_path ) : ASRS_RECRUIT_VERSION;
		$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : ASRS_RECRUIT_VERSION;

		wp_register_style(
			'asrs-recruit-frontend',
			ASRS_RECRUIT_URL . 'assets/css/asrs-recruit.css',
			array(),
			$css_ver
		);

		wp_register_script(
			'asrs-recruit-frontend',
			ASRS_RECRUIT_URL . 'assets/js/asrs-recruit.js',
			array(),
			$js_ver,
			true
		);

		wp_localize_script(
			'asrs-recruit-frontend',
			'asrsRecruitConfig',
			array(
				'ajaxUrl'      => admin_url( 'admin-ajax.php' ),
				'maxResumeMb'  => max( 1, (int) $this->settings->get( 'max_resume_mb', 5 ) ),
				'submitting'   => __( 'Submitting application...', 'asrs-recruit-engine' ),
				'submitLabel'  => __( 'Submit Application', 'asrs-recruit-engine' ),
				'fileTooLarge' => __( 'Selected file exceeds the maximum allowed size.', 'asrs-recruit-engine' ),
				'genericError' => __( 'Something went wrong. Please check your details and try again.', 'asrs-recruit-engine' ),
				'recaptcha'    => $this->recaptcha_config(),
			)
		);

		if ( $this->settings->is_turnstile_enabled() ) {
			wp_register_script(
				'cloudflare-turnstile',
				'https://challenges.cloudflare.com/turnstile/v0/api.js',
				array(),
				null,
				true
			);
		}

		if ( $this->settings->is_recaptcha_enabled() && ! wp_script_is( 'google-recaptcha', 'registered' ) ) {
			$site_key = trim( (string) $this->settings->get( 'recaptcha_site_key', '' ) );
			wp_register_script(
				'google-recaptcha',
				'v3' === $this->settings->get_recaptcha_version()
					? add_query_arg( 'render', rawurlencode( $site_key ), 'https://www.google.com/recaptcha/api.js' )
					: 'https://www.google.com/recaptcha/api.js?render=explicit',
				array(),
				null,
				true
			);
		}
	}

	/**
	 * Public (non-secret) reCAPTCHA settings for the frontend script.
	 *
	 * @return array<string, mixed>
	 */
	private function recaptcha_config(): array {
		if ( ! $this->settings->is_recaptcha_enabled() ) {
			return array( 'enabled' => false );
		}
		return array(
			'enabled' => true,
			'version' => $this->settings->get_recaptcha_version(),
			'siteKey' => trim( (string) $this->settings->get( 'recaptcha_site_key', '' ) ),
			'action'  => \ASRS\RecruitEngine\Security\SecurityManager::RECAPTCHA_ACTION,
		);
	}

	public function enqueue_frontend(): void {
		wp_enqueue_style( 'asrs-recruit-frontend' );
		wp_enqueue_script( 'asrs-recruit-frontend' );
		if ( $this->settings->is_turnstile_enabled() ) {
			wp_enqueue_script( 'cloudflare-turnstile' );
		}
		if ( $this->settings->is_recaptcha_enabled() ) {
			wp_enqueue_script( 'google-recaptcha' );
		}
	}

	public function enqueue_admin_assets( string $hook ): void {
		if ( false === strpos( $hook, 'asrs-recruit' ) ) {
			return;
		}

		$css_path = ASRS_RECRUIT_PATH . 'assets/css/asrs-recruit-admin.css';
		$js_path  = ASRS_RECRUIT_PATH . 'assets/js/asrs-recruit-admin.js';

		$css_ver = file_exists( $css_path ) ? (string) filemtime( $css_path ) : ASRS_RECRUIT_VERSION;
		$js_ver  = file_exists( $js_path ) ? (string) filemtime( $js_path ) : ASRS_RECRUIT_VERSION;

		wp_enqueue_style(
			'asrs-recruit-admin',
			ASRS_RECRUIT_URL . 'assets/css/asrs-recruit-admin.css',
			array(),
			$css_ver
		);

		wp_enqueue_script(
			'asrs-recruit-admin',
			ASRS_RECRUIT_URL . 'assets/js/asrs-recruit-admin.js',
			array( 'jquery' ),
			$js_ver,
			true
		);

		wp_localize_script(
			'asrs-recruit-admin',
			'asrsRecruitAdmin',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'asrs_recruit_admin_nonce' ),
				'i18n'    => array(
					'testing'      => __( 'Testing connection...', 'asrs-recruit-engine' ),
					'syncingJobs'  => __( 'Syncing job openings...', 'asrs-recruit-engine' ),
					'retrying'     => __( 'Retrying sync...', 'asrs-recruit-engine' ),
					'exchanging'   => __( 'Exchanging grant token...', 'asrs-recruit-engine' ),
					'confirmDel'   => __( 'Delete this application and its local resume file permanently?', 'asrs-recruit-engine' ),
				),
			)
		);
	}
}
