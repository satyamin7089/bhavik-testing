<?php
/**
 * Base class for ASRS Forms Engine admin pages.
 *
 * @package ASRS\FormsEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Admin\Pages;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

abstract class AbstractPage {

	/**
	 * Render page output.
	 */
	abstract public function render(): void;

	/**
	 * Render unified admin page header.
	 *
	 * @param string $title       Page title.
	 * @param string $subtitle    Page subtitle.
	 * @param string $actions_html Optional right-hand action buttons HTML.
	 */
	protected function render_header( string $title, string $subtitle = '', string $actions_html = '' ): void {
		?>
		<div class="wrap asrs-admin-wrap">
			<div class="asrs-admin-header">
				<div>
					<h1 class="asrs-admin-title"><?php echo esc_html( $title ); ?></h1>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="asrs-admin-subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</div>
				<?php if ( '' !== $actions_html ) : ?>
					<div class="asrs-admin-header-actions">
						<?php echo $actions_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
					</div>
				<?php endif; ?>
			</div>
		<?php
	}

	/**
	 * Close the admin wrapper.
	 */
	protected function render_footer(): void {
		echo '</div>';
	}

	/**
	 * Render a colored status badge.
	 *
	 * @param string $status Status string.
	 */
	protected function render_status_badge( string $status ): string {
		$key = sanitize_key( $status );
		$map = array(
			'synced'       => array( 'class' => 'asrs-badge-success', 'label' => __( 'Synced', 'asrs-forms-engine' ) ),
			'connected'    => array( 'class' => 'asrs-badge-success', 'label' => __( 'Connected', 'asrs-forms-engine' ) ),
			'success'      => array( 'class' => 'asrs-badge-success', 'label' => __( 'Success', 'asrs-forms-engine' ) ),
			'completed'    => array( 'class' => 'asrs-badge-success', 'label' => __( 'Completed', 'asrs-forms-engine' ) ),
			'pending'      => array( 'class' => 'asrs-badge-warning', 'label' => __( 'Pending', 'asrs-forms-engine' ) ),
			'active'       => array( 'class' => 'asrs-badge-info', 'label' => __( 'Active Draft', 'asrs-forms-engine' ) ),
			'failed'       => array( 'class' => 'asrs-badge-danger', 'label' => __( 'Failed', 'asrs-forms-engine' ) ),
			'error'        => array( 'class' => 'asrs-badge-danger', 'label' => __( 'Error', 'asrs-forms-engine' ) ),
			'abandoned'    => array( 'class' => 'asrs-badge-warning', 'label' => __( 'Abandoned', 'asrs-forms-engine' ) ),
			'disconnected' => array( 'class' => 'asrs-badge-muted', 'label' => __( 'Disconnected', 'asrs-forms-engine' ) ),
			'unknown'      => array( 'class' => 'asrs-badge-muted', 'label' => __( 'Not Tested', 'asrs-forms-engine' ) ),
		);

		$meta = $map[ $key ] ?? array(
			'class' => 'asrs-badge-muted',
			'label' => ucfirst( $status ),
		);

		return sprintf(
			'<span class="asrs-badge %s">%s</span>',
			esc_attr( $meta['class'] ),
			esc_html( $meta['label'] )
		);
	}

	/**
	 * Render pagination controls.
	 *
	 * @param int    $total        Total records.
	 * @param int    $per_page     Items per page.
	 * @param int    $current_page Current page index (1-based).
	 * @param string $base_url     Base admin URL preserving filters.
	 */
	protected function render_pagination( int $total, int $per_page, int $current_page, string $base_url ): void {
		$total_pages = (int) ceil( $total / max( 1, $per_page ) );
		if ( $total_pages <= 1 ) {
			return;
		}

		$links = paginate_links(
			array(
				'base'      => add_query_arg( 'paged', '%#%', $base_url ),
				'format'    => '',
				'prev_text' => '&laquo;',
				'next_text' => '&raquo;',
				'total'     => $total_pages,
				'current'   => $current_page,
			)
		);

		if ( $links ) {
			echo '<div class="tablenav bottom"><div class="tablenav-pages">' . $links . '</div></div>'; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		}
	}

	/**
	 * Format UTC MySQL datetime in WP site timezone.
	 *
	 * @param string|null $utc_datetime UTC datetime.
	 */
	protected function format_datetime( ?string $utc_datetime ): string {
		if ( empty( $utc_datetime ) ) {
			return '—';
		}

		$ts = strtotime( $utc_datetime . ' UTC' );
		if ( false === $ts ) {
			return esc_html( $utc_datetime );
		}

		return esc_html( wp_date( 'Y-m-d H:i:s', $ts ) );
	}
}
