<?php
/**
 * Repository for form submissions and Zoho CRM sync state.
 *
 * @package ASRS\FormsEngine\Repositories
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Repositories;

use ASRS\FormsEngine\Database\Schema;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SubmissionRepository extends AbstractRepository {

	public const STATUS_PENDING = 'pending';
	public const STATUS_SYNCED  = 'synced';
	public const STATUS_FAILED  = 'failed';

	protected function table(): string {
		return Schema::table( 'submissions' );
	}

	/**
	 * Insert a new submission row.
	 *
	 * @param array<string, mixed> $record Record fields.
	 */
	public function create( array $record ): int {
		$now = current_time( 'mysql', true );

		$data = array(
			'submission_uuid'    => (string) ( $record['submission_uuid'] ?? wp_generate_uuid4() ),
			'form_id'            => sanitize_key( (string) ( $record['form_id'] ?? '' ) ),
			'data_json'          => wp_json_encode( $record['data'] ?? array() ),
			'meta_json'          => wp_json_encode( $record['meta'] ?? array() ),
			'sync_status'        => (string) ( $record['sync_status'] ?? self::STATUS_PENDING ),
			'zoho_module'        => isset( $record['zoho_module'] ) ? sanitize_text_field( (string) $record['zoho_module'] ) : null,
			'zoho_record_id'     => null,
			'zoho_record_url'    => null,
			'zoho_response_json' => null,
			'sync_attempts'      => 0,
			'last_sync_error'    => null,
			'last_sync_at'       => null,
			'next_retry_at'      => $record['next_retry_at'] ?? null,
			'created_at'         => $now,
			'updated_at'         => $now,
		);

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery
		$inserted = $this->db->insert( $this->table(), $data );

		return false === $inserted ? 0 : (int) $this->db->insert_id;
	}

	/**
	 * Fetch a single submission by ID.
	 *
	 * @return array<string, mixed>|null
	 */
	public function find( int $id ): ?array {
		$table = $this->table();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $this->db->get_row( $this->db->prepare( "SELECT * FROM `{$table}` WHERE id = %d", $id ), ARRAY_A );

		return is_array( $row ) ? $this->hydrate( $row ) : null;
	}

	/**
	 * Mark submission as synced with Zoho CRM.
	 *
	 * @param array<string, mixed> $response Raw API response.
	 */
	public function mark_synced( int $id, string $module, string $record_id, string $record_url, array $response ): void {
		$now   = current_time( 'mysql', true );
		$table = $this->table();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$this->db->query(
			$this->db->prepare(
				"UPDATE `{$table}`
				SET sync_status = %s,
					zoho_module = %s,
					zoho_record_id = %s,
					zoho_record_url = %s,
					zoho_response_json = %s,
					sync_attempts = sync_attempts + 1,
					last_sync_error = NULL,
					last_sync_at = %s,
					next_retry_at = NULL,
					updated_at = %s
				WHERE id = %d",
				self::STATUS_SYNCED,
				$module,
				$record_id,
				$record_url,
				wp_json_encode( $response ),
				$now,
				$now,
				$id
			)
		);
	}

	/**
	 * Mark submission sync as failed and schedule next retry timestamp.
	 *
	 * @param array<string, mixed> $response Raw response payload.
	 */
	public function mark_failed( int $id, string $module, string $error, array $response = array(), ?string $next_retry_at = null ): void {
		$now   = current_time( 'mysql', true );
		$table = $this->table();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$this->db->query(
			$this->db->prepare(
				"UPDATE `{$table}`
				SET sync_status = %s,
					zoho_module = %s,
					zoho_response_json = %s,
					sync_attempts = sync_attempts + 1,
					last_sync_error = %s,
					last_sync_at = %s,
					next_retry_at = %s,
					updated_at = %s
				WHERE id = %d",
				self::STATUS_FAILED,
				$module,
				wp_json_encode( $response ),
				$error,
				$now,
				$next_retry_at,
				$now,
				$id
			)
		);
	}

	/**
	 * Query submissions with filters and pagination.
	 *
	 * @param array<string, mixed> $args Query arguments.
	 * @return array{items: array<int, array<string, mixed>>, total: int}
	 */
	public function query( array $args = array() ): array {
		$table    = $this->table();
		$where    = array( '1=1' );
		$params   = array();
		$per_page = max( 1, min( 500, (int) ( $args['per_page'] ?? 20 ) ) );
		$page     = max( 1, (int) ( $args['page'] ?? 1 ) );
		$offset   = ( $page - 1 ) * $per_page;

		if ( ! empty( $args['form_id'] ) ) {
			$where[]  = 'form_id = %s';
			$params[] = sanitize_key( (string) $args['form_id'] );
		}

		if ( ! empty( $args['sync_status'] ) ) {
			$where[]  = 'sync_status = %s';
			$params[] = sanitize_key( (string) $args['sync_status'] );
		}

		if ( ! empty( $args['date_from'] ) ) {
			$where[]  = 'created_at >= %s';
			$params[] = sanitize_text_field( (string) $args['date_from'] ) . ' 00:00:00';
		}

		if ( ! empty( $args['date_to'] ) ) {
			$where[]  = 'created_at <= %s';
			$params[] = sanitize_text_field( (string) $args['date_to'] ) . ' 23:59:59';
		}

		if ( ! empty( $args['search'] ) ) {
			$like     = '%' . $this->db->esc_like( (string) $args['search'] ) . '%';
			$where[]  = '(data_json LIKE %s OR zoho_record_id LIKE %s OR submission_uuid LIKE %s OR last_sync_error LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		$where_sql = implode( ' AND ', $where );
		$order_by  = $this->sanitize_order_by(
			(string) ( $args['order_by'] ?? 'created_at' ),
			array( 'id', 'form_id', 'sync_status', 'sync_attempts', 'created_at', 'last_sync_at' ),
			'created_at'
		);
		$order_dir = $this->sanitize_order_dir( (string) ( $args['order'] ?? 'DESC' ) );

		$count_sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$where_sql}";
		if ( ! empty( $params ) ) {
			// phpcs:ignore WordPress.DB.PreparedSQL.NotPrepared
			$count_sql = $this->db->prepare( $count_sql, $params );
		}
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$total = (int) $this->db->get_var( $count_sql );

		$data_params   = $params;
		$data_params[] = $per_page;
		$data_params[] = $offset;

		$data_sql = "SELECT * FROM `{$table}` WHERE {$where_sql} ORDER BY {$order_by} {$order_dir} LIMIT %d OFFSET %d";
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$rows = $this->db->get_results( $this->db->prepare( $data_sql, $data_params ), ARRAY_A );

		$items = array();
		if ( is_array( $rows ) ) {
			foreach ( $rows as $row ) {
				$items[] = $this->hydrate( $row );
			}
		}

		return array(
			'items' => $items,
			'total' => $total,
		);
	}

	/**
	 * Retrieve submissions due for automatic background retry.
	 *
	 * @return array<int, array<string, mixed>>
	 */
	public function get_due_for_retry( int $max_attempts, int $limit = 20 ): array {
		$now   = current_time( 'mysql', true );
		$table = $this->table();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $this->db->get_results(
			$this->db->prepare(
				"SELECT * FROM `{$table}`
				WHERE (sync_status = %s OR (sync_status = %s AND created_at <= DATE_SUB(%s, INTERVAL 5 MINUTE)))
				AND sync_attempts < %d
				AND (next_retry_at IS NULL OR next_retry_at <= %s)
				ORDER BY created_at ASC
				LIMIT %d",
				self::STATUS_FAILED,
				self::STATUS_PENDING,
				$now,
				$max_attempts,
				$now,
				$limit
			),
			ARRAY_A
		);

		return is_array( $rows ) ? array_map( array( $this, 'hydrate' ), $rows ) : array();
	}

	public function delete( int $id ): bool {
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching
		return false !== $this->db->delete( $this->table(), array( 'id' => $id ), array( '%d' ) );
	}

	/**
	 * Aggregate KPI counts grouped by sync_status and form_id.
	 *
	 * @return array<string, mixed>
	 */
	public function get_stats(): array {
		$table = $this->table();

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $this->db->get_results( "SELECT sync_status, COUNT(*) AS cnt FROM `{$table}` GROUP BY sync_status", ARRAY_A );

		$counts = array(
			'total'   => 0,
			'synced'  => 0,
			'pending' => 0,
			'failed'  => 0,
		);

		if ( is_array( $rows ) ) {
			foreach ( $rows as $row ) {
				$status = (string) $row['sync_status'];
				$cnt    = (int) $row['cnt'];
				$counts['total'] += $cnt;
				if ( isset( $counts[ $status ] ) ) {
					$counts[ $status ] = $cnt;
				}
			}
		}

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$by_form_rows = $this->db->get_results( "SELECT form_id, COUNT(*) AS cnt FROM `{$table}` GROUP BY form_id", ARRAY_A );
		$by_form      = array();
		if ( is_array( $by_form_rows ) ) {
			foreach ( $by_form_rows as $row ) {
				$by_form[ (string) $row['form_id'] ] = (int) $row['cnt'];
			}
		}

		$counts['by_form'] = $by_form;

		return $counts;
	}

	/**
	 * @param array<string, mixed> $row Raw DB row.
	 * @return array<string, mixed>
	 */
	private function hydrate( array $row ): array {
		$row['id']            = (int) $row['id'];
		$row['sync_attempts'] = (int) $row['sync_attempts'];
		$row['data']          = json_decode( (string) ( $row['data_json'] ?? '{}' ), true ) ?: array();
		$row['meta']          = json_decode( (string) ( $row['meta_json'] ?? '{}' ), true ) ?: array();
		$row['zoho_response'] = json_decode( (string) ( $row['zoho_response_json'] ?? '{}' ), true ) ?: array();
		return $row;
	}
}
