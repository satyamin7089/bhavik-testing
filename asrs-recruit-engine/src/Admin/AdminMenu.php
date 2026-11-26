<?php
/**
 * WordPress Admin Menu & Protected Resume Download Endpoint.
 *
 * @package ASRS\RecruitEngine\Admin
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Admin;

use ASRS\RecruitEngine\Admin\Pages\ApplicationsPage;
use ASRS\RecruitEngine\Admin\Pages\JobOpeningsPage;
use ASRS\RecruitEngine\Admin\Pages\SettingsPage;
use ASRS\RecruitEngine\Repositories\ApplicationRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class AdminMenu {

	private ApplicationsPage $applications_page;
	private JobOpeningsPage $jobs_page;
	private SettingsPage $settings_page;
	private ApplicationRepository $applications;

	public function __construct(
		ApplicationsPage $applications_page,
		JobOpeningsPage $jobs_page,
		SettingsPage $settings_page,
		ApplicationRepository $applications
	) {
		$this->applications_page = $applications_page;
		$this->jobs_page         = $jobs_page;
		$this->settings_page     = $settings_page;
		$this->applications      = $applications;
	}

	public function register(): void {
		add_action( 'admin_menu', array( $this, 'register_menu' ) );
		add_action( 'admin_post_asrs_recruit_download_resume', array( $this, 'handle_resume_download' ) );
	}

	public function register_menu(): void {
		add_menu_page(
			__( 'ASRS Careers', 'asrs-recruit-engine' ),
			__( 'ASRS Careers', 'asrs-recruit-engine' ),
			'manage_options',
			'asrs-recruit-applications',
			array( $this->applications_page, 'render' ),
			'dashicons-businessperson',
			29
		);

		add_submenu_page(
			'asrs-recruit-applications',
			__( 'Applications', 'asrs-recruit-engine' ),
			__( 'Applications', 'asrs-recruit-engine' ),
			'manage_options',
			'asrs-recruit-applications',
			array( $this->applications_page, 'render' )
		);

		add_submenu_page(
			'asrs-recruit-applications',
			__( 'Job Openings', 'asrs-recruit-engine' ),
			__( 'Job Openings', 'asrs-recruit-engine' ),
			'manage_options',
			'asrs-recruit-jobs',
			array( $this->jobs_page, 'render' )
		);

		add_submenu_page(
			'asrs-recruit-applications',
			__( 'Zoho Recruit & Settings', 'asrs-recruit-engine' ),
			__( 'Zoho Recruit & Settings', 'asrs-recruit-engine' ),
			'manage_options',
			'asrs-recruit-settings',
			array( $this->settings_page, 'render' )
		);
	}

	public function handle_resume_download(): void {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( esc_html__( 'Unauthorized.', 'asrs-recruit-engine' ), 403 );
		}

		$app_id = isset( $_GET['app_id'] ) ? (int) $_GET['app_id'] : 0; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		check_admin_referer( 'asrs_download_resume_' . $app_id );

		$app = $this->applications->find( $app_id );
		if ( ! $app || empty( $app['resume_path'] ) || ! file_exists( (string) $app['resume_path'] ) ) {
			wp_die( esc_html__( 'Resume file not found.', 'asrs-recruit-engine' ), 404 );
		}

		$path = (string) $app['resume_path'];
		$name = sanitize_file_name( (string) ( $app['resume_name'] ?: basename( $path ) ) );

		header( 'Content-Description: File Transfer' );
		header( 'Content-Type: application/octet-stream' );
		header( 'Content-Disposition: attachment; filename="' . $name . '"' );
		header( 'Content-Length: ' . (string) filesize( $path ) );
		readfile( $path ); // phpcs:ignore WordPress.WP.AlternativeFunctions.file_system_operations_readfile
		exit;
	}
}
