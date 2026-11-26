<?php
/**
 * Main Plugin Container & Bootstrapper.
 *
 * @package ASRS\RecruitEngine
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine;

use ASRS\RecruitEngine\Admin\AdminMenu;
use ASRS\RecruitEngine\Admin\Pages\ApplicationsPage;
use ASRS\RecruitEngine\Admin\Pages\JobOpeningsPage;
use ASRS\RecruitEngine\Admin\Pages\SettingsPage;
use ASRS\RecruitEngine\Ajax\AdminAjaxHandler;
use ASRS\RecruitEngine\Ajax\FrontendAjaxHandler;
use ASRS\RecruitEngine\Core\Settings;
use ASRS\RecruitEngine\Cron\CronManager;
use ASRS\RecruitEngine\Database\Schema;
use ASRS\RecruitEngine\Frontend\AssetsManager;
use ASRS\RecruitEngine\Frontend\CareersRenderer;
use ASRS\RecruitEngine\Repositories\ApplicationRepository;
use ASRS\RecruitEngine\Security\ResumeUploader;
use ASRS\RecruitEngine\Security\SecurityManager;
use ASRS\RecruitEngine\Services\ApplicationService;
use ASRS\RecruitEngine\Services\NotificationService;
use ASRS\RecruitEngine\Shortcode\ShortcodeManager;
use ASRS\RecruitEngine\Zoho\RecruitAuthService;
use ASRS\RecruitEngine\Zoho\RecruitClient;
use ASRS\RecruitEngine\Zoho\RecruitService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	private static ?self $instance = null;

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	private function __construct() {}

	public function boot(): void {
		$settings      = new Settings();
		$applications  = new ApplicationRepository();
		$auth          = new RecruitAuthService( $settings );
		$client        = new RecruitClient( $auth );
		$recruit       = new RecruitService( $client, $auth, $settings, $applications );
		$uploader      = new ResumeUploader( $settings );
		$security      = new SecurityManager( $settings );
		$notifications = new NotificationService( $settings );
		$app_service   = new ApplicationService( $security, $uploader, $applications, $recruit, $notifications );
		$assets        = new AssetsManager( $settings );
		$renderer      = new CareersRenderer( $recruit, $settings, $assets );
		$shortcodes    = new ShortcodeManager( $renderer, $recruit );
		$front_ajax    = new FrontendAjaxHandler( $app_service );
		$admin_ajax    = new AdminAjaxHandler( $auth, $recruit, $applications );
		$cron          = new CronManager( $applications, $recruit, $auth, $settings );

		$admin_menu = new AdminMenu(
			new ApplicationsPage( $applications, $settings ),
			new JobOpeningsPage( $recruit ),
			new SettingsPage( $settings, $auth ),
			$applications
		);

		$assets->register();
		$shortcodes->register();
		$front_ajax->register();
		$admin_ajax->register();
		$cron->register();
		$admin_menu->register();
	}

	public static function activate(): void {
		Schema::create_tables();
		CronManager::schedule_events();
	}

	public static function deactivate(): void {
		CronManager::clear_events();
	}
}
