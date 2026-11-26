<?php
/**
 * Admin Page: Entries (Lead Submissions, KPI Summary, Search, Filters, CSV Export, Retry, Detail View).
 *
 * @package ASRS\FormsEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Admin\Pages;

use ASRS\FormsEngine\Forms\FormRegistry;
use ASRS\FormsEngine\Repositories\SubmissionRepository;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class EntriesPage extends AbstractPage {

	private SubmissionRepository $submissions;
	private FormRegistry $registry;

	public function __construct(
		SubmissionRepository $submissions,
		FormRegistry $registry
	) {
		$this->submissions = $submissions;
		$this->registry    = $registry;
	}

	public function render(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$view = isset( $_GET['view'] ) ? sanitize_key( wp_unslash( (string) $_GET['view'] ) ) : 'list';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$id = isset( $_GET['id'] ) ? (int) $_GET['id'] : 0;

		if ( 'detail' === $view && $id > 0 ) {
			$this->render_detail( $id );
			return;
		}

		$this->render_list();
	}

	private function render_list(): void {
		$stats = $this->submissions->get_stats();

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$form_id = isset( $_GET['form_id'] ) ? sanitize_key( wp_unslash( (string) $_GET['form_id'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$sync_status = isset( $_GET['sync_status'] ) ? sanitize_key( wp_unslash( (string) $_GET['sync_status'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['s'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$date_from = isset( $_GET['date_from'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['date_from'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$date_to = isset( $_GET['date_to'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['date_to'] ) ) : '';
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged = max( 1, (int) ( $_GET['paged'] ?? 1 ) );

		$per_page = 20;
		$result   = $this->submissions->query(
			array(
				'form_id'     => $form_id,
				'sync_status' => $sync_status,
				'search'      => $search,
				'date_from'   => $date_from,
				'date_to'     => $date_to,
				'page'        => $paged,
				'per_page'    => $per_page,
			)
		);

		$export_url = wp_nonce_url(
			add_query_arg(
				array(
					'page'        => 'asrs-forms',
					'asrs_action' => 'export_csv',
					'form_id'     => $form_id,
					'sync_status' => $sync_status,
					's'           => $search,
					'date_from'   => $date_from,
					'date_to'     => $date_to,
				),
				admin_url( 'admin.php' )
			),
			'asrs_export_csv'
		);

		$actions = '';
		if ( $stats['failed'] > 0 ) {
			$actions .= '<button type="button" id="asrs-run-retry-queue-btn" class="button button-secondary">' . esc_html__( 'Retry Failed Syncs', 'asrs-forms-engine' ) . '</button> ';
		}
		$actions .= sprintf(
			'<a href="%s" class="button button-primary">%s</a>',
			esc_url( $export_url ),
			esc_html__( 'Export Filtered CSV', 'asrs-forms-engine' )
		);

		$this->render_header(
			__( 'ASRS Forms — Lead Entries', 'asrs-forms-engine' ),
			__( 'Every form lead is stored safely in WordPress and synced to Zoho CRM v7 (triggering Zoho workflows automatically).', 'asrs-forms-engine' ),
			$actions
		);
		?>

		<div id="asrs-retry-queue-feedback"></div>

		<?php
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['retried'] ) ) {
			printf(
				'<div class="notice notice-info is-dismissible"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: 1: total retried, 2: succeeded */
						__( 'Retried %1$d submissions (%2$d synced).', 'asrs-forms-engine' ),
						(int) $_GET['retried'],
						(int) ( $_GET['succeeded'] ?? 0 )
					)
				)
			);
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['deleted'] ) ) {
			printf(
				'<div class="notice notice-success is-dismissible"><p>%s</p></div>',
				esc_html(
					sprintf(
						/* translators: %d: deleted count */
						__( 'Deleted %d submission(s).', 'asrs-forms-engine' ),
						(int) $_GET['deleted']
					)
				)
			);
		}
		?>

		<div class="asrs-kpi-grid">
			<div class="asrs-kpi-card">
				<div class="asrs-kpi-label"><?php esc_html_e( 'Total Leads', 'asrs-forms-engine' ); ?></div>
				<div class="asrs-kpi-value"><?php echo esc_html( number_format_i18n( $stats['total'] ) ); ?></div>
			</div>
			<div class="asrs-kpi-card asrs-kpi-success">
				<div class="asrs-kpi-label"><?php esc_html_e( 'Synced to Zoho CRM', 'asrs-forms-engine' ); ?></div>
				<div class="asrs-kpi-value"><?php echo esc_html( number_format_i18n( $stats['synced'] ) ); ?></div>
			</div>
			<div class="asrs-kpi-card">
				<div class="asrs-kpi-label"><?php esc_html_e( 'Pending Sync', 'asrs-forms-engine' ); ?></div>
				<div class="asrs-kpi-value"><?php echo esc_html( number_format_i18n( $stats['pending'] ) ); ?></div>
			</div>
			<div class="asrs-kpi-card <?php echo $stats['failed'] > 0 ? 'asrs-kpi-danger' : ''; ?>">
				<div class="asrs-kpi-label"><?php esc_html_e( 'Failed Syncs', 'asrs-forms-engine' ); ?></div>
				<div class="asrs-kpi-value"><?php echo esc_html( number_format_i18n( $stats['failed'] ) ); ?></div>
			</div>
		</div>

		<?php $forms = $this->registry->all(); ?>

		<div class="asrs-panel">
			<form method="get" class="asrs-filter-bar">
				<input type="hidden" name="page" value="asrs-forms" />
				<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search name, email, Zoho ID...', 'asrs-forms-engine' ); ?>" />

				<select name="form_id">
					<option value=""><?php esc_html_e( 'All Forms', 'asrs-forms-engine' ); ?></option>
					<?php foreach ( $forms as $f ) : ?>
						<option value="<?php echo esc_attr( $f->get_id() ); ?>" <?php selected( $form_id, $f->get_id() ); ?>>
							<?php echo esc_html( $f->get_title() ); ?>
						</option>
					<?php endforeach; ?>
				</select>

				<select name="sync_status">
					<option value=""><?php esc_html_e( 'All Sync Statuses', 'asrs-forms-engine' ); ?></option>
					<option value="synced" <?php selected( $sync_status, 'synced' ); ?>><?php esc_html_e( 'Synced', 'asrs-forms-engine' ); ?></option>
					<option value="pending" <?php selected( $sync_status, 'pending' ); ?>><?php esc_html_e( 'Pending', 'asrs-forms-engine' ); ?></option>
					<option value="failed" <?php selected( $sync_status, 'failed' ); ?>><?php esc_html_e( 'Failed', 'asrs-forms-engine' ); ?></option>
				</select>

				<input type="date" name="date_from" value="<?php echo esc_attr( $date_from ); ?>" title="<?php esc_attr_e( 'From Date', 'asrs-forms-engine' ); ?>" />
				<input type="date" name="date_to" value="<?php echo esc_attr( $date_to ); ?>" title="<?php esc_attr_e( 'To Date', 'asrs-forms-engine' ); ?>" />

				<button type="submit" class="button button-secondary"><?php esc_html_e( 'Filter', 'asrs-forms-engine' ); ?></button>
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=asrs-forms' ) ); ?>" class="button button-link"><?php esc_html_e( 'Reset', 'asrs-forms-engine' ); ?></a>
			</form>

			<form method="post">
				<?php wp_nonce_field( 'asrs_entries_bulk_nonce' ); ?>

				<div class="tablenav top">
					<div class="alignleft actions bulkactions">
						<select name="bulk_action">
							<option value=""><?php esc_html_e( 'Bulk Actions', 'asrs-forms-engine' ); ?></option>
							<option value="retry"><?php esc_html_e( 'Retry Zoho Sync', 'asrs-forms-engine' ); ?></option>
							<option value="delete"><?php esc_html_e( 'Delete Permanently', 'asrs-forms-engine' ); ?></option>
						</select>
						<button type="submit" name="asrs_entries_bulk_submit" value="1" class="button action"><?php esc_html_e( 'Apply', 'asrs-forms-engine' ); ?></button>
					</div>
					<div class="alignright">
						<span class="displaying-num">
							<?php
							printf(
								/* translators: %s: total count */
								esc_html__( '%s entries', 'asrs-forms-engine' ),
								esc_html( number_format_i18n( $result['total'] ) )
							);
							?>
						</span>
					</div>
				</div>

				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<td class="manage-column column-cb check-column"><input type="checkbox" class="asrs-select-all" /></td>
							<th style="width:65px;"><?php esc_html_e( 'ID', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'Form', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'Lead Summary', 'asrs-forms-engine' ); ?></th>
							<th style="width:110px;"><?php esc_html_e( 'Sync Status', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'Zoho CRM Record', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'Submitted At', 'asrs-forms-engine' ); ?></th>
							<th style="width:140px;"><?php esc_html_e( 'Actions', 'asrs-forms-engine' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php if ( empty( $result['items'] ) ) : ?>
							<tr><td colspan="8"><?php esc_html_e( 'No submissions recorded yet.', 'asrs-forms-engine' ); ?></td></tr>
						<?php else : ?>
							<?php foreach ( $result['items'] as $row ) : ?>
								<?php
								$form_obj = $this->registry->get( (string) $row['form_id'] );
								$preview  = array();
								foreach ( array_slice( $row['data'], 0, 3, true ) as $k => $v ) {
									$val_str   = is_array( $v ) ? implode( ', ', $v ) : (string) $v;
									$preview[] = '<strong>' . esc_html( ucwords( str_replace( '_', ' ', (string) $k ) ) ) . ':</strong> ' . esc_html( mb_strimwidth( $val_str, 0, 60, '...' ) );
								}
								?>
								<tr>
									<th scope="row" class="check-column">
										<input type="checkbox" name="entry_ids[]" value="<?php echo esc_attr( (string) $row['id'] ); ?>" />
									</th>
									<td>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=asrs-forms&view=detail&id=' . (int) $row['id'] ) ); ?>">
											<strong>#<?php echo esc_html( (string) $row['id'] ); ?></strong>
										</a>
									</td>
									<td><?php echo esc_html( $form_obj ? $form_obj->get_title() : (string) $row['form_id'] ); ?></td>
									<td><?php echo implode( '<br>', $preview ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
									<td><?php echo $this->render_status_badge( (string) $row['sync_status'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
									<td>
										<?php if ( ! empty( $row['zoho_record_id'] ) ) : ?>
											<code><?php echo esc_html( (string) $row['zoho_record_id'] ); ?></code>
											<?php if ( ! empty( $row['zoho_record_url'] ) ) : ?>
												<br><a href="<?php echo esc_url( (string) $row['zoho_record_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open in Zoho ↗', 'asrs-forms-engine' ); ?></a>
											<?php endif; ?>
										<?php elseif ( ! empty( $row['last_sync_error'] ) ) : ?>
											<small class="asrs-text-danger"><?php echo esc_html( mb_strimwidth( (string) $row['last_sync_error'], 0, 80, '...' ) ); ?></small>
										<?php else : ?>
											—
										<?php endif; ?>
									</td>
									<td><?php echo $this->format_datetime( (string) $row['created_at'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
									<td>
										<a href="<?php echo esc_url( admin_url( 'admin.php?page=asrs-forms&view=detail&id=' . (int) $row['id'] ) ); ?>" class="button button-small">
											<?php esc_html_e( 'Details', 'asrs-forms-engine' ); ?>
										</a>
										<?php if ( 'synced' !== $row['sync_status'] ) : ?>
											<button type="button" class="button button-small asrs-retry-one-btn" data-id="<?php echo esc_attr( (string) $row['id'] ); ?>">
												<?php esc_html_e( 'Sync', 'asrs-forms-engine' ); ?>
											</button>
										<?php endif; ?>
									</td>
								</tr>
							<?php endforeach; ?>
						<?php endif; ?>
					</tbody>
				</table>
			</form>

			<?php
			$base_url = add_query_arg(
				array(
					'page'        => 'asrs-forms',
					'form_id'     => $form_id,
					'sync_status' => $sync_status,
					's'           => $search,
					'date_from'   => $date_from,
					'date_to'     => $date_to,
				),
				admin_url( 'admin.php' )
			);
			$this->render_pagination( $result['total'], $per_page, $paged, $base_url );
			?>
		</div>

		<?php
		$this->render_footer();
	}

	private function render_detail( int $id ): void {
		$row = $this->submissions->find( $id );
		if ( ! $row ) {
			$this->render_header( __( 'Submission Not Found', 'asrs-forms-engine' ) );
			echo '<div class="notice notice-error"><p>' . esc_html__( 'The requested submission record does not exist.', 'asrs-forms-engine' ) . '</p></div>';
			$this->render_footer();
			return;
		}

		$form_obj = $this->registry->get( (string) $row['form_id'] );

		$actions = sprintf(
			'<a href="%s" class="button button-secondary">← %s</a> <button type="button" class="button button-primary asrs-retry-one-btn" data-id="%d">%s</button>',
			esc_url( admin_url( 'admin.php?page=asrs-forms' ) ),
			esc_html__( 'Back to Entries', 'asrs-forms-engine' ),
			$id,
			esc_html__( 'Sync / Retry Zoho CRM', 'asrs-forms-engine' )
		);

		$this->render_header(
			sprintf(
				/* translators: %d: submission ID */
				__( 'Submission #%d', 'asrs-forms-engine' ),
				$id
			),
			sprintf( 'UUID: %s | Form: %s', (string) $row['submission_uuid'], $form_obj ? $form_obj->get_title() : (string) $row['form_id'] ),
			$actions
		);
		?>

		<div class="asrs-two-col">
			<div class="asrs-panel">
				<h2><?php esc_html_e( 'Submitted Form Data', 'asrs-forms-engine' ); ?></h2>
				<table class="asrs-kv-table">
					<?php foreach ( $row['data'] as $key => $val ) : ?>
						<?php
						$label = $key;
						if ( $form_obj && method_exists( $form_obj, 'get_field_map' ) ) {
							$map = $form_obj->get_field_map();
							if ( isset( $map[ $key ] ) ) {
								$label = $map[ $key ]->get_label();
							}
						}
						$val_text = is_array( $val ) ? implode( ', ', $val ) : (string) $val;
						?>
						<tr>
							<th>
								<?php echo esc_html( (string) $label ); ?>
								<br><small><code><?php echo esc_html( (string) $key ); ?></code></small>
							</th>
							<td><?php echo nl2br( esc_html( $val_text ) ); ?></td>
						</tr>
					<?php endforeach; ?>
				</table>
			</div>

			<div class="asrs-panel">
				<div class="asrs-panel-header">
					<h2><?php esc_html_e( 'Zoho CRM Sync & Metadata', 'asrs-forms-engine' ); ?></h2>
					<?php echo $this->render_status_badge( (string) $row['sync_status'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
				<table class="asrs-kv-table">
					<tr>
						<th><?php esc_html_e( 'Zoho Module', 'asrs-forms-engine' ); ?></th>
						<td><code><?php echo esc_html( (string) ( $row['zoho_module'] ?: '—' ) ); ?></code></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Zoho Record ID', 'asrs-forms-engine' ); ?></th>
						<td>
							<?php if ( ! empty( $row['zoho_record_id'] ) ) : ?>
								<code><?php echo esc_html( (string) $row['zoho_record_id'] ); ?></code>
								<?php if ( ! empty( $row['zoho_record_url'] ) ) : ?>
									&nbsp;<a href="<?php echo esc_url( (string) $row['zoho_record_url'] ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open in Zoho CRM ↗', 'asrs-forms-engine' ); ?></a>
								<?php endif; ?>
							<?php else : ?>
								—
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Sync Attempts', 'asrs-forms-engine' ); ?></th>
						<td><?php echo esc_html( (string) $row['sync_attempts'] ); ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Last Sync At', 'asrs-forms-engine' ); ?></th>
						<td><?php echo $this->format_datetime( $row['last_sync_at'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
					<?php if ( ! empty( $row['last_sync_error'] ) ) : ?>
						<tr>
							<th><?php esc_html_e( 'Last Sync Error', 'asrs-forms-engine' ); ?></th>
							<td><code class="asrs-text-danger"><?php echo esc_html( (string) $row['last_sync_error'] ); ?></code></td>
						</tr>
					<?php endif; ?>
					<tr>
						<th><?php esc_html_e( 'Page URL', 'asrs-forms-engine' ); ?></th>
						<td><?php echo ! empty( $row['meta']['page_url'] ) ? esc_html( (string) $row['meta']['page_url'] ) : '—'; ?></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'IP Address', 'asrs-forms-engine' ); ?></th>
						<td><code><?php echo esc_html( (string) ( $row['meta']['ip_address'] ?? '—' ) ); ?></code></td>
					</tr>
					<tr>
						<th><?php esc_html_e( 'Submitted At', 'asrs-forms-engine' ); ?></th>
						<td><?php echo $this->format_datetime( (string) $row['created_at'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					</tr>
				</table>
			</div>
		</div>

		<?php if ( ! empty( $row['zoho_response'] ) ) : ?>
			<div class="asrs-panel">
				<h2><?php esc_html_e( 'Zoho CRM Response Payload', 'asrs-forms-engine' ); ?></h2>
				<pre class="asrs-code-block"><?php echo esc_html( wp_json_encode( $row['zoho_response'], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES ) ?: '' ); ?></pre>
			</div>
		<?php endif; ?>

		<?php
		$this->render_footer();
	}
}
