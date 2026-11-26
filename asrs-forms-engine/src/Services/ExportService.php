<?php
/**
 * CSV Export service with formula-injection hardening.
 *
 * @package ASRS\FormsEngine\Services
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Services;

use ASRS\FormsEngine\Forms\FormRegistry;
use ASRS\FormsEngine\Repositories\SubmissionRepository;
use ASRS\FormsEngine\Security\Sanitizer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ExportService {

	private SubmissionRepository $submissions;
	private FormRegistry $registry;

	public function __construct( SubmissionRepository $submissions, FormRegistry $registry ) {
		$this->submissions = $submissions;
		$this->registry    = $registry;
	}

	/**
	 * Stream filtered submissions as a UTF-8 CSV download.
	 *
	 * @param array<string, mixed> $filters Query filters (form_id, sync_status, search, date_from, date_to).
	 */
	public function stream_submissions_csv( array $filters ): void {
		$filters['per_page'] = 500;
		$filters['page']     = 1;

		$first_batch = $this->submissions->query( $filters );
		$items       = $first_batch['items'];

		// Fetch remaining pages up to 10,000 rows safely.
		$total_pages = min( 20, (int) ceil( $first_batch['total'] / 500 ) );
		for ( $p = 2; $p <= $total_pages; $p++ ) {
			$filters['page'] = $p;
			$batch           = $this->submissions->query( $filters );
			$items           = array_merge( $items, $batch['items'] );
		}

		// Collect dynamic field columns across exported submissions.
		$field_keys = array();
		if ( ! empty( $filters['form_id'] ) ) {
			$form = $this->registry->get( (string) $filters['form_id'] );
			if ( $form ) {
				foreach ( $form->get_fields() as $field ) {
					$field_keys[ $field->get_name() ] = $field->get_label();
				}
			}
		}

		foreach ( $items as $row ) {
			foreach ( array_keys( $row['data'] ) as $key ) {
				if ( ! isset( $field_keys[ $key ] ) ) {
					$field_keys[ $key ] = ucwords( str_replace( '_', ' ', (string) $key ) );
				}
			}
		}

		$filename = sprintf(
			'asrs-submissions-%s%s.csv',
			! empty( $filters['form_id'] ) ? sanitize_key( (string) $filters['form_id'] ) . '-' : '',
			gmdate( 'Y-m-d-His' )
		);

		nocache_headers();
		header( 'Content-Type: text/csv; charset=utf-8' );
		header( 'Content-Disposition: attachment; filename="' . $filename . '"' );

		$output = fopen( 'php://output', 'w' );
		if ( false === $output ) {
			exit;
		}

		// UTF-8 BOM for Excel compatibility.
		fwrite( $output, "\xEF\xBB\xBF" );

		$headers = array_merge(
			array(
				'ID',
				'Submission UUID',
				'Form ID',
				'Sync Status',
				'Zoho Module',
				'Zoho Record ID',
				'Sync Attempts',
				'Last Sync Error',
				'Submitted At (UTC)',
			),
			array_values( $field_keys ),
			array( 'Page URL', 'IP Address' )
		);

		fputcsv( $output, array_map( array( Sanitizer::class, 'escape_csv_cell' ), $headers ) );

		foreach ( $items as $row ) {
			$line = array(
				(string) $row['id'],
				(string) $row['submission_uuid'],
				(string) $row['form_id'],
				(string) $row['sync_status'],
				(string) ( $row['zoho_module'] ?? '' ),
				(string) ( $row['zoho_record_id'] ?? '' ),
				(string) $row['sync_attempts'],
				(string) ( $row['last_sync_error'] ?? '' ),
				(string) $row['created_at'],
			);

			foreach ( array_keys( $field_keys ) as $fk ) {
				$val    = $row['data'][ $fk ] ?? '';
				$line[] = is_array( $val ) ? implode( '; ', $val ) : (string) $val;
			}

			$line[] = (string) ( $row['meta']['page_url'] ?? '' );
			$line[] = (string) ( $row['meta']['ip_address'] ?? '' );

			fputcsv( $output, array_map( array( Sanitizer::class, 'escape_csv_cell' ), $line ) );
		}

		fclose( $output );
		exit;
	}
}
