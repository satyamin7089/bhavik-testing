<?php
/**
 * WordPress Admin menu registration and early action dispatcher.
 *
 * @package ASRS\FormsEngine\Admin
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Admin;

use ASRS\FormsEngine\Admin\Pages\EntriesPage;
use ASRS\FormsEngine\Admin\Pages\FormsPage;
use ASRS\FormsEngine\Admin\Pages\SettingsPage;
use ASRS\FormsEngine\Admin\Pages\ZohoIntegrationPage;
use ASRS\FormsEngine\Core\Capabilities;
use ASRS\FormsEngine\Core\Container;
use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Repositories\SubmissionRepository;
use ASRS\FormsEngine\Services\ExportService;
use ASRS\FormsEngine\Services\RetryService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminMenu {

	private Container $container;

	public function __construct( Container $container ) {
		$this->container = $container;
	}

	public function init(): void {
		add_action( 'admin_menu', array( $this, 'register_menus' ) );
		add_action( 'admin_init', array( $this, 'handle_early_actions' ) );
	}

	/**
	 * Register the lean 4-screen ASRS Forms admin menu.
	 */
	public function register_menus(): void {
		$cap = Capabilities::required_cap();

		add_menu_page(
			__( 'ASRS Forms Engine', 'asrs-forms-engine' ),
			__( 'ASRS Forms', 'asrs-forms-engine' ),
			$cap,
			'asrs-forms',
			fn() => $this->container->get( EntriesPage::class )->render(),
			'dashicons-feedback',
			28
		);

		$submenus = array(
			array( 'asrs-forms', __( 'Entries', 'asrs-forms-engine' ), EntriesPage::class ),
			array( 'asrs-forms-list', __( 'Forms', 'asrs-forms-engine' ), FormsPage::class ),
			array( 'asrs-forms-zoho', __( 'Zoho CRM', 'asrs-forms-engine' ), ZohoIntegrationPage::class ),
			array( 'asrs-forms-settings', __( 'Settings', 'asrs-forms-engine' ), SettingsPage::class ),
		);

		foreach ( $submenus as $item ) {
			[ $slug, $label, $page_class ] = $item;
			add_submenu_page(
				'asrs-forms',
				$label . ' — ' . __( 'ASRS Forms', 'asrs-forms-engine' ),
				$label,
				$cap,
				$slug,
				fn() => $this->container->get( $page_class )->render()
			);
		}
	}

	/**
	 * Handle CSV downloads and POST saves before headers are sent.
	 */
	public function handle_early_actions(): void {
		if ( ! is_admin() || ! Capabilities::current_user_can_manage() ) {
			return;
		}

		// 1. CSV Export.
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['page'], $_GET['asrs_action'] ) && 'asrs-forms' === $_GET['page'] && 'export_csv' === $_GET['asrs_action'] ) {
			check_admin_referer( 'asrs_export_csv' );
			/** @var ExportService $exporter */
			$exporter = $this->container->get( ExportService::class );
			$exporter->stream_submissions_csv(
				array(
					'form_id'     => isset( $_GET['form_id'] ) ? sanitize_key( wp_unslash( (string) $_GET['form_id'] ) ) : '',
					'sync_status' => isset( $_GET['sync_status'] ) ? sanitize_key( wp_unslash( (string) $_GET['sync_status'] ) ) : '',
					'search'      => isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['s'] ) ) : '',
					'date_from'   => isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['date_from'] ) ) : '',
					'date_to'     => isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['date_to'] ) ) : '',
				)
			);
		}

		// 2. Save General & Email Settings.
		if ( isset( $_POST['asrs_save_general_settings'] ) ) {
			check_admin_referer( 'asrs_save_general_settings_nonce' );
			/** @var Settings $settings */
			$settings = $this->container->get( Settings::class );
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$settings->save_general( wp_unslash( $_POST ) );
			wp_safe_redirect( add_query_arg( array( 'page' => 'asrs-forms-settings', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		// 3. Save Zoho CRM Settings.
		if ( isset( $_POST['asrs_save_zoho_settings'] ) ) {
			check_admin_referer( 'asrs_save_zoho_settings_nonce' );
			/** @var Settings $settings */
			$settings = $this->container->get( Settings::class );
			// phpcs:ignore WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
			$settings->save_zoho_settings( wp_unslash( $_POST ) );
			wp_safe_redirect( add_query_arg( array( 'page' => 'asrs-forms-zoho', 'updated' => '1' ), admin_url( 'admin.php' ) ) );
			exit;
		}

		// 4. Entries Bulk Actions (Retry / Delete).
		if ( isset( $_POST['asrs_entries_bulk_submit'] ) ) {
			check_admin_referer( 'asrs_entries_bulk_nonce' );
			$action = isset( $_POST['bulk_action'] ) ? sanitize_key( wp_unslash( (string) $_POST['bulk_action'] ) ) : '';
			$ids    = isset( $_POST['entry_ids'] ) && is_array( $_POST['entry_ids'] )
				? array_map( 'intval', $_POST['entry_ids'] )
				: array();

			if ( ! empty( $ids ) ) {
				if ( 'retry' === $action ) {
					/** @var RetryService $retry */
					$retry = $this->container->get( RetryService::class );
					$res   = $retry->retry_many( $ids );
					wp_safe_redirect(
						add_query_arg(
							array(
								'page'      => 'asrs-forms',
								'retried'   => $res['total'],
								'succeeded' => $res['succeeded'],
							),
							admin_url( 'admin.php' )
						)
					);
					exit;
				}

				if ( 'delete' === $action ) {
					/** @var SubmissionRepository $repo */
					$repo = $this->container->get( SubmissionRepository::class );
					foreach ( $ids as $id ) {
						$repo->delete( $id );
					}
					wp_safe_redirect(
						add_query_arg(
							array(
								'page'    => 'asrs-forms',
								'deleted' => count( $ids ),
							),
							admin_url( 'admin.php' )
						)
					);
					exit;
				}
			}
		}
	}
}
