<?php
/**
 * Main Plugin Bootstrap & Service Container Orchestrator.
 *
 * @package ASRS\FormsEngine
 */

declare(strict_types=1);

namespace ASRS\FormsEngine;

use ASRS\FormsEngine\Admin\AdminAssets;
use ASRS\FormsEngine\Admin\AdminMenu;
use ASRS\FormsEngine\Admin\Pages\EntriesPage;
use ASRS\FormsEngine\Admin\Pages\FormsPage;
use ASRS\FormsEngine\Admin\Pages\SettingsPage;
use ASRS\FormsEngine\Admin\Pages\ZohoIntegrationPage;
use ASRS\FormsEngine\Ajax\AdminAjaxHandler;
use ASRS\FormsEngine\Ajax\FrontendAjaxHandler;
use ASRS\FormsEngine\Core\Capabilities;
use ASRS\FormsEngine\Core\Container;
use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Cron\CronManager;
use ASRS\FormsEngine\Database\Migrator;
use ASRS\FormsEngine\Database\Schema;
use ASRS\FormsEngine\Forms\FormRegistry;
use ASRS\FormsEngine\Forms\FormValidator;
use ASRS\FormsEngine\Frontend\AssetsManager;
use ASRS\FormsEngine\Frontend\FormRenderer;
use ASRS\FormsEngine\Repositories\FormMappingRepository;
use ASRS\FormsEngine\Repositories\SubmissionRepository;
use ASRS\FormsEngine\Security\ReCaptchaVerifier;
use ASRS\FormsEngine\Security\SecurityManager;
use ASRS\FormsEngine\Security\TurnstileVerifier;
use ASRS\FormsEngine\Services\ExportService;
use ASRS\FormsEngine\Services\Integrations\ZohoCrmIntegration;
use ASRS\FormsEngine\Services\NotificationService;
use ASRS\FormsEngine\Services\RetryService;
use ASRS\FormsEngine\Services\SubmissionService;
use ASRS\FormsEngine\Services\SyncService;
use ASRS\FormsEngine\Services\Zoho\ZohoAuthService;
use ASRS\FormsEngine\Services\Zoho\ZohoClient;
use ASRS\FormsEngine\Services\Zoho\ZohoCrmService;
use ASRS\FormsEngine\Shortcode\ShortcodeManager;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

final class Plugin {

	private static ?self $instance = null;
	private Container $container;
	private bool $booted = false;

	private function __construct() {
		$this->container = new Container();
		$this->register_services();
	}

