<?php
/**
 * Admin Screen 2: Cached Job Openings & Sync Trigger.
 *
 * @package ASRS\RecruitEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Admin\Pages;

use ASRS\RecruitEngine\Zoho\RecruitService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class JobOpeningsPage {

	private RecruitService $recruit;

	public function __construct( RecruitService $recruit ) {
		$this->recruit = $recruit;
	}

	public function render(): void {
		$cached = $this->recruit->get_cached_jobs();
		$jobs   = $cached['jobs'];
		?>
		<div class="wrap asrs-recruit-admin-wrap">
			<div class="asrs-admin-header">
				<div>
					<h1><?php esc_html_e( 'Job Openings (Synced from Zoho Recruit)', 'asrs-recruit-engine' ); ?></h1>
					<p class="description">
						<?php esc_html_e( 'Embed your white-label careers portal on any WordPress page with:', 'asrs-recruit-engine' ); ?>
						<code>[asrs_careers]</code>
					</p>
				</div>
				<div>
					<button type="button" class="button button-primary" id="asrs-sync-jobs-btn">
						<?php esc_html_e( 'Sync Jobs Now', 'asrs-recruit-engine' ); ?>
					</button>
				</div>
			</div>

			<div id="asrs-sync-jobs-notice" class="notice" style="display:none;"><p></p></div>

			<p class="description">
				<?php
				if ( '' !== $cached['synced_at'] ) {
					echo esc_html( sprintf( __( 'Last synced (UTC): %s · %d job opening(s) cached locally (auto-refreshes hourly).', 'asrs-recruit-engine' ), $cached['synced_at'], count( $jobs ) ) );
				} else {
					esc_html_e( 'No jobs synced yet. Connect Zoho Recruit in Settings, then click "Sync Jobs Now".', 'asrs-recruit-engine' );
				}
				?>
			</p>

			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Job Title', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Department', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Location', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Type', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Status', 'asrs-recruit-engine' ); ?></th>
						<th><?php esc_html_e( 'Direct Shortcode / Query', 'asrs-recruit-engine' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $jobs ) ) : ?>
						<tr>
							<td colspan="6"><?php esc_html_e( 'No active job openings cached.', 'asrs-recruit-engine' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $jobs as $job ) : ?>
							<?php
							$loc = implode( ', ', array_filter( array( (string) $job['city'], (string) $job['state'], (string) $job['country'] ) ) );
							if ( '' === $loc && ! empty( $job['remote'] ) ) {
								$loc = __( 'Remote', 'asrs-recruit-engine' );
							}
							?>
							<tr>
								<td><strong><?php echo esc_html( (string) $job['title'] ); ?></strong><br /><code>ID: <?php echo esc_html( (string) $job['id'] ); ?></code></td>
								<td><?php echo esc_html( (string) ( $job['department'] ?: '—' ) ); ?></td>
								<td><?php echo esc_html( $loc ?: '—' ); ?></td>
								<td><?php echo esc_html( (string) ( $job['job_type'] ?: '—' ) ); ?></td>
								<td><?php echo esc_html( (string) ( $job['status'] ?: '—' ) ); ?></td>
								<td>
									<code>[asrs_careers_apply job_id="<?php echo esc_attr( (string) $job['id'] ); ?>"]</code><br />
									<span class="description"><?php echo esc_html( '?job=' . (string) $job['id'] ); ?></span>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>
		<?php
	}
}
