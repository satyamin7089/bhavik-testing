<?php
/**
 * Uninstall cleanup for ASRS Recruit Engine.
 *
 * @package ASRS\RecruitEngine
 */

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'asrs_recruit_settings', array() );
if ( ! is_array( $settings ) || empty( $settings['delete_data_on_uninstall'] ) ) {
	return;
}

global $wpdb;
$table = $wpdb->prefix . 'asrs_recruit_applications';
// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.SchemaChange, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );

delete_option( 'asrs_recruit_settings' );
delete_option( 'asrs_recruit_zoho_settings' );
delete_option( 'asrs_recruit_jobs_cache' );
delete_option( 'asrs_recruit_org_id' );
delete_option( 'asrs_recruit_db_version' );
delete_transient( 'asrs_recruit_access_token' );