	public static function instance(): self {
		if ( null === self::$instance ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	public function container(): Container {
		return $this->container;
	}

	public static function activate(): void {
		Schema::create_tables();
		Capabilities::install();
		CronManager::schedule_events();
	}

	public static function deactivate(): void {
		CronManager::unschedule_events();
	}

	public function boot(): void {
		if ( $this->booted ) {
			return;
		}
		$this->booted = true;

		load_plugin_textdomain( 'asrs-forms-engine', false, dirname( ASRS_FORMS_BASENAME ) . '/languages' );

		Migrator::maybe_migrate();

		add_action(
			'init',
			function (): void {
				/** @var FormRegistry $registry */
				$registry = $this->container->get( FormRegistry::class );
				$registry->init();

				/** @var ShortcodeManager $shortcodes */
				$shortcodes = $this->container->get( ShortcodeManager::class );
				$shortcodes->register_shortcodes();
			},
			20
		);

		$this->container->get( AssetsManager::class )->init();
		$this->container->get( FrontendAjaxHandler::class )->register();
		$this->container->get( CronManager::class )->init();

		if ( is_admin() ) {
			$this->container->get( AdminAjaxHandler::class )->register();
			$this->container->get( AdminAssets::class )->init();
			$this->container->get( AdminMenu::class )->init();
		}

		do_action( 'asrs_forms_loaded', $this );
	}

	private function register_services(): void {
		$c = $this->container;

		// Core & Security.
		$c->singleton( Settings::class, static fn(): Settings => new Settings() );
		$c->singleton(
			TurnstileVerifier::class,
			static fn( Container $c ): TurnstileVerifier => new TurnstileVerifier( $c->get( Settings::class ) )
		);
		$c->singleton(
			ReCaptchaVerifier::class,
			static fn( Container $c ): ReCaptchaVerifier => new ReCaptchaVerifier( $c->get( Settings::class ) )
		);
		$c->singleton(
			SecurityManager::class,
			static fn( Container $c ): SecurityManager => new SecurityManager(
				$c->get( Settings::class ),
				$c->get( TurnstileVerifier::class ),
				$c->get( ReCaptchaVerifier::class )
			)
		);

		// Repositories.
		$c->singleton( SubmissionRepository::class, static fn(): SubmissionRepository => new SubmissionRepository() );
		$c->singleton( FormMappingRepository::class, static fn(): FormMappingRepository => new FormMappingRepository() );

		// Forms Domain.
		$c->singleton( FormRegistry::class, static fn(): FormRegistry => new FormRegistry() );
		$c->singleton( FormValidator::class, static fn(): FormValidator => new FormValidator() );

		// Zoho CRM API v7 Layer.
		$c->singleton(
			ZohoAuthService::class,
			static fn( Container $c ): ZohoAuthService => new ZohoAuthService( $c->get( Settings::class ) )
		);
		$c->singleton(
			ZohoClient::class,
			static fn( Container $c ): ZohoClient => new ZohoClient( $c->get( ZohoAuthService::class ) )
		);
		$c->singleton(
			ZohoCrmService::class,
			static fn( Container $c ): ZohoCrmService => new ZohoCrmService(
				$c->get( ZohoClient::class ),
				$c->get( ZohoAuthService::class ),
				$c->get( Settings::class )
			)
		);
		$c->singleton(
			ZohoCrmIntegration::class,
			static fn( Container $c ): ZohoCrmIntegration => new ZohoCrmIntegration(
				$c->get( Settings::class ),
				$c->get( ZohoCrmService::class ),
				$c->get( FormMappingRepository::class )
			)
		);

		// Application Services.
		$c->singleton(
			NotificationService::class,
			static fn( Container $c ): NotificationService => new NotificationService( $c->get( Settings::class ) )
		);
		$c->singleton(
			SyncService::class,
			static fn( Container $c ): SyncService => new SyncService(
				$c->get( SubmissionRepository::class ),
				$c->get( FormRegistry::class ),
				$c->get( FormMappingRepository::class ),
				$c->get( ZohoCrmIntegration::class ),
				$c->get( Settings::class ),
				$c->get( NotificationService::class )
			)
		);
		$c->singleton(
			RetryService::class,
			static fn( Container $c ): RetryService => new RetryService(
				$c->get( SubmissionRepository::class ),
				$c->get( SyncService::class ),
				$c->get( Settings::class )
			)
		);
		$c->singleton(
			SubmissionService::class,
			static fn( Container $c ): SubmissionService => new SubmissionService(
				$c->get( FormRegistry::class ),
				$c->get( FormValidator::class ),
				$c->get( SecurityManager::class ),
				$c->get( SubmissionRepository::class ),
				$c->get( SyncService::class ),
				$c->get( FormMappingRepository::class ),
				$c->get( Settings::class )
			)
		);
		$c->singleton(
			ExportService::class,
			static fn( Container $c ): ExportService => new ExportService(
				$c->get( SubmissionRepository::class ),
				$c->get( FormRegistry::class )
			)
		);

		// Frontend & Shortcodes.
		$c->singleton(
			AssetsManager::class,
			static fn( Container $c ): AssetsManager => new AssetsManager(
				$c->get( TurnstileVerifier::class ),
				$c->get( ReCaptchaVerifier::class )
			)
		);
		$c->singleton(
			FormRenderer::class,
			static fn( Container $c ): FormRenderer => new FormRenderer(
				$c->get( AssetsManager::class ),
				$c->get( TurnstileVerifier::class ),
				$c->get( ReCaptchaVerifier::class ),
				$c->get( Settings::class )
			)
		);
		$c->singleton(
			ShortcodeManager::class,
			static fn( Container $c ): ShortcodeManager => new ShortcodeManager(
				$c->get( FormRegistry::class ),
				$c->get( FormRenderer::class )
			)
		);

		// AJAX & Cron.
		$c->singleton(
			FrontendAjaxHandler::class,
			static fn( Container $c ): FrontendAjaxHandler => new FrontendAjaxHandler( $c->get( SubmissionService::class ) )
		);
		$c->singleton(
			AdminAjaxHandler::class,
			static fn( Container $c ): AdminAjaxHandler => new AdminAjaxHandler(
				$c->get( SecurityManager::class ),
				$c->get( ZohoCrmService::class ),
				$c->get( ZohoAuthService::class ),
				$c->get( FormMappingRepository::class ),
				$c->get( RetryService::class )
			)
		);
		$c->singleton(
			CronManager::class,
			static fn( Container $c ): CronManager => new CronManager(
				$c->get( RetryService::class ),
				$c->get( Settings::class )
			)
		);

		// Admin UI (4 clean screens).
		$c->singleton( AdminAssets::class, static fn(): AdminAssets => new AdminAssets() );
		$c->singleton( AdminMenu::class, static fn( Container $c ): AdminMenu => new AdminMenu( $c ) );
		$c->singleton(
			EntriesPage::class,
			static fn( Container $c ): EntriesPage => new EntriesPage(
				$c->get( SubmissionRepository::class ),
				$c->get( FormRegistry::class )
			)
		);
		$c->singleton(
			FormsPage::class,
			static fn( Container $c ): FormsPage => new FormsPage(
				$c->get( FormRegistry::class ),
				$c->get( SubmissionRepository::class ),
				$c->get( FormMappingRepository::class ),
				$c->get( ZohoCrmService::class ),
				$c->get( ReCaptchaVerifier::class ),
				$c->get( TurnstileVerifier::class ),
				$c->get( Settings::class )
			)
		);
		$c->singleton(
			ZohoIntegrationPage::class,
			static fn( Container $c ): ZohoIntegrationPage => new ZohoIntegrationPage(
				$c->get( Settings::class ),
				$c->get( ZohoAuthService::class ),
				$c->get( ZohoCrmService::class )
			)
		);
		$c->singleton(
			SettingsPage::class,
			static fn( Container $c ): SettingsPage => new SettingsPage( $c->get( Settings::class ) )
		);
	}
}
