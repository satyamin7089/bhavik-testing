<?php
/**
 * Admin CSS/JS assets registration and localization.
 *
 * @package ASRS\FormsEngine\Admin
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Admin;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminAssets {

	/**
	 * Register admin enqueue hook.
	 */
	public function init(): void {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue' ) );
	}

	/**
	 * Enqueue assets only on ASRS Forms admin pages.
	 *
	 * @param string $hook_suffix Current admin screen hook suffix.
	 */
	public function enqueue( string $hook_suffix ): void {
		if ( ! str_contains( $hook_suffix, 'asrs-forms' ) ) {
			return;
		}

		$css_file = ASRS_FORMS_PATH . 'assets/css/asrs-admin.css';
		$js_file  = ASRS_FORMS_PATH . 'assets/js/asrs-admin.js';

		wp_enqueue_style(
			'asrs-forms-admin',
			ASRS_FORMS_URL . 'assets/css/asrs-admin.css',
			array(),
			file_exists( $css_file ) ? (string) filemtime( $css_file ) : ASRS_FORMS_VERSION
		);

		wp_enqueue_script(
			'asrs-forms-admin',
			ASRS_FORMS_URL . 'assets/js/asrs-admin.js',
			array( 'jquery' ),
			file_exists( $js_file ) ? (string) filemtime( $js_file ) : ASRS_FORMS_VERSION,
			true
		);

		wp_localize_script(
			'asrs-forms-admin',
			'ASRSAdminConfig',
			array(
				'ajaxUrl' => admin_url( 'admin-ajax.php' ),
				'nonce'   => wp_create_nonce( 'asrs_forms_admin_nonce' ),
				'i18n'    => array(
					'copied'         => __( 'Copied!', 'asrs-forms-engine' ),
					'testing'        => __( 'Testing connection...', 'asrs-forms-engine' ),
					'syncingModules' => __( 'Syncing Zoho modules...', 'asrs-forms-engine' ),
					'loadingFields'  => __( 'Loading module fields...', 'asrs-forms-engine' ),
					'creatingRecord' => __( 'Creating test record...', 'asrs-forms-engine' ),
					'retrying'       => __( 'Retrying sync...', 'asrs-forms-engine' ),
					'confirmClear'   => __( 'Clear all API logs? This cannot be undone.', 'asrs-forms-engine' ),
					'errorGeneric'   => __( 'Request failed. Check API Logs for details.', 'asrs-forms-engine' ),
				),
			)
		);
	}
}
