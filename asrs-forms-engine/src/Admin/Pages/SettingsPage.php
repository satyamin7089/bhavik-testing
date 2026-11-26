<?php
/**
 * Admin Page: Settings (Spam/reCAPTCHA/Turnstile Security, Auto-Retry & Failure Alerts).
 *
 * @package ASRS\FormsEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Admin\Pages;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Security\Crypto;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class SettingsPage extends AbstractPage {

	private Settings $settings;

	public function __construct( Settings $settings ) {
		$this->settings = $settings;
	}

	public function render(): void {
		$cfg        = $this->settings->get_all_general();
		$rc_enabled = ! empty( $cfg['recaptcha_enabled'] );
		$rc_ready   = $rc_enabled && '' !== trim( (string) $cfg['recaptcha_site_key'] ) && '' !== trim( (string) $cfg['recaptcha_secret_key'] );
		$rc_version = 'v3' === (string) ( $cfg['recaptcha_version'] ?? 'v2' ) ? 'v3' : 'v2';

		$this->render_header(
			__( 'Platform Settings', 'asrs-forms-engine' ),
			__( 'Configure spam protection, Google reCAPTCHA, Cloudflare Turnstile, and failed-sync retry behavior. Lead & customer emails are handled inside Zoho CRM Workflows.', 'asrs-forms-engine' )
		);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['updated'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Settings saved.', 'asrs-forms-engine' ) . '</p></div>';
		}
		?>

		<form method="post">
			<?php wp_nonce_field( 'asrs_save_general_settings_nonce' ); ?>

			<div class="asrs-panel" id="asrs-recaptcha-settings">
				<div class="asrs-panel-header">
					<h2><?php esc_html_e( 'Google reCAPTCHA Integration', 'asrs-forms-engine' ); ?></h2>
					<?php if ( $rc_ready ) : ?>
						<span class="asrs-badge asrs-badge-success">
							<?php
							echo esc_html(
								sprintf(
									/* translators: %s: reCAPTCHA version (v2 or v3) */
									__( 'Active (%s)', 'asrs-forms-engine' ),
									strtoupper( $rc_version )
								)
							);
							?>
						</span>
					<?php elseif ( $rc_enabled ) : ?>
						<span class="asrs-badge asrs-badge-warning"><?php esc_html_e( 'Keys Required', 'asrs-forms-engine' ); ?></span>
					<?php else : ?>
						<span class="asrs-badge asrs-badge-muted"><?php esc_html_e( 'Disabled', 'asrs-forms-engine' ); ?></span>
					<?php endif; ?>
				</div>

				<table class="form-table">
					<tr>
						<th><label for="recaptcha_enabled"><?php esc_html_e( 'Google reCAPTCHA', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="recaptcha_enabled" name="recaptcha_enabled" value="1" <?php checked( $rc_enabled ); ?> />
								<?php esc_html_e( 'Enable Google reCAPTCHA verification on all forms', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="recaptcha_version"><?php esc_html_e( 'reCAPTCHA Version', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<select id="recaptcha_version" name="recaptcha_version" class="regular-text asrs-recaptcha-version-select">
								<option value="v2" <?php selected( $rc_version, 'v2' ); ?>>
									<?php esc_html_e( 'v2 — "I\'m not a robot" checkbox', 'asrs-forms-engine' ); ?>
								</option>
								<option value="v3" <?php selected( $rc_version, 'v3' ); ?>>
									<?php esc_html_e( 'v3 — Invisible score-based', 'asrs-forms-engine' ); ?>
								</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Create keys at google.com/recaptcha/admin for this domain. The key type must match the selected version.', 'asrs-forms-engine' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="recaptcha_site_key"><?php esc_html_e( 'reCAPTCHA Site Key', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<input type="text" id="recaptcha_site_key" name="recaptcha_site_key" class="regular-text code" value="<?php echo esc_attr( (string) $cfg['recaptcha_site_key'] ); ?>" placeholder="<?php esc_attr_e( '6Lc...', 'asrs-forms-engine' ); ?>" />
						</td>
					</tr>
					<tr>
						<th><label for="recaptcha_secret_key"><?php esc_html_e( 'reCAPTCHA Secret Key', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<input type="password" id="recaptcha_secret_key" name="recaptcha_secret_key" class="regular-text code" value="<?php echo esc_attr( Crypto::mask( (string) $cfg['recaptcha_secret_key'] ) ); ?>" autocomplete="new-password" />
							<p class="description"><?php esc_html_e( 'Encrypted at rest with AES-256-CBC. Leave masked to preserve existing secret, or enter __CLEAR__ to remove.', 'asrs-forms-engine' ); ?></p>
						</td>
					</tr>
					<tr class="asrs-recaptcha-v3-row" <?php echo 'v3' !== $rc_version ? 'style="display:none;"' : ''; ?>>
						<th><label for="recaptcha_min_score"><?php esc_html_e( 'v3 Minimum Score', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<input type="number" id="recaptcha_min_score" name="recaptcha_min_score" min="0" max="1" step="0.1" style="width:90px;" value="<?php echo esc_attr( (string) ( $cfg['recaptcha_min_score'] ?? 0.5 ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Threshold between 0.0 (likely bot) and 1.0 (human). Recommended: 0.5. Used with reCAPTCHA v3 only.', 'asrs-forms-engine' ); ?></p>
						</td>
					</tr>
				</table>
			</div>

			<div class="asrs-panel">
				<h2><?php esc_html_e( 'Security & Spam Protection', 'asrs-forms-engine' ); ?></h2>
				<table class="form-table">
					<tr>
						<th><label for="honeypot_enabled"><?php esc_html_e( 'Honeypot Protection', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="honeypot_enabled" name="honeypot_enabled" value="1" <?php checked( ! empty( $cfg['honeypot_enabled'] ) ); ?> />
								<?php esc_html_e( 'Enable hidden anti-bot honeypot field', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="min_submit_seconds"><?php esc_html_e( 'Minimum Submit Time (seconds)', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<input type="number" id="min_submit_seconds" name="min_submit_seconds" min="0" max="30" value="<?php echo esc_attr( (string) $cfg['min_submit_seconds'] ); ?>" />
						</td>
					</tr>
					<tr>
						<th><label for="turnstile_enabled"><?php esc_html_e( 'Cloudflare Turnstile (Optional)', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="turnstile_enabled" name="turnstile_enabled" value="1" <?php checked( ! empty( $cfg['turnstile_enabled'] ) ); ?> />
								<?php esc_html_e( 'Enable Cloudflare Turnstile challenge on all forms', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="turnstile_site_key"><?php esc_html_e( 'Turnstile Site Key', 'asrs-forms-engine' ); ?></label></th>
						<td><input type="text" id="turnstile_site_key" name="turnstile_site_key" class="regular-text code" value="<?php echo esc_attr( (string) $cfg['turnstile_site_key'] ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="turnstile_secret_key"><?php esc_html_e( 'Turnstile Secret Key', 'asrs-forms-engine' ); ?></label></th>
						<td><input type="password" id="turnstile_secret_key" name="turnstile_secret_key" class="regular-text code" value="<?php echo esc_attr( Crypto::mask( (string) $cfg['turnstile_secret_key'] ) ); ?>" autocomplete="new-password" /></td>
					</tr>
					<tr>
						<th><label for="store_ip_address"><?php esc_html_e( 'Store Visitor IP Address', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="store_ip_address" name="store_ip_address" value="1" <?php checked( ! empty( $cfg['store_ip_address'] ) ); ?> />
								<?php esc_html_e( 'Save visitor IP address in submission metadata', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div>

			<div class="asrs-panel">
				<h2><?php esc_html_e( 'Zoho Sync Retry & Failure Alert', 'asrs-forms-engine' ); ?></h2>
				<table class="form-table">
					<tr>
						<th><label for="auto_retry_enabled"><?php esc_html_e( 'WP-Cron Auto Retry', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="auto_retry_enabled" name="auto_retry_enabled" value="1" <?php checked( ! empty( $cfg['auto_retry_enabled'] ) ); ?> />
								<?php esc_html_e( 'Automatically retry failed Zoho CRM syncs in the background', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="max_retry_attempts"><?php esc_html_e( 'Max Retry Attempts', 'asrs-forms-engine' ); ?></label></th>
						<td><input type="number" id="max_retry_attempts" name="max_retry_attempts" min="1" max="20" value="<?php echo esc_attr( (string) $cfg['max_retry_attempts'] ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="retry_base_delay_min"><?php esc_html_e( 'Initial Retry Delay (minutes)', 'asrs-forms-engine' ); ?></label></th>
						<td><input type="number" id="retry_base_delay_min" name="retry_base_delay_min" min="1" max="120" value="<?php echo esc_attr( (string) $cfg['retry_base_delay_min'] ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="admin_notify_on_sync_fail"><?php esc_html_e( 'Sync Failure Alert', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="admin_notify_on_sync_fail" name="admin_notify_on_sync_fail" value="1" <?php checked( ! empty( $cfg['admin_notify_on_sync_fail'] ) ); ?> />
								<?php esc_html_e( 'Send fallback email to admin only if Zoho CRM sync fails (normal lead & customer emails are managed in Zoho CRM)', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="notification_email"><?php esc_html_e( 'Failure Alert Email', 'asrs-forms-engine' ); ?></label></th>
						<td><input type="email" id="notification_email" name="notification_email" class="regular-text" value="<?php echo esc_attr( (string) $cfg['notification_email'] ); ?>" /></td>
					</tr>
					<tr>
						<th><label for="delete_data_on_uninstall"><?php esc_html_e( 'Uninstall Cleanup', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="delete_data_on_uninstall" name="delete_data_on_uninstall" value="1" <?php checked( ! empty( $cfg['delete_data_on_uninstall'] ) ); ?> />
								<?php esc_html_e( 'Remove all ASRS Forms tables and options when plugin is deleted', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
				</table>
			</div>

			<p class="submit">
				<button type="submit" name="asrs_save_general_settings" value="1" class="button button-primary button-large">
					<?php esc_html_e( 'Save Settings', 'asrs-forms-engine' ); ?>
				</button>
			</p>
		</form>

		<?php
		$this->render_footer();
	}
}
