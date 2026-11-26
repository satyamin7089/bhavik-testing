<?php
/**
 * Database migration runner.
 *
 * @package ASRS\FormsEngine\Database
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Migrator {

	/**
	 * Ensure database schema is up to date.
	 */
	public static function maybe_migrate(): void {
		$installed = (string) get_option( 'asrs_forms_db_version', '' );
		if ( version_compare( $installed, ASRS_FORMS_DB_VERSION, '<' ) ) {
			Schema::create_tables();
		}
	}
}
