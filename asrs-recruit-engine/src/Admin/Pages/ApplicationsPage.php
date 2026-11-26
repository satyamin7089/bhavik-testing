<?php
/**
 * Admin Screen 1: Applications & Zoho Recruit Sync Status.
 *
 * @package ASRS\RecruitEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Admin\Pages;

use ASRS\RecruitEngine\Core\Settings;
use ASRS\RecruitEngine\Repositories\ApplicationRepository;
use ASRS\RecruitEngine\Zoho\RecruitDataCenter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ApplicationsPage {

	private ApplicationRepository $applications;
	private Settings $settings;

	public function __construct( ApplicationRepository $applications, Settings $settings ) {
		$this->applications = $applications;
		$this->settings     = $settings;
	}

	public function render(): void {
		$status = isset( $_GET['sync_status'] ) ? sanitize_key( wp_unslash( (string) $_GET['sync_status'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$search = isset( $_GET['s'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['s'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$paged  = max( 1, isset( $_GET['paged'] ) ? (int) $_GET['paged'] : 1 ); // phpcs:ignore WordPress.Security.NonceVerification.Recommended

		$result = $this->applications->query(
			array(
				'sync_status' => $status,
				'search'      => $search,
				'page'        => $paged,
				'per_page'    => 20,
			)
		);
		$counts = $this->applications->get_stats();
		$items  = $result['items'];
		$pages  = max( 1, (int) ceil( $result['total'] / 20 ) );
		?>
		<div class="wrap asrs-recruit-admin-wrap">
			<div class="asrs-admin-header">
				<div>
					<h1><?php esc_html_e( 'Job Applications', 'asrs-recruit-engine' ); ?></h1>
					<p class="description"><?php esc_html_e( 'All applications submitted through [asrs_careers] are stored safely here and synced to Zoho Recruit (Candidate + Resume + Job Association).', 'asrs-recruit-engine' ); ?></p>
				</div>
				<div class="asrs-admin-stats">
					<span class="asrs-stat-pill"><?php echo esc_html( sprintf( __( 'Total: %d', 'asrs-recruit-engine' ), $counts['total'] ) ); ?></span>
					<span class="asrs-stat-pill asrs-pill-synced"><?php echo esc_html( sprintf( __( 'Synced: %d', 'asrs-recruit-engine' ), $counts['synced'] ) ); ?></span>
					<span class="asrs-stat-pill asrs-pill-failed"><?php echo esc_html( sprintf( __( 'Failed: %d', 'asrs-recruit-engine' ), $counts['failed'] ) ); ?></span>
				</div>
			</div>

			<form method="get" class="asrs-admin-filter-bar">
				<input type="hidden" name="page" value="asrs-recruit-applications" />
				<select name="sync_status">
					<option value=""><?php esc_html_e( 'All Sync Statuses', 'asrs-recruit-engine' ); ?></option>
					<option value="synced" <?php selected( $status, 'synced' ); ?>><?php esc_html_e( 'Synced', 'asrs-recruit-engine' ); ?></option>
					<option value="pending" <?php selected( $status, 'pending' ); ?>><?php esc_html_e( 'Pending', 'asrs-recruit-engine' ); ?></option>
					<option value="failed" <?php selected( $status, 'failed' ); ?>><?php esc_html_e( 'Failed', 'asrs-recruit-engine' ); ?></option>
				</select>
				<input type="search" name="s" value="<?php echo esc_attr( $search ); ?>" placeholder="<?php esc_attr_e( 'Search candidate, email, or job...', 'asrs-recruit-engine' ); ?>" />
				<button type="submit" class="button"><?php esc_html_e( 'Filter', 'asrs-recruit-engine' ); ?></button>
			</form>

			<table class="wp-list-table widefat fixed striped asrs-recruit-table">
				<thead>
					<tr>
						<th style="width:60px;"><?php esc_html_e( 'ID', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Candidate', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Position', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Resume', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Zoho Recruit Sync', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Submitted', 'asrs-recruit-engine' ); ?></th>
						<th style="width:170px;"><?php esc_html_e( 'Actions', 'asrs-recruit-engine' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $items ) ) : ?>
						<tr>
							<td colspan="7"><?php esc_html_e( 'No job applications received yet.', 'asrs-recruit-engine' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $items as $app ) : ?>
							<?php
							$app_id       = (int) $app['id'];
							$dl_url       = wp_nonce_url(
								admin_url( 'admin-post.php?action=asrs_recruit_download_resume&app_id=' . $app_id ),
								'asrs_download_resume_' . $app_id
							);
							$sync_status  = (string) $app['sync_status'];
							?>
							<tr data-app-row="<?php echo esc_attr( (string) $app_id ); ?>">
								<td>#<?php echo esc_html( (string) $app_id ); ?></td>
								<td>
									<strong><?php echo esc_html( $app['first_name'] . ' ' . $app['last_name'] ); ?></strong><br />
									<a href="mailto:<?php echo esc_attr( (string) $app['email'] ); ?>"><?php echo esc_html( (string) $app['email'] ); ?></a><br />
									<span class="description"><?php echo esc_html( (string) $app['phone'] ); ?><?php echo '' !== (string) $app['city'] ? ' · ' . esc_html( (string) $app['city'] ) : ''; ?></span>
								</td>
								<td>
									<strong><?php echo esc_html( (string) $app['job_title'] ); ?></strong>
									<?php if ( '' !== (string) $app['experience_years'] ) : ?>
										<br /><span class="description"><?php echo esc_html( sprintf( __( '%s yrs exp', 'asrs-recruit-engine' ), (string) $app['experience_years'] ) ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<?php if ( ! empty( $app['resume_name'] ) ) : ?>
										<a class="button button-small" href="<?php echo esc_url( $dl_url ); ?>"><?php echo esc_html( (string) $app['resume_name'] ); ?></a>
									<?php else : ?>
										—
									<?php endif; ?>
								</td>
								<td>
									<span class="asrs-sync-badge asrs-sync-<?php echo esc_attr( $sync_status ); ?>">
										<?php echo esc_html( ucfirst( $sync_status ) ); ?>
									</span>
									<div class="asrs-step-pills">
										<span class="<?php echo ! empty( $app['zoho_candidate_id'] ) ? 'done' : 'wait'; ?>"><?php esc_html_e( 'Candidate', 'asrs-recruit-engine' ); ?></span>
										<span class="<?php echo ! empty( $app['resume_uploaded'] ) ? 'done' : 'wait'; ?>"><?php esc_html_e( 'Resume', 'asrs-recruit-engine' ); ?></span>
										<span class="<?php echo ( ! empty( $app['job_associated'] ) || empty( $app['job_id'] ) ) ? 'done' : 'wait'; ?>"><?php esc_html_e( 'Job Link', 'asrs-recruit-engine' ); ?></span>
									</div>
									<?php
									if ( ! empty( $app['zoho_candidate_id'] ) ) :
										$zoho_cfg   = $this->settings->get_zoho_settings();
										$org_id     = (string) get_option( 'asrs_recruit_org_id', '' );
										$record_url = RecruitDataCenter::build_candidate_url(
											(string) $zoho_cfg['data_center'],
											(string) $app['zoho_candidate_id'],
											$org_id
										);
									?>
										<div><a href="<?php echo esc_url( $record_url ); ?>" target="_blank" rel="noopener noreferrer"><?php esc_html_e( 'Open in Zoho Recruit ↗', 'asrs-recruit-engine' ); ?></a></div>
									<?php endif; ?>
									<?php if ( ! empty( $app['last_sync_error'] ) ) : ?>
										<div class="asrs-sync-error"><?php echo esc_html( (string) $app['last_sync_error'] ); ?></div>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( get_date_from_gmt( (string) $app['created_at'], 'Y-m-d H:i' ) ); ?></td>
								<td>
									<?php if ( 'synced' !== $sync_status ) : ?>
										<button type="button" class="button button-small button-primary asrs-retry-app" data-app-id="<?php echo esc_attr( (string) $app_id ); ?>">
											<?php esc_html_e( 'Retry Sync', 'asrs-recruit-engine' ); ?>
										</button>
									<?php endif; ?>
									<button type="button" class="button button-small asrs-delete-app" data-app-id="<?php echo esc_attr( (string) $app_id ); ?>">
										<?php esc_html_e( 'Delete', 'asrs-recruit-engine' ); ?>
									</button>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>

			<?php if ( $pages > 1 ) : ?>
				<div class="tablenav bottom">
					<div class="tablenav-pages">
						<?php
						echo wp_kses_post(
							paginate_links(
								array(
									'base'    => add_query_arg( 'paged', '%#%' ),
									'format'  => '',
									'current' => $paged,
									'total'   => $pages,
								)
							)
						);
						?>
					</div>
				</div>
			<?php endif; ?>
		</div>
		<?php
	}
}
