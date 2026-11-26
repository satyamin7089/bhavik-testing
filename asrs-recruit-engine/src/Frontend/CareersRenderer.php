<?php
/**
 * White-label Frontend Careers Portal & Application Form Renderer.
 *
 * @package ASRS\RecruitEngine\Frontend
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Frontend;

use ASRS\RecruitEngine\Core\Settings;
use ASRS\RecruitEngine\Zoho\RecruitService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class CareersRenderer {

	private RecruitService $recruit;
	private Settings $settings;
	private AssetsManager $assets;

	public function __construct( RecruitService $recruit, Settings $settings, AssetsManager $assets ) {
		$this->recruit  = $recruit;
		$this->settings = $settings;
		$this->assets   = $assets;
	}

	/**
	 * Render full white-label Careers Portal ([asrs_careers]).
	 *
	 * @param array<string, string> $atts
	 */
	public function render_portal( array $atts = array() ): string {
		$this->assets->enqueue_frontend();

		$selected_job_id = isset( $_GET['job'] ) ? sanitize_text_field( wp_unslash( (string) $_GET['job'] ) ) : ''; // phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( '' !== $selected_job_id ) {
			$job = $this->recruit->find_cached_job( $selected_job_id );
			if ( $job ) {
				return $this->render_job_detail( $job );
			}
		}

		$cached = $this->recruit->get_cached_jobs();
		$jobs   = $cached['jobs'];

		if ( ! empty( $atts['department'] ) ) {
			$dept_filter = mb_strtolower( trim( $atts['department'] ) );
			$jobs        = array_values(
				array_filter(
					$jobs,
					static fn( array $j ): bool => mb_strtolower( (string) $j['department'] ) === $dept_filter
				)
			);
		}

		$departments = array_values( array_unique( array_filter( array_map( static fn( array $j ): string => (string) ( $j['department'] ?? '' ), $jobs ) ) ) );
		$locations   = array_values( array_unique( array_filter( array_map( fn( array $j ): string => $this->format_location( $j ), $jobs ) ) ) );
		$job_types   = array_values( array_unique( array_filter( array_map( static fn( array $j ): string => (string) ( $j['job_type'] ?? '' ), $jobs ) ) ) );

		sort( $departments );
		sort( $locations );
		sort( $job_types );

		$heading  = $atts['heading'] ?? (string) $this->settings->get( 'careers_heading', __( 'Open Positions', 'asrs-recruit-engine' ) );
		$subtitle = $atts['subtitle'] ?? (string) $this->settings->get( 'careers_subtitle', __( 'Explore current opportunities and apply directly online.', 'asrs-recruit-engine' ) );

		ob_start();
		?>
		<div class="asrs-recruit-wrapper" data-asrs-careers>
			<div class="asrs-recruit-hero">
				<div class="asrs-recruit-hero-text">
					<h2 class="asrs-recruit-title"><?php echo esc_html( $heading ); ?></h2>
					<?php if ( '' !== $subtitle ) : ?>
						<p class="asrs-recruit-subtitle"><?php echo esc_html( $subtitle ); ?></p>
					<?php endif; ?>
				</div>
				<div class="asrs-recruit-count-pill">
					<span data-asrs-visible-count><?php echo esc_html( (string) count( $jobs ) ); ?></span>
					<?php echo esc_html( _n( 'Open Role', 'Open Roles', count( $jobs ), 'asrs-recruit-engine' ) ); ?>
				</div>
			</div>

			<?php if ( ! empty( $jobs ) ) : ?>
				<div class="asrs-recruit-filters">
					<div class="asrs-filter-field asrs-filter-search">
						<input type="search" class="asrs-recruit-input" data-asrs-filter="search" placeholder="<?php esc_attr_e( 'Search job title or keyword...', 'asrs-recruit-engine' ); ?>" />
					</div>
					<?php if ( ! empty( $departments ) ) : ?>
						<div class="asrs-filter-field">
							<select class="asrs-recruit-input" data-asrs-filter="department">
								<option value=""><?php esc_html_e( 'All Departments', 'asrs-recruit-engine' ); ?></option>
								<?php foreach ( $departments as $dept ) : ?>
									<option value="<?php echo esc_attr( mb_strtolower( $dept ) ); ?>"><?php echo esc_html( $dept ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $locations ) ) : ?>
						<div class="asrs-filter-field">
							<select class="asrs-recruit-input" data-asrs-filter="location">
								<option value=""><?php esc_html_e( 'All Locations', 'asrs-recruit-engine' ); ?></option>
								<?php foreach ( $locations as $loc ) : ?>
									<option value="<?php echo esc_attr( mb_strtolower( $loc ) ); ?>"><?php echo esc_html( $loc ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
					<?php if ( ! empty( $job_types ) ) : ?>
						<div class="asrs-filter-field">
							<select class="asrs-recruit-input" data-asrs-filter="type">
								<option value=""><?php esc_html_e( 'All Job Types', 'asrs-recruit-engine' ); ?></option>
								<?php foreach ( $job_types as $type ) : ?>
									<option value="<?php echo esc_attr( mb_strtolower( $type ) ); ?>"><?php echo esc_html( $type ); ?></option>
								<?php endforeach; ?>
							</select>
						</div>
					<?php endif; ?>
				</div>

				<div class="asrs-jobs-grid" data-asrs-jobs-grid>
					<?php foreach ( $jobs as $job ) : ?>
						<?php
						$loc      = $this->format_location( $job );
						$dept     = (string) ( $job['department'] ?? '' );
						$type     = (string) ( $job['job_type'] ?? '' );
						$exp      = (string) ( $job['experience'] ?? '' );
						$summary  = wp_trim_words( wp_strip_all_tags( (string) ( $job['description'] ?? '' ) ), 24, '...' );
						$job_url  = add_query_arg( 'job', rawurlencode( (string) $job['id'] ), get_permalink() ?: home_url( '/' ) );
						$keywords = mb_strtolower( trim( $job['title'] . ' ' . $dept . ' ' . $loc . ' ' . $type . ' ' . $summary ) );
						?>
						<article
							class="asrs-job-card"
							data-asrs-job-card
							data-keywords="<?php echo esc_attr( $keywords ); ?>"
							data-department="<?php echo esc_attr( mb_strtolower( $dept ) ); ?>"
							data-location="<?php echo esc_attr( mb_strtolower( $loc ) ); ?>"
							data-type="<?php echo esc_attr( mb_strtolower( $type ) ); ?>"
						>
							<div class="asrs-job-card-top">
								<?php if ( '' !== $dept ) : ?>
									<span class="asrs-job-badge"><?php echo esc_html( $dept ); ?></span>
								<?php endif; ?>
								<?php if ( ! empty( $job['remote'] ) ) : ?>
									<span class="asrs-job-badge asrs-job-badge-remote"><?php esc_html_e( 'Remote', 'asrs-recruit-engine' ); ?></span>
								<?php endif; ?>
							</div>

							<h3 class="asrs-job-card-title">
								<a href="<?php echo esc_url( $job_url ); ?>"><?php echo esc_html( (string) $job['title'] ); ?></a>
							</h3>

							<div class="asrs-job-meta">
								<?php if ( '' !== $loc ) : ?>
									<span class="asrs-job-meta-item"><?php echo esc_html( $loc ); ?></span>
								<?php endif; ?>
								<?php if ( '' !== $type ) : ?>
									<span class="asrs-job-meta-item"><?php echo esc_html( $type ); ?></span>
								<?php endif; ?>
								<?php if ( '' !== $exp ) : ?>
									<span class="asrs-job-meta-item"><?php echo esc_html( $exp ); ?></span>
								<?php endif; ?>
							</div>

							<?php if ( '' !== $summary ) : ?>
								<p class="asrs-job-excerpt"><?php echo esc_html( $summary ); ?></p>
							<?php endif; ?>

							<div class="asrs-job-card-footer">
								<a class="asrs-recruit-btn" href="<?php echo esc_url( $job_url ); ?>">
									<span class="asrs-btn-label"><?php esc_html_e( 'View & Apply', 'asrs-recruit-engine' ); ?></span>
									<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
								</a>
							</div>
						</article>
					<?php endforeach; ?>
				</div>

				<div class="asrs-recruit-empty" data-asrs-no-results hidden>
					<p><?php esc_html_e( 'No positions match your selected filters. Try clearing your search.', 'asrs-recruit-engine' ); ?></p>
				</div>
			<?php else : ?>
				<div class="asrs-recruit-empty">
					<p><?php esc_html_e( 'There are currently no open positions listed. You can still submit a general application below and our team will keep your profile on file.', 'asrs-recruit-engine' ); ?></p>
				</div>
				<?php echo $this->render_application_form( '', __( 'General Application', 'asrs-recruit-engine' ) ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			<?php endif; ?>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render Job Detail + Application Form for a specific position.
	 *
	 * @param array<string, mixed> $job
	 */
	public function render_job_detail( array $job ): string {
		$this->assets->enqueue_frontend();

		$back_url = remove_query_arg( 'job', get_permalink() ?: home_url( '/' ) );
		$loc      = $this->format_location( $job );

		ob_start();
		?>
		<div class="asrs-recruit-wrapper asrs-recruit-detail-wrap">
			<div class="asrs-recruit-back-row">
				<a class="asrs-recruit-back-link" href="<?php echo esc_url( $back_url ); ?>">
					<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
					<span><?php esc_html_e( 'Back to all open positions', 'asrs-recruit-engine' ); ?></span>
				</a>
			</div>

			<div class="asrs-recruit-detail-layout">
				<div class="asrs-recruit-job-info">
					<div class="asrs-job-card-top">
						<?php if ( ! empty( $job['department'] ) ) : ?>
							<span class="asrs-job-badge"><?php echo esc_html( (string) $job['department'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $job['remote'] ) ) : ?>
							<span class="asrs-job-badge asrs-job-badge-remote"><?php esc_html_e( 'Remote', 'asrs-recruit-engine' ); ?></span>
						<?php endif; ?>
					</div>

					<h2 class="asrs-recruit-title"><?php echo esc_html( (string) $job['title'] ); ?></h2>

					<div class="asrs-job-meta asrs-job-meta-detail">
						<?php if ( '' !== $loc ) : ?>
							<span class="asrs-job-meta-item"><strong><?php esc_html_e( 'Location:', 'asrs-recruit-engine' ); ?></strong> <?php echo esc_html( $loc ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $job['job_type'] ) ) : ?>
							<span class="asrs-job-meta-item"><strong><?php esc_html_e( 'Type:', 'asrs-recruit-engine' ); ?></strong> <?php echo esc_html( (string) $job['job_type'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $job['experience'] ) ) : ?>
							<span class="asrs-job-meta-item"><strong><?php esc_html_e( 'Experience:', 'asrs-recruit-engine' ); ?></strong> <?php echo esc_html( (string) $job['experience'] ); ?></span>
						<?php endif; ?>
						<?php if ( ! empty( $job['salary'] ) ) : ?>
							<span class="asrs-job-meta-item"><strong><?php esc_html_e( 'Compensation:', 'asrs-recruit-engine' ); ?></strong> <?php echo esc_html( (string) $job['salary'] ); ?></span>
						<?php endif; ?>
					</div>

					<?php if ( ! empty( $job['description'] ) ) : ?>
						<div class="asrs-job-section">
							<h3><?php esc_html_e( 'Role Overview', 'asrs-recruit-engine' ); ?></h3>
							<div class="asrs-job-rich-text"><?php echo wp_kses_post( wpautop( (string) $job['description'] ) ); ?></div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $job['requirements'] ) ) : ?>
						<div class="asrs-job-section">
							<h3><?php esc_html_e( 'Requirements', 'asrs-recruit-engine' ); ?></h3>
							<div class="asrs-job-rich-text"><?php echo wp_kses_post( wpautop( (string) $job['requirements'] ) ); ?></div>
						</div>
					<?php endif; ?>

					<?php if ( ! empty( $job['benefits'] ) ) : ?>
						<div class="asrs-job-section">
							<h3><?php esc_html_e( 'Benefits', 'asrs-recruit-engine' ); ?></h3>
							<div class="asrs-job-rich-text"><?php echo wp_kses_post( wpautop( (string) $job['benefits'] ) ); ?></div>
						</div>
					<?php endif; ?>
				</div>

				<div class="asrs-recruit-apply-col">
					<?php echo $this->render_application_form( (string) $job['id'], (string) $job['title'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>
			</div>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * Render standalone application form ([asrs_careers_apply]).
	 */
	public function render_application_form( string $job_id = '', string $job_title = '' ): string {
		$this->assets->enqueue_frontend();
		$max_mb = max( 1, (int) $this->settings->get( 'max_resume_mb', 5 ) );

		ob_start();
		?>
		<div class="asrs-apply-card">
			<h3 class="asrs-apply-heading">
				<?php
				echo '' !== $job_title
					? esc_html( sprintf( __( 'Apply for %s', 'asrs-recruit-engine' ), $job_title ) )
					: esc_html__( 'Submit Your Application', 'asrs-recruit-engine' );
				?>
			</h3>
			<p class="asrs-apply-subheading"><?php esc_html_e( 'Fields marked with * are required.', 'asrs-recruit-engine' ); ?></p>

			<form class="asrs-recruit-form" method="post" enctype="multipart/form-data" novalidate>
				<input type="hidden" name="action" value="asrs_submit_application" />
				<input type="hidden" name="asrs_recruit_nonce" value="<?php echo esc_attr( wp_create_nonce( 'asrs_recruit_apply' ) ); ?>" />
				<input type="hidden" name="asrs_rendered_at" value="<?php echo esc_attr( (string) time() ); ?>" />
				<input type="hidden" name="job_id" value="<?php echo esc_attr( $job_id ); ?>" />
				<input type="hidden" name="job_title" value="<?php echo esc_attr( $job_title ); ?>" />

				<div class="asrs-hp-field" aria-hidden="true">
					<label><?php esc_html_e( 'Website', 'asrs-recruit-engine' ); ?></label>
					<input type="text" name="asrs_website_hp" value="" tabindex="-1" autocomplete="off" />
				</div>

				<div class="asrs-recruit-grid">
					<div class="asrs-field-group">
						<label class="asrs-recruit-label" for="asrs-first-name"><?php esc_html_e( 'First Name *', 'asrs-recruit-engine' ); ?></label>
						<input class="asrs-recruit-input" id="asrs-first-name" type="text" name="first_name" placeholder="<?php esc_attr_e( 'Enter first name', 'asrs-recruit-engine' ); ?>" required />
						<div class="asrs-field-error" data-error-for="first_name"></div>
					</div>

					<div class="asrs-field-group">
						<label class="asrs-recruit-label" for="asrs-last-name"><?php esc_html_e( 'Last Name *', 'asrs-recruit-engine' ); ?></label>
						<input class="asrs-recruit-input" id="asrs-last-name" type="text" name="last_name" placeholder="<?php esc_attr_e( 'Enter last name', 'asrs-recruit-engine' ); ?>" required />
						<div class="asrs-field-error" data-error-for="last_name"></div>
					</div>

					<div class="asrs-field-group">
						<label class="asrs-recruit-label" for="asrs-email"><?php esc_html_e( 'Email Address *', 'asrs-recruit-engine' ); ?></label>
						<input class="asrs-recruit-input" id="asrs-email" type="email" name="email" placeholder="you@example.com" required />
						<div class="asrs-field-error" data-error-for="email"></div>
					</div>

					<div class="asrs-field-group">
						<label class="asrs-recruit-label" for="asrs-phone"><?php esc_html_e( 'Phone Number *', 'asrs-recruit-engine' ); ?></label>
						<input class="asrs-recruit-input" id="asrs-phone" type="tel" name="phone" placeholder="+91 98765 43210" required />
						<div class="asrs-field-error" data-error-for="phone"></div>
					</div>

					<div class="asrs-field-group">
						<label class="asrs-recruit-label" for="asrs-city"><?php esc_html_e( 'Current City', 'asrs-recruit-engine' ); ?></label>
						<input class="asrs-recruit-input" id="asrs-city" type="text" name="city" placeholder="<?php esc_attr_e( 'e.g. Kanpur, Mumbai, Bengaluru', 'asrs-recruit-engine' ); ?>" />
					</div>

					<div class="asrs-field-group">
						<label class="asrs-recruit-label" for="asrs-exp"><?php esc_html_e( 'Total Experience (Years)', 'asrs-recruit-engine' ); ?></label>
						<input class="asrs-recruit-input" id="asrs-exp" type="text" inputmode="decimal" name="experience_years" placeholder="e.g. 4.5" autocomplete="off" />
					</div>

					<div class="asrs-field-group asrs-field-full">
						<label class="asrs-recruit-label" for="asrs-linkedin"><?php esc_html_e( 'LinkedIn / Portfolio URL', 'asrs-recruit-engine' ); ?></label>
						<input class="asrs-recruit-input" id="asrs-linkedin" type="url" name="linkedin_url" placeholder="https://linkedin.com/in/yourprofile" />
					</div>

					<div class="asrs-field-group asrs-field-full">
						<label class="asrs-recruit-label" for="asrs-resume"><?php esc_html_e( 'Resume / CV (PDF, DOC, DOCX) *', 'asrs-recruit-engine' ); ?></label>
						<label class="asrs-file-dropzone" for="asrs-resume">
							<input class="asrs-file-input" id="asrs-resume" type="file" name="resume" accept=".pdf,.doc,.docx,application/pdf,application/msword,application/vnd.openxmlformats-officedocument.wordprocessingml.document" required />
							<span class="asrs-file-drop-title" data-asrs-file-label><?php esc_html_e( 'Click to choose your resume file', 'asrs-recruit-engine' ); ?></span>
							<span class="asrs-file-drop-hint"><?php echo esc_html( sprintf( __( 'Accepted: PDF, DOC, DOCX (max %d MB)', 'asrs-recruit-engine' ), $max_mb ) ); ?></span>
						</label>
						<div class="asrs-field-error" data-error-for="resume"></div>
					</div>

					<div class="asrs-field-group asrs-field-full">
						<label class="asrs-recruit-label" for="asrs-cover"><?php esc_html_e( 'Cover Note / Key Highlights', 'asrs-recruit-engine' ); ?></label>
						<textarea class="asrs-recruit-input asrs-recruit-textarea" id="asrs-cover" name="cover_note" rows="4" placeholder="<?php esc_attr_e( 'Briefly share your relevant experience, notice period, or anything else you would like us to know...', 'asrs-recruit-engine' ); ?>"></textarea>
					</div>

					<?php if ( $this->settings->is_turnstile_enabled() ) : ?>
						<div class="asrs-field-group asrs-field-full">
							<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( (string) $this->settings->get( 'turnstile_site_key', '' ) ); ?>"></div>
						</div>
					<?php endif; ?>

					<?php if ( $this->settings->is_recaptcha_enabled() ) : ?>
						<?php if ( 'v3' === $this->settings->get_recaptcha_version() ) : ?>
							<input type="hidden" name="g-recaptcha-response" class="asrs-recaptcha-token" value="" />
						<?php else : ?>
							<div class="asrs-field-group asrs-field-full asrs-recruit-captcha">
								<div class="asrs-g-recaptcha" data-sitekey="<?php echo esc_attr( trim( (string) $this->settings->get( 'recaptcha_site_key', '' ) ) ); ?>"></div>
								<div class="asrs-field-error" data-error-for="recaptcha"></div>
							</div>
						<?php endif; ?>
					<?php endif; ?>
				</div>

				<div class="asrs-form-feedback" data-asrs-feedback hidden></div>

				<div class="asrs-form-actions">
					<button type="submit" class="asrs-recruit-btn asrs-recruit-submit">
						<span class="asrs-btn-label"><?php esc_html_e( 'Submit Application', 'asrs-recruit-engine' ); ?></span>
						<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
					</button>
				</div>
			</form>
		</div>
		<?php
		return (string) ob_get_clean();
	}

	/**
	 * @param array<string, mixed> $job
	 */
	private function format_location( array $job ): string {
		$parts = array_filter(
			array(
				(string) ( $job['city'] ?? '' ),
				(string) ( $job['state'] ?? '' ),
				(string) ( $job['country'] ?? '' ),
			)
		);
		if ( ! empty( $parts ) ) {
			return implode( ', ', $parts );
		}
		return ! empty( $job['remote'] ) ? __( 'Remote', 'asrs-recruit-engine' ) : '';
	}
}
