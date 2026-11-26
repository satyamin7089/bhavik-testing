<?php
/**
 * Plugin Name:       ASRS Recruit Engine
 * Plugin URI:        https://github.com/asrs/asrs-recruit-engine
 * Description:       White-label WordPress Careers Portal & Application Engine synced natively with Zoho Recruit API v2 (Zero Zoho Branding).
 * Version:           1.0.1
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author:            ASRS Engineering
 * License:           GPL-2.0-or-later
 * Text Domain:       asrs-recruit-engine
 *
 * @package ASRS\RecruitEngine
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASRS_RECRUIT_VERSION', '1.0.1' );
define( 'ASRS_RECRUIT_DB_VERSION', '1.0.0' );
define( 'ASRS_RECRUIT_FILE', __FILE__ );
define( 'ASRS_RECRUIT_PATH', plugin_dir_path( __FILE__ ) );
define( 'ASRS_RECRUIT_URL', plugin_dir_url( __FILE__ ) );
define( 'ASRS_RECRUIT_BASENAME', plugin_basename( __FILE__ ) );

spl_autoload_register(
	static function ( string $class ): void {
		$prefix   = 'ASRS\\RecruitEngine\\';
		$base_dir = ASRS_RECRUIT_PATH . 'src/';

		$len = strlen( $prefix );
		if ( strncmp( $prefix, $class, $len ) !== 0 ) {
			return;
		}

		$relative_class = substr( $class, $len );
		$file           = $base_dir . str_replace( '\\', '/', $relative_class ) . '.php';

		if ( file_exists( $file ) ) {
			require_once $file;
		}
	}
);

register_activation_hook(
	__FILE__,
	static function (): void {
		\ASRS\RecruitEngine\Plugin::activate();
	}
);

register_deactivation_hook(
	__FILE__,
	static function (): void {
		\ASRS\RecruitEngine\Plugin::deactivate();
	}
);

add_action(
	'plugins_loaded',
	static function (): void {
		\ASRS\RecruitEngine\Plugin::instance()->boot();
	},
	10
);
