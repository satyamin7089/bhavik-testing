<?php
/**
 * Repository for job applications in wp_asrs_recruit_applications.
 *
 * @package ASRS\RecruitEngine\Repositories
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Repositories;

use ASRS\RecruitEngine\Database\Schema;
use wpdb;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ApplicationRepository {

	public const STATUS_PENDING = 'pending';
	public const STATUS_SYNCED  = 'synced';
	public const STATUS_FAILED  = 'failed';

	private wpdb $db;

	public function __construct() {
		global $wpdb;
		$this->db = $wpdb;
	}

	/**
	 * @param array<string, mixed> $record Application payload.
	 */
	public function create( array $record ): int {
		$now = current_time( 'mysql', true );
		$ok  = $this->db->insert(
			Schema::table(),
			array(
				'application_uuid'   => (string) ( $record['application_uuid'] ?? wp_generate_uuid4() ),
				'job_id'             => sanitize_text_field( (string) ( $record['job_id'] ?? '' ) ),
				'job_title'          => sanitize_text_field( (string) ( $record['job_title'] ?? '' ) ),
				'first_name'         => sanitize_text_field( (string) ( $record['first_name'] ?? '' ) ),
				'last_name'          => sanitize_text_field( (string) ( $record['last_name'] ?? '' ) ),
				'email'              => sanitize_email( (string) ( $record['email'] ?? '' ) ),
				'phone'              => sanitize_text_field( (string) ( $record['phone'] ?? '' ) ),
				'city'               => sanitize_text_field( (string) ( $record['city'] ?? '' ) ),
				'experience_years'   => sanitize_text_field( (string) ( $record['experience_years'] ?? '' ) ),
				'linkedin_url'       => esc_url_raw( (string) ( $record['linkedin_url'] ?? '' ) ),
				'cover_note'         => sanitize_textarea_field( (string) ( $record['cover_note'] ?? '' ) ),
				'resume_path'        => (string) ( $record['resume_path'] ?? '' ),
				'resume_name'        => sanitize_file_name( (string) ( $record['resume_name'] ?? '' ) ),
				'sync_status'        => self::STATUS_PENDING,
				'zoho_candidate_id'  => null,
				'zoho_candidate_url' => null,
				'resume_uploaded'    => 0,
				'job_associated'     => 0,
				'sync_attempts'      => 0,
				'created_at'         => $now,
				'updated_at'         => $now,
			)
		);

		return false === $ok ? 0 : (int) $this->db->insert_id;
	}

	/**
	 * @return array<string, mixed>|null
	 */
	public function find( int $id ): ?array {
		$table = Schema::table();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$row = $this->db->get_row( $this->db->prepare( "SELECT * FROM `{$table}` WHERE id = %d", $id ), ARRAY_A );

		return is_array( $row ) ? $row : null;
	}

	public function update_sync_progress( int $id, array $fields ): bool {
		$fields['updated_at'] = current_time( 'mysql', true );
		return false !== $this->db->update( Schema::table(), $fields, array( 'id' => $id ) );
	}

	/**
	 * @param array<string, mixed> $args Filter args.
	 * @return array{items: array<int, array<string, mixed>>, total: int}
	 */
	public function query( array $args = array() ): array {
		$table    = Schema::table();
		$where    = array( '1=1' );
		$params   = array();
		$per_page = max( 1, min( 100, (int) ( $args['per_page'] ?? 25 ) ) );
		$page     = max( 1, (int) ( $args['page'] ?? 1 ) );
		$offset   = ( $page - 1 ) * $per_page;

		if ( ! empty( $args['job_id'] ) ) {
			$where[]  = 'job_id = %s';
			$params[] = sanitize_text_field( (string) $args['job_id'] );
		}
		if ( ! empty( $args['sync_status'] ) ) {
			$where[]  = 'sync_status = %s';
			$params[] = sanitize_key( (string) $args['sync_status'] );
		}
		if ( ! empty( $args['search'] ) ) {
			$like     = '%' . $this->db->esc_like( sanitize_text_field( (string) $args['search'] ) ) . '%';
			$where[]  = '(first_name LIKE %s OR last_name LIKE %s OR email LIKE %s OR job_title LIKE %s)';
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
			$params[] = $like;
		}

		$where_sql = implode( ' AND ', $where );
		$count_sql = "SELECT COUNT(*) FROM `{$table}` WHERE {$where_sql}";
		$data_sql  = "SELECT * FROM `{$table}` WHERE {$where_sql} ORDER BY id DESC LIMIT %d OFFSET %d";

		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$total = (int) ( empty( $params ) ? $this->db->get_var( $count_sql ) : $this->db->get_var( $this->db->prepare( $count_sql, $params ) ) );

		$data_params = array_merge( $params, array( $per_page, $offset ) );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.NotPrepared
		$items = $this->db->get_results( $this->db->prepare( $data_sql, $data_params ), ARRAY_A );

		return array(
			'items' => is_array( $items ) ? $items : array(),
			'total' => $total,
		);
	}

	/**
	 * @return array<int, array<string, mixed>>
	 */
	public function get_due_for_retry( int $limit = 20, int $max_attempts = 5 ): array {
		$table = Schema::table();
		$now   = current_time( 'mysql', true );
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $this->db->get_results(
			$this->db->prepare(
				"SELECT * FROM `{$table}` WHERE sync_status = %s AND sync_attempts < %d AND (next_retry_at IS NULL OR next_retry_at <= %s) ORDER BY id ASC LIMIT %d",
				self::STATUS_FAILED,
				$max_attempts,
				$now,
				$limit
			),
			ARRAY_A
		);

		return is_array( $rows ) ? $rows : array();
	}

	/**
	 * @return array{total: int, synced: int, failed: int, pending: int}
	 */
	public function get_stats(): array {
		$table = Schema::table();
		// phpcs:ignore WordPress.DB.DirectDatabaseQuery.DirectQuery, WordPress.DB.DirectDatabaseQuery.NoCaching, WordPress.DB.PreparedSQL.InterpolatedNotPrepared
		$rows = $this->db->get_results( "SELECT sync_status, COUNT(*) AS cnt FROM `{$table}` GROUP BY sync_status", ARRAY_A );
		$res  = array( 'total' => 0, 'synced' => 0, 'failed' => 0, 'pending' => 0 );

		if ( is_array( $rows ) ) {
			foreach ( $rows as $row ) {
				$cnt = (int) $row['cnt'];
				$res['total'] += $cnt;
				if ( isset( $res[ $row['sync_status'] ] ) ) {
					$res[ $row['sync_status'] ] = $cnt;
				}
			}
		}

		return $res;
	}

	public function delete( int $id ): bool {
		$row = $this->find( $id );
		if ( $row && ! empty( $row['resume_path'] ) && file_exists( (string) $row['resume_path'] ) ) {
			wp_delete_file( (string) $row['resume_path'] );
		}
		return false !== $this->db->delete( Schema::table(), array( 'id' => $id ), array( '%d' ) );
	}
}
