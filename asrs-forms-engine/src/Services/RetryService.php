<?php
/**
 * Failed sync retry engine (manual + WP-Cron batch processing).
 *
 * @package ASRS\FormsEngine\Services
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Repositories\SubmissionRepository;
use WP_Error;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class RetryService {

	private SubmissionRepository $submissions;
	private SyncService $sync;
	private Settings $settings;

	public function __construct(
		SubmissionRepository $submissions,
		SyncService $sync,
		Settings $settings
	) {
		$this->submissions = $submissions;
		$this->sync        = $sync;
		$this->settings    = $settings;
	}

	/**
	 * Manually retry a single submission immediately.
	 *
	 * @param int $submission_id Submission ID.
	 * @return array<string, mixed>|WP_Error
	 */
	public function retry_one( int $submission_id ) {
		return $this->sync->sync_submission( $submission_id, true );
	}

	/**
	 * Retry multiple submissions by IDs.
	 *
	 * @param int[] $ids Submission IDs.
	 * @return array{total: int, succeeded: int, failed: int}
	 */
	public function retry_many( array $ids ): array {
		$succeeded = 0;
		$failed    = 0;

		foreach ( $ids as $id ) {
			$res = $this->retry_one( (int) $id );
			if ( is_wp_error( $res ) || ! empty( $res['skipped'] ) ) {
				++$failed;
			} else {
				++$succeeded;
			}
		}

		return array(
			'total'     => count( $ids ),
			'succeeded' => $succeeded,
			'failed'    => $failed,
		);
	}

	/**
	 * Process all due failed/pending submissions (called by WP-Cron or manual "Run Queue Now").
	 *
	 * @param int  $batch_limit Maximum records per run.
	 * @param bool $ignore_backoff When true (manual run), retries all failed submissions under max attempts.
	 * @return array{processed: int, succeeded: int, failed: int}
	 */
	public function process_queue( int $batch_limit = 20, bool $ignore_backoff = false ): array {
		$max_attempts = (int) $this->settings->get( 'max_retry_attempts', 5 );

		if ( $ignore_backoff ) {
			$query = $this->submissions->query(
				array(
					'sync_status' => SubmissionRepository::STATUS_FAILED,
					'per_page'    => $batch_limit,
					'order_by'    => 'created_at',
					'order'       => 'ASC',
				)
			);
			$due = $query['items'];
		} else {
			$due = $this->submissions->get_due_for_retry( $max_attempts, $batch_limit );
		}

		$succeeded = 0;
		$failed    = 0;

		foreach ( $due as $row ) {
			$res = $this->sync->sync_submission( (int) $row['id'], true );
			if ( is_wp_error( $res ) || ! empty( $res['skipped'] ) ) {
				++$failed;
			} else {
				++$succeeded;
			}
		}

		return array(
			'processed' => count( $due ),
			'succeeded' => $succeeded,
			'failed'    => $failed,
		);
	}
}
