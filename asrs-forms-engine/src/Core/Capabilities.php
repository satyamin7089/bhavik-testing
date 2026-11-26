<?php
/**
 * Capability management for ASRS Forms Engine.
 *
 * @package ASRS\FormsEngine\Core
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Capabilities {

	public const MANAGE_FORMS = 'asrs_manage_forms';

	/**
	 * Grant plugin capabilities to administrator role.
	 */
	public static function install(): void {
		$role = get_role( 'administrator' );
		if ( $role && ! $role->has_cap( self::MANAGE_FORMS ) ) {
			$role->add_cap( self::MANAGE_FORMS );
		}
	}

	/**
	 * Determine if the current user can manage ASRS Forms.
	 */
	public static function current_user_can_manage(): bool {
		return current_user_can( self::MANAGE_FORMS ) || current_user_can( 'manage_options' );
	}

	/**
	 * Required capability string for WordPress admin menu registration.
	 */
	public static function required_cap(): string {
		return current_user_can( self::MANAGE_FORMS ) ? self::MANAGE_FORMS : 'manage_options';
	}
}
