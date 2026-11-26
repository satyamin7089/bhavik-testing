<?php
/**
 * Frontend CSS/JS asset registration and localization.
 *
 * @package ASRS\FormsEngine\Frontend
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Frontend;

use ASRS\FormsEngine\Security\TurnstileVerifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AssetsManager {

	private TurnstileVerifier $turnstile;
	private bool $enqueued = false;

	public function __construct( TurnstileVerifier $turnstile ) {
		$this->turnstile = $turnstile;
	}

	public function init(): void {
		add_action( 'wp_enqueue_scripts', array( $this, 'register_assets' ) );
	}

	public function register_assets(): void {
		$css_file = ASRS_FORMS_PATH . 'assets/css/asrs-forms.css';
		$js_file  = ASRS_FORMS_PATH . 'assets/js/asrs-forms.js';
		$css_ver  = file_exists( $css_file ) ? (string) filemtime( $css_file ) : ASRS_FORMS_VERSION;
		$js_ver   = file_exists( $js_file ) ? (string) filemtime( $js_file ) : ASRS_FORMS_VERSION;

		wp_register_style(
			'asrs-forms-frontend',
			ASRS_FORMS_URL . 'assets/css/asrs-forms.css',
			array(),
			$css_ver
		);

		wp_register_script(
			'asrs-forms-frontend',
			ASRS_FORMS_URL . 'assets/js/asrs-forms.js',
			array(),
			$js_ver,
			true
		);

		if ( $this->turnstile->is_enabled() ) {
			wp_register_script(
				'cloudflare-turnstile',
				'https://challenges.cloudflare.com/turnstile/v0/api.js',
				array(),
				null,
				true
			);
		}
	}

	public function enqueue_on_demand(): void {
		if ( $this->enqueued ) {
			return;
		}
		$this->enqueued = true;

		wp_enqueue_style( 'asrs-forms-frontend' );
		wp_enqueue_script( 'asrs-forms-frontend' );

		if ( $this->turnstile->is_enabled() ) {
			wp_enqueue_script( 'cloudflare-turnstile' );
		}

		wp_localize_script(
			'asrs-forms-frontend',
			'ASRSFormsConfig',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'asrs_forms_frontend_nonce' ),
				'i18n'    => array(
					'genericError' => __( 'An unexpected error occurred. Please try again.', 'asrs-forms-engine' ),
				),
			)
		);
	}
}
