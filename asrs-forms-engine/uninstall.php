<?php
/**
 * Uninstall handler for ASRS Forms Engine.
 *
 * @package ASRS\FormsEngine
 */

declare(strict_types=1);

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$settings = get_option( 'asrs_forms_settings', array() );

if ( empty( $settings['delete_data_on_uninstall'] ) ) {
	return;
}

global $wpdb;

$tables = array(
	$wpdb->prefix . 'asrs_submissions',
	$wpdb->prefix . 'asrs_form_mappings',
	$wpdb->prefix . 'asrs_drafts',
	$wpdb->prefix . 'asrs_api_logs',
);

foreach ( $tables as $table ) {
	// phpcs:ignore WordPress.DB.PreparedSQL.InterpolatedNotPrepared, WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.DirectDatabaseQuery.SchemaChange
	$wpdb->query( "DROP TABLE IF EXISTS `{$table}`" );
}

$options = array(
	'asrs_forms_settings',
	'asrs_forms_zoho_settings',
	'asrs_forms_zoho_org_info',
	'asrs_forms_zoho_modules',
	'asrs_forms_zoho_fields_cache',
	'asrs_forms_zoho_api_domain',
	'asrs_forms_db_version',
);

foreach ( $options as $option ) {
	delete_option( $option );
}

delete_transient( 'asrs_forms_zoho_access_token' );

$role = get_role( 'administrator' );
if ( $role ) {
	$role->remove_cap( 'asrs_manage_forms' );
}
