<?php
/**
 * Base repository with common SQL safety helpers.
 *
 * @package ASRS\FormsEngine\Repositories
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Repositories;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class AbstractRepository {

	protected \wpdb $db;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * Table name for the concrete repository.
	 */
	abstract protected function table(): string;

	/**
	 * Whitelist an ORDER BY column.
	 *
	 * @param string   $column  Requested column.
	 * @param string[] $allowed Allowed columns.
	 * @param string   $default Default column.
	 */
	protected function sanitize_order_by( string $column, array $allowed, string $default ): string {
		return in_array( $column, $allowed, true ) ? $column : $default;
	}

	/**
	 * Whitelist sort direction.
	 *
	 * @param string $direction ASC or DESC.
	 */
	protected function sanitize_order_dir( string $direction ): string {
		return 'ASC' === strtoupper( $direction ) ? 'ASC' : 'DESC';
	}
}
