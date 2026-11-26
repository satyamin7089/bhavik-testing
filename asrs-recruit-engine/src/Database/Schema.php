<?php
/**
 * Database schema & table installer for ASRS Recruit Engine.
 *
 * @package ASRS\RecruitEngine\Database
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Database;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Schema {

	public static function table(): string {
		global $wpdb;
		return $wpdb->prefix . 'asrs_recruit_applications';
	}

	public static function create_tables(): void {
		global $wpdb;

		require_once ABSPATH . 'wp-admin/includes/upgrade.php';

		$charset = $wpdb->get_charset_collate();
		$table   = self::table();

		$sql = "CREATE TABLE {$table} (
			id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
			application_uuid char(36) NOT NULL,
			job_id varchar(100) NOT NULL,
			job_title varchar(255) NOT NULL,
			first_name varchar(120) NOT NULL,
			last_name varchar(120) NOT NULL,
			email varchar(190) NOT NULL,
			phone varchar(50) NOT NULL,
			city varchar(120) DEFAULT '',
			experience_years varchar(40) DEFAULT '',
			linkedin_url varchar(300) DEFAULT '',
			cover_note text DEFAULT NULL,
			resume_path varchar(500) DEFAULT '',
			resume_name varchar(255) DEFAULT '',
			sync_status varchar(20) NOT NULL DEFAULT 'pending',
			zoho_candidate_id varchar(100) DEFAULT NULL,
			zoho_candidate_url varchar(500) DEFAULT NULL,
			resume_uploaded tinyint(1) NOT NULL DEFAULT 0,
			job_associated tinyint(1) NOT NULL DEFAULT 0,
			sync_attempts int(10) unsigned NOT NULL DEFAULT 0,
			last_sync_error text DEFAULT NULL,
			last_sync_at datetime DEFAULT NULL,
			next_retry_at datetime DEFAULT NULL,
			created_at datetime NOT NULL,
			updated_at datetime NOT NULL,
			PRIMARY KEY  (id),
			UNIQUE KEY application_uuid (application_uuid),
			KEY job_id (job_id),
			KEY sync_status (sync_status),
			KEY next_retry_at (next_retry_at),
			KEY created_at (created_at)
		) {$charset};";

		dbDelta( $sql );
		update_option( 'asrs_recruit_db_version', ASRS_RECRUIT_DB_VERSION, false );
	}
}
