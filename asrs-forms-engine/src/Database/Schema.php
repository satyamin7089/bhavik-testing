<?php
/**
 * Database schema definitions using dbDelta.
 *
 * @package ASRS\FormsEngine\Database
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Schema {

	/**
	 * Get full table name for a logical entity.
	 *
	 * @param string $name Short name: submissions, form_mappings.
	 */
	public static function table( string $name ): string {
		global $wpdb;
		return $wpdb->prefix . 'asrs_' . $name;
	}

	/**
	 * Create or upgrade plugin database tables.
	 */
	public static function create_tables(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset_collate     = $wpdb->get_charset_collate();
		$submissions_table   = self::table( 'submissions' );
		$form_mappings_table = self::table( 'form_mappings' );

		$sql_submissions = "CREATE TABLE {$submissions_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			submission_uuid char(36) NOT NULL,
			form_id varchar(100) NOT NULL,
			data_json longtext NOT NULL,
			meta_json longtext DEFAULT NULL,
			sync_status varchar(20) NOT NULL DEFAULT 'pending',
			zoho_module varchar(100) DEFAULT NULL,
			zoho_record_id varchar(100) DEFAULT NULL,
			zoho_record_url varchar(500) DEFAULT NULL,
			zoho_response_json longtext DEFAULT NULL,
			sync_attempts int(10) unsigned NOT NULL DEFAULT 0,
			last_sync_error text DEFAULT NULL,
			last_sync_at datetime DEFAULT NULL,
			next_retry_at datetime DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY submission_uuid (submission_uuid),
			KEY form_id (form_id),
			KEY sync_status (sync_status),
			KEY next_retry_at (next_retry_at),
			KEY created_at (created_at),
			KEY zoho_record_id (zoho_record_id)
		) {$charset_collate};";

		$sql_form_mappings = "CREATE TABLE {$form_mappings_table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			form_id varchar(100) NOT NULL,
			zoho_enabled tinyint(1) NOT NULL DEFAULT 1,
			zoho_module varchar(100) NOT NULL DEFAULT 'Leads',
			field_map_json longtext NOT NULL,
			static_values_json longtext DEFAULT NULL,
			trigger_workflow tinyint(1) NOT NULL DEFAULT 1,
			updated_by bigint(20) unsigned DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY form_id (form_id)
		) {$charset_collate};";

		dbDelta( $sql_submissions );
		dbDelta( $sql_form_mappings );

		update_option( 'asrs_forms_db_version', ASRS_FORMS_DB_VERSION, false );
	}
}
