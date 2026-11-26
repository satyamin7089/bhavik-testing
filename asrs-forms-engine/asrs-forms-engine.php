<?php
/**
 * Plugin Name:       ASRS Forms Engine
 * Plugin URI:        https://github.com/asrs/asrs-forms-engine
 * Description:       Developer-first WordPress form engine with drop-in PHP forms, AJAX lead capture, and Zoho CRM API v7 synchronization.
 * Version:           1.2.0
 * Requires at least: 6.2
 * Requires PHP:      8.0
 * Author:            ASRS Engineering
 * Author URI:        https://asrs.example.com
 * License:           GPL-2.0-or-later
 * License URI:       https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain:       asrs-forms-engine
 * Domain Path:       /languages
 *
 * @package ASRS\FormsEngine
 */

declare(strict_types=1);

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'ASRS_FORMS_VERSION', '1.2.0' );
define( 'ASRS_FORMS_DB_VERSION', '1.0.0' );
define( 'ASRS_FORMS_FILE', __FILE__ );
define( 'ASRS_FORMS_PATH', plugin_dir_path( __FILE__ ) );
define( 'ASRS_FORMS_URL', plugin_dir_url( __FILE__ ) );
define( 'ASRS_FORMS_BASENAME', plugin_basename( __FILE__ ) );

// Load Composer autoloader if present, otherwise use built-in PSR-4 autoloader.
$asrs_composer_autoload = ASRS_FORMS_PATH . 'vendor/autoload.php';
if ( file_exists( $asrs_composer_autoload ) ) {
	require_once $asrs_composer_autoload;
} else {
	spl_autoload_register(
		static function ( string $class ): void {
			$prefix   = 'ASRS\\FormsEngine\\';
			$base_dir = ASRS_FORMS_PATH . 'src/';

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
}

register_activation_hook(
	__FILE__,
	static function (): void {
		\ASRS\FormsEngine\Plugin::activate();
	}
);

register_deactivation_hook(
	__FILE__,
	static function (): void {
		\ASRS\FormsEngine\Plugin::deactivate();
	}
);

add_action(
	'plugins_loaded',
	static function (): void {
		\ASRS\FormsEngine\Plugin::instance()->boot();
	},
	10
);
