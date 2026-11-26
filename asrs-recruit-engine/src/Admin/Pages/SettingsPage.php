<?php
/**
 * Admin Screen 3: Zoho Recruit OAuth Connection & Portal Settings.
 *
 * @package ASRS\RecruitEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Admin\Pages;

use ASRS\RecruitEngine\Core\Settings;
use ASRS\RecruitEngine\Zoho\RecruitAuthService;
use ASRS\RecruitEngine\Zoho\RecruitDataCenter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsPage {

	private Settings $settings;
	private RecruitAuthService $auth;

	public function __construct( Settings $settings, RecruitAuthService $auth ) {
		$this->settings = $settings;
		$this->auth     = $auth;
	}

	public function render(): void {
		$saved_notice = false;
		if ( isset( $_POST['asrs_recruit_save_settings'] ) && check_admin_referer( 'asrs_recruit_save_settings_nonce' ) ) {
			$this->handle_save();
			$saved_notice = true;
		}

		$zoho     = $this->settings->get_zoho_settings();
		$general  = $this->settings->get_all();
		$status   = $this->auth->get_token_status();
		$dcs      = RecruitDataCenter::all();
		?>
		<div class="wrap asrs-recruit-admin-wrap">
			<h1><?php esc_html_e( 'Zoho Recruit & Careers Settings', 'asrs-recruit-engine' ); ?></h1>

			<?php if ( $saved_notice ) : ?>
				<div class="notice notice-success is-dismissible"><p><?php esc_html_e( 'Settings saved.', 'asrs-recruit-engine' ); ?></p></div>
			<?php endif; ?>

			<div class="asrs-admin-card">
				<h2><?php esc_html_e( 'Zoho Recruit OAuth 2.0 Connection', 'asrs-recruit-engine' ); ?></h2>
				<p class="description">
					<?php esc_html_e( 'Required Zoho API Console Scopes:', 'asrs-recruit-engine' ); ?>
					<code>ZohoRecruit.modules.ALL,ZohoRecruit.settings.ALL,ZohoRecruit.org.ALL</code>
				</p>

				<div class="asrs-conn-status">
					<strong><?php esc_html_e( 'Status:', 'asrs-recruit-engine' ); ?></strong>
					<?php if ( 'connected' === $status['last_test_status'] ) : ?>
						<span class="asrs-sync-badge asrs-sync-synced"><?php esc_html_e( 'Connected', 'asrs-recruit-engine' ); ?></span>
					<?php elseif ( 'failed' === $status['last_test_status'] ) : ?>
						<span class="asrs-sync-badge asrs-sync-failed"><?php esc_html_e( 'Connection Failed', 'asrs-recruit-engine' ); ?></span>
						<span class="asrs-sync-error"><?php echo esc_html( (string) $status['last_test_error'] ); ?></span>
					<?php else : ?>
						<span class="asrs-sync-badge asrs-sync-pending"><?php esc_html_e( 'Not Tested', 'asrs-recruit-engine' ); ?></span>
					<?php endif; ?>
				</div>

				<form method="post">
					<?php wp_nonce_field( 'asrs_recruit_save_settings_nonce' ); ?>
					<input type="hidden" name="asrs_recruit_save_settings" value="1" />

					<table class="form-table">
						<tr>
							<th scope="row"><?php esc_html_e( 'Enable Zoho Recruit Sync', 'asrs-recruit-engine' ); ?></th>
							<td><label><input type="checkbox" name="zoho[enabled]" value="1" <?php checked( ! empty( $zoho['enabled'] ) ); ?> /> <?php esc_html_e( 'Automatically sync job applications to Zoho Recruit', 'asrs-recruit-engine' ); ?></label></td>
						</tr>
						<tr>
							<th scope="row"><label for="zoho-dc"><?php esc_html_e( 'Zoho Data Center', 'asrs-recruit-engine' ); ?></label></th>
							<td>
								<select id="zoho-dc" name="zoho[data_center]">
									<?php foreach ( $dcs as $dc_key => $dc_info ) : ?>
										<option value="<?php echo esc_attr( $dc_key ); ?>" <?php selected( (string) $zoho['data_center'], $dc_key ); ?>><?php echo esc_html( $dc_info['label'] ); ?></option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="zoho-cid"><?php esc_html_e( 'Client ID', 'asrs-recruit-engine' ); ?></label></th>
							<td><input id="zoho-cid" type="text" class="regular-text" name="zoho[client_id]" value="<?php echo esc_attr( (string) $zoho['client_id'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="zoho-csec"><?php esc_html_e( 'Client Secret', 'asrs-recruit-engine' ); ?></label></th>
							<td><input id="zoho-csec" type="password" class="regular-text" name="zoho[client_secret]" value="" placeholder="<?php echo '' !== $zoho['client_secret'] ? esc_attr__( 'Saved (leave blank to keep)', 'asrs-recruit-engine' ) : ''; ?>" autocomplete="new-password" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="zoho-rtok"><?php esc_html_e( 'Refresh Token', 'asrs-recruit-engine' ); ?></label></th>
							<td>
								<input id="zoho-rtok" type="password" class="regular-text" name="zoho[refresh_token]" value="" placeholder="<?php echo '' !== $zoho['refresh_token'] ? esc_attr__( 'Saved (leave blank to keep)', 'asrs-recruit-engine' ) : ''; ?>" autocomplete="new-password" />
								<p class="description"><?php esc_html_e( 'Or save Client ID + Client Secret first, then paste a Self Client Grant Token below to auto-generate your Refresh Token.', 'asrs-recruit-engine' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="asrs-grant-token"><?php esc_html_e( 'Quick Self-Client Grant Token', 'asrs-recruit-engine' ); ?></label></th>
							<td>
								<input id="asrs-grant-token" type="text" class="regular-text" placeholder="<?php esc_attr_e( 'Paste 1000.xxxx grant code here', 'asrs-recruit-engine' ); ?>" />
								<button type="button" class="button" id="asrs-exchange-grant-btn"><?php esc_html_e( 'Exchange & Connect', 'asrs-recruit-engine' ); ?></button>
								<button type="button" class="button" id="asrs-test-conn-btn"><?php esc_html_e( 'Test Connection', 'asrs-recruit-engine' ); ?></button>
								<div id="asrs-zoho-ajax-msg" class="description" style="margin-top:6px;"></div>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="zoho-src"><?php esc_html_e( 'Candidate Source', 'asrs-recruit-engine' ); ?></label></th>
							<td><input id="zoho-src" type="text" class="regular-text" name="zoho[candidate_source]" value="<?php echo esc_attr( (string) $zoho['candidate_source'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Trigger Zoho Workflows', 'asrs-recruit-engine' ); ?></th>
							<td><label><input type="checkbox" name="zoho[trigger_workflow]" value="1" <?php checked( ! empty( $zoho['trigger_workflow'] ) ); ?> /> <?php esc_html_e( 'Run Zoho Recruit Workflow Rules (e.g., Candidate auto-acknowledgement & HR alerts) on submission', 'asrs-recruit-engine' ); ?></label></td>
						</tr>
					</table>

					<hr />
					<h2><?php esc_html_e( 'Careers Portal & Security Settings', 'asrs-recruit-engine' ); ?></h2>
					<table class="form-table">
						<tr>
							<th scope="row"><label for="gen-heading"><?php esc_html_e( 'Portal Heading', 'asrs-recruit-engine' ); ?></label></th>
							<td><input id="gen-heading" type="text" class="regular-text" name="general[careers_heading]" value="<?php echo esc_attr( (string) $general['careers_heading'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="gen-sub"><?php esc_html_e( 'Portal Subtitle', 'asrs-recruit-engine' ); ?></label></th>
							<td><input id="gen-sub" type="text" class="large-text" name="general[careers_subtitle]" value="<?php echo esc_attr( (string) $general['careers_subtitle'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><label for="gen-statuses"><?php esc_html_e( 'Allowed Job Opening Statuses', 'asrs-recruit-engine' ); ?></label></th>
							<td>
								<input id="gen-statuses" type="text" class="regular-text" name="general[allowed_job_statuses]" value="<?php echo esc_attr( (string) $general['allowed_job_statuses'] ); ?>" />
								<p class="description"><?php esc_html_e( 'Comma-separated Zoho Job Opening statuses to display (leave empty to show all). Default: In-progress,Waiting for approval,Open', 'asrs-recruit-engine' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Only Published Jobs', 'asrs-recruit-engine' ); ?></th>
							<td><label><input type="checkbox" name="general[only_published_jobs]" value="1" <?php checked( ! empty( $general['only_published_jobs'] ) ); ?> /> <?php esc_html_e( 'Only show jobs where "Publish" is enabled in Zoho Recruit', 'asrs-recruit-engine' ); ?></label></td>
						</tr>
						<tr>
							<th scope="row"><label for="gen-mb"><?php esc_html_e( 'Max Resume Size (MB)', 'asrs-recruit-engine' ); ?></label></th>
							<td><input id="gen-mb" type="number" min="1" max="20" name="general[max_resume_mb]" value="<?php echo esc_attr( (string) $general['max_resume_mb'] ); ?>" /></td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Sync Failure Alert Email', 'asrs-recruit-engine' ); ?></th>
							<td>
								<label><input type="checkbox" name="general[notify_on_sync_failure]" value="1" <?php checked( ! empty( $general['notify_on_sync_failure'] ) ); ?> /> <?php esc_html_e( 'Email admin if Zoho Recruit sync fails', 'asrs-recruit-engine' ); ?></label><br />
								<input type="email" class="regular-text" style="margin-top:6px;" name="general[sync_failure_email]" value="<?php echo esc_attr( (string) $general['sync_failure_email'] ); ?>" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Cloudflare Turnstile (Optional)', 'asrs-recruit-engine' ); ?></th>
							<td>
								<label><input type="checkbox" name="general[turnstile_enabled]" value="1" <?php checked( ! empty( $general['turnstile_enabled'] ) ); ?> /> <?php esc_html_e( 'Enable Turnstile anti-spam on job applications', 'asrs-recruit-engine' ); ?></label><br />
								<input type="text" class="regular-text" style="margin-top:6px;" name="general[turnstile_site_key]" value="<?php echo esc_attr( (string) $general['turnstile_site_key'] ); ?>" placeholder="<?php esc_attr_e( 'Site Key', 'asrs-recruit-engine' ); ?>" /><br />
								<input type="password" class="regular-text" style="margin-top:6px;" name="general[turnstile_secret_key]" value="" placeholder="<?php echo '' !== $general['turnstile_secret_key'] ? esc_attr__( 'Secret Key saved (leave blank to keep)', 'asrs-recruit-engine' ) : esc_attr__( 'Secret Key', 'asrs-recruit-engine' ); ?>" autocomplete="new-password" />
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Google reCAPTCHA (Optional)', 'asrs-recruit-engine' ); ?></th>
							<td>
								<label><input type="checkbox" name="general[recaptcha_enabled]" value="1" <?php checked( ! empty( $general['recaptcha_enabled'] ) ); ?> /> <?php esc_html_e( 'Enable Google reCAPTCHA on job applications', 'asrs-recruit-engine' ); ?></label><br />
								<select name="general[recaptcha_version]" style="margin-top:6px;">
									<option value="v2" <?php selected( (string) ( $general['recaptcha_version'] ?? 'v2' ), 'v2' ); ?>><?php esc_html_e( 'v2 — "I\'m not a robot" checkbox', 'asrs-recruit-engine' ); ?></option>
									<option value="v3" <?php selected( (string) ( $general['recaptcha_version'] ?? 'v2' ), 'v3' ); ?>><?php esc_html_e( 'v3 — invisible score-based', 'asrs-recruit-engine' ); ?></option>
								</select><br />
								<input type="text" class="regular-text" style="margin-top:6px;" name="general[recaptcha_site_key]" value="<?php echo esc_attr( (string) ( $general['recaptcha_site_key'] ?? '' ) ); ?>" placeholder="<?php esc_attr_e( 'Site Key', 'asrs-recruit-engine' ); ?>" /><br />
								<input type="password" class="regular-text" style="margin-top:6px;" name="general[recaptcha_secret_key]" value="" placeholder="<?php echo '' !== (string) ( $general['recaptcha_secret_key'] ?? '' ) ? esc_attr__( 'Secret Key saved (leave blank to keep)', 'asrs-recruit-engine' ) : esc_attr__( 'Secret Key', 'asrs-recruit-engine' ); ?>" autocomplete="new-password" /><br />
								<label style="display:inline-block;margin-top:6px;"><?php esc_html_e( 'v3 minimum score', 'asrs-recruit-engine' ); ?> <input type="number" min="0" max="1" step="0.1" style="width:80px;" name="general[recaptcha_min_score]" value="<?php echo esc_attr( (string) ( $general['recaptcha_min_score'] ?? 0.5 ) ); ?>" /></label>
								<p class="description"><?php esc_html_e( 'Create keys at google.com/recaptcha/admin for this domain; the key type must match the selected version. The secret key is stored encrypted. The minimum score (0.0 – 1.0, recommended 0.5) is only used with v3.', 'asrs-recruit-engine' ); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php esc_html_e( 'Uninstall Cleanup', 'asrs-recruit-engine' ); ?></th>
							<td><label><input type="checkbox" name="general[delete_data_on_uninstall]" value="1" <?php checked( ! empty( $general['delete_data_on_uninstall'] ) ); ?> /> <?php esc_html_e( 'Delete all applications & settings if plugin is uninstalled', 'asrs-recruit-engine' ); ?></label></td>
						</tr>
					</table>

					<p class="submit">
						<button type="submit" class="button button-primary"><?php esc_html_e( 'Save All Settings', 'asrs-recruit-engine' ); ?></button>
					</p>
				</form>
			</div>
		</div>
		<?php
	}

	private function handle_save(): void {
		$current_zoho = $this->settings->get_zoho_settings();
		$raw_zoho     = is_array( $_POST['zoho'] ?? null ) ? wp_unslash( $_POST['zoho'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$new_zoho = array(
			'enabled'          => ! empty( $raw_zoho['enabled'] ),
			'data_center'      => sanitize_text_field( (string) ( $raw_zoho['data_center'] ?? $current_zoho['data_center'] ) ),
			'client_id'        => sanitize_text_field( (string) ( $raw_zoho['client_id'] ?? '' ) ),
			'client_secret'    => '' !== trim( (string) ( $raw_zoho['client_secret'] ?? '' ) ) ? trim( (string) $raw_zoho['client_secret'] ) : (string) $current_zoho['client_secret'],
			'refresh_token'    => '' !== trim( (string) ( $raw_zoho['refresh_token'] ?? '' ) ) ? trim( (string) $raw_zoho['refresh_token'] ) : (string) $current_zoho['refresh_token'],
			'candidate_source' => sanitize_text_field( (string) ( $raw_zoho['candidate_source'] ?? 'Website Careers' ) ),
			'trigger_workflow' => ! empty( $raw_zoho['trigger_workflow'] ),
			'connected_at'     => (string) $current_zoho['connected_at'],
			'last_test_status' => (string) $current_zoho['last_test_status'],
			'last_tested_at'   => (string) $current_zoho['last_tested_at'],
			'last_test_error'  => (string) $current_zoho['last_test_error'],
		);
		$this->settings->save_zoho_settings( $new_zoho );

		$current_gen = $this->settings->get_all();
		$raw_gen     = is_array( $_POST['general'] ?? null ) ? wp_unslash( $_POST['general'] ) : array(); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		$new_gen = array(
			'careers_heading'          => sanitize_text_field( (string) ( $raw_gen['careers_heading'] ?? $current_gen['careers_heading'] ) ),
			'careers_subtitle'         => sanitize_text_field( (string) ( $raw_gen['careers_subtitle'] ?? '' ) ),
			'allowed_job_statuses'     => sanitize_text_field( (string) ( $raw_gen['allowed_job_statuses'] ?? '' ) ),
			'only_published_jobs'      => ! empty( $raw_gen['only_published_jobs'] ),
			'max_resume_mb'            => max( 1, min( 20, (int) ( $raw_gen['max_resume_mb'] ?? 5 ) ) ),
			'max_retry_attempts'       => (int) $current_gen['max_retry_attempts'],
			'notify_on_sync_failure'   => ! empty( $raw_gen['notify_on_sync_failure'] ),
			'sync_failure_email'       => sanitize_email( (string) ( $raw_gen['sync_failure_email'] ?? get_option( 'admin_email' ) ) ),
			'turnstile_enabled'        => ! empty( $raw_gen['turnstile_enabled'] ),
			'turnstile_site_key'       => sanitize_text_field( (string) ( $raw_gen['turnstile_site_key'] ?? '' ) ),
			'turnstile_secret_key'     => '' !== trim( (string) ( $raw_gen['turnstile_secret_key'] ?? '' ) ) ? trim( (string) $raw_gen['turnstile_secret_key'] ) : (string) $current_gen['turnstile_secret_key'],
			'recaptcha_enabled'        => ! empty( $raw_gen['recaptcha_enabled'] ),
			'recaptcha_version'        => 'v3' === (string) ( $raw_gen['recaptcha_version'] ?? 'v2' ) ? 'v3' : 'v2',
			'recaptcha_site_key'       => sanitize_text_field( (string) ( $raw_gen['recaptcha_site_key'] ?? '' ) ),
			'recaptcha_secret_key'     => '' !== trim( (string) ( $raw_gen['recaptcha_secret_key'] ?? '' ) ) ? trim( (string) $raw_gen['recaptcha_secret_key'] ) : (string) $current_gen['recaptcha_secret_key'],
			'recaptcha_min_score'      => round( max( 0.0, min( 1.0, (float) ( $raw_gen['recaptcha_min_score'] ?? 0.5 ) ) ), 2 ),
			'delete_data_on_uninstall' => ! empty( $raw_gen['delete_data_on_uninstall'] ),
		);
		$this->settings->save_all( $new_gen );
	}
}
