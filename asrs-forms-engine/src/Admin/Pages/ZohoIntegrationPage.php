<?php
/**
 * Admin Page: Zoho CRM API v7 Connection & Discovery.
 *
 * @package ASRS\FormsEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Admin\Pages;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Security\Crypto;
use ASRS\FormsEngine\Services\Zoho\ZohoAuthService;
use ASRS\FormsEngine\Services\Zoho\ZohoCrmService;
use ASRS\FormsEngine\Services\Zoho\ZohoDataCenter;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ZohoIntegrationPage extends AbstractPage {

	private Settings $settings;
	private ZohoAuthService $auth;
	private ZohoCrmService $crm;

	public function __construct( Settings $settings, ZohoAuthService $auth, ZohoCrmService $crm ) {
		$this->settings = $settings;
		$this->auth     = $auth;
		$this->crm      = $crm;
	}

	public function render(): void {
		$cfg        = $this->settings->get_zoho_settings();
		$gen_cfg    = $this->settings->get_all_general();
		$rc_enabled = ! empty( $gen_cfg['recaptcha_enabled'] );
		$rc_ready   = $rc_enabled && '' !== trim( (string) $gen_cfg['recaptcha_site_key'] ) && '' !== trim( (string) $gen_cfg['recaptcha_secret_key'] );
		$rc_version = 'v3' === (string) ( $gen_cfg['recaptcha_version'] ?? 'v2' ) ? 'v3' : 'v2';
		$status     = $this->auth->get_token_status();
		$org_info   = $this->crm->get_cached_org_info();
		$modules    = $this->crm->get_cached_modules();
		$dcs        = ZohoDataCenter::all();

		$actions = '<button type="button" id="asrs-test-zoho-btn" class="button button-primary">' . esc_html__( 'Test Connection', 'asrs-forms-engine' ) . '</button> '
			. '<button type="button" id="asrs-sync-modules-btn" class="button button-secondary">' . esc_html__( 'Sync Modules', 'asrs-forms-engine' ) . '</button>';

		$this->render_header(
			__( 'Zoho CRM API v7 & Integrations', 'asrs-forms-engine' ),
			__( 'Connect WordPress to Zoho CRM and configure Google reCAPTCHA protection. Zoho Workflows, Blueprints, and Assignment Rules run automatically on every synced lead.', 'asrs-forms-engine' ),
			$actions
		);

		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['updated'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Zoho CRM settings saved.', 'asrs-forms-engine' ) . '</p></div>';
		}
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		if ( isset( $_GET['recaptcha_updated'] ) ) {
			echo '<div class="notice notice-success is-dismissible"><p>' . esc_html__( 'Google reCAPTCHA integration settings saved.', 'asrs-forms-engine' ) . '</p></div>';
		}
		?>

		<div id="asrs-zoho-action-feedback"></div>

		<div class="asrs-two-col">
			<div class="asrs-panel">
				<div class="asrs-panel-header">
					<h2><?php esc_html_e( 'OAuth 2.0 Connection Settings', 'asrs-forms-engine' ); ?></h2>
					<?php echo $this->render_status_badge( $status['enabled'] ? $status['last_test_status'] : 'disconnected' ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</div>

				<form method="post">
					<?php wp_nonce_field( 'asrs_save_zoho_settings_nonce' ); ?>

					<table class="form-table">
						<tr>
							<th><label for="zoho_enabled"><?php esc_html_e( 'Enable Integration', 'asrs-forms-engine' ); ?></label></th>
							<td>
								<label>
									<input type="checkbox" id="zoho_enabled" name="enabled" value="1" <?php checked( ! empty( $cfg['enabled'] ) ); ?> />
									<?php esc_html_e( 'Sync form submissions to Zoho CRM API v7', 'asrs-forms-engine' ); ?>
								</label>
							</td>
						</tr>
						<tr>
							<th><label for="connection_name"><?php esc_html_e( 'Connection Name', 'asrs-forms-engine' ); ?></label></th>
							<td><input type="text" id="connection_name" name="connection_name" class="regular-text" value="<?php echo esc_attr( (string) $cfg['connection_name'] ); ?>" /></td>
						</tr>
						<tr>
							<th><label for="data_center"><?php esc_html_e( 'Zoho Data Center', 'asrs-forms-engine' ); ?></label></th>
							<td>
								<select id="data_center" name="data_center" class="regular-text">
									<?php foreach ( $dcs as $dc_key => $dc_meta ) : ?>
										<option value="<?php echo esc_attr( $dc_key ); ?>" <?php selected( (string) $cfg['data_center'], $dc_key ); ?>>
											<?php echo esc_html( $dc_meta['label'] ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							</td>
						</tr>
						<tr>
							<th><label for="client_id"><?php esc_html_e( 'Client ID', 'asrs-forms-engine' ); ?></label></th>
							<td><input type="text" id="client_id" name="client_id" class="regular-text code" value="<?php echo esc_attr( (string) $cfg['client_id'] ); ?>" placeholder="1000.XXXXXXXXXXXX" /></td>
						</tr>
						<tr>
							<th><label for="client_secret"><?php esc_html_e( 'Client Secret', 'asrs-forms-engine' ); ?></label></th>
							<td>
								<input type="password" id="client_secret" name="client_secret" class="regular-text code" value="<?php echo esc_attr( Crypto::mask( (string) $cfg['client_secret'] ) ); ?>" autocomplete="new-password" />
								<p class="description"><?php esc_html_e( 'Encrypted at rest with AES-256-CBC. Leave masked to preserve existing secret.', 'asrs-forms-engine' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="refresh_token"><?php esc_html_e( 'Refresh Token', 'asrs-forms-engine' ); ?></label></th>
							<td>
								<input type="password" id="refresh_token" name="refresh_token" class="regular-text code" value="<?php echo esc_attr( Crypto::mask( (string) $cfg['refresh_token'] ) ); ?>" autocomplete="new-password" />
								<p class="description"><?php esc_html_e( 'Scopes: ZohoCRM.modules.ALL, ZohoCRM.settings.ALL, ZohoCRM.org.READ', 'asrs-forms-engine' ); ?></p>
							</td>
						</tr>
						<tr>
							<th><label for="default_module"><?php esc_html_e( 'Default CRM Module', 'asrs-forms-engine' ); ?></label></th>
							<td>
								<input type="text" id="default_module" name="default_module" class="regular-text" value="<?php echo esc_attr( (string) $cfg['default_module'] ); ?>" placeholder="Leads" />
							</td>
						</tr>
						<tr>
							<th><label for="trigger_workflow"><?php esc_html_e( 'Zoho Automations', 'asrs-forms-engine' ); ?></label></th>
							<td>
								<label>
									<input type="checkbox" id="trigger_workflow" name="trigger_workflow" value="1" <?php checked( ! empty( $cfg['trigger_workflow'] ) ); ?> />
									<?php esc_html_e( 'Trigger Zoho CRM workflows, approvals, and blueprints automatically', 'asrs-forms-engine' ); ?>
								</label>
							</td>
						</tr>
					</table>

					<p class="submit">
						<button type="submit" name="asrs_save_zoho_settings" value="1" class="button button-primary button-large">
							<?php esc_html_e( 'Save Zoho Settings', 'asrs-forms-engine' ); ?>
						</button>
					</p>
				</form>
			</div>

			<div>
				<div class="asrs-panel">
					<h2><?php esc_html_e( 'CRM Organization Information', 'asrs-forms-engine' ); ?></h2>
					<table class="asrs-kv-table">
						<tr>
							<th><?php esc_html_e( 'Connection Name', 'asrs-forms-engine' ); ?></th>
							<td><?php echo esc_html( (string) $cfg['connection_name'] ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Company Name', 'asrs-forms-engine' ); ?></th>
							<td id="asrs-org-company"><?php echo esc_html( (string) ( $org_info['company_name'] ?? '—' ) ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Org ID / ZGID', 'asrs-forms-engine' ); ?></th>
							<td id="asrs-org-id">
								<code><?php echo esc_html( (string) ( $org_info['id'] ?? '—' ) ); ?></code>
								<?php if ( ! empty( $org_info['zgid'] ) ) : ?>
									/ <code><?php echo esc_html( (string) $org_info['zgid'] ); ?></code>
								<?php endif; ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Environment', 'asrs-forms-engine' ); ?></th>
							<td id="asrs-org-type"><?php echo esc_html( strtoupper( (string) ( $org_info['type'] ?? '—' ) ) ); ?></td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Timezone / Currency', 'asrs-forms-engine' ); ?></th>
							<td id="asrs-org-tz">
								<?php echo esc_html( trim( ( (string) ( $org_info['time_zone'] ?? '' ) ) . ' / ' . ( (string) ( $org_info['currency'] ?? '' ) ), ' /' ) ?: '—' ); ?>
							</td>
						</tr>
						<tr>
							<th><?php esc_html_e( 'Last Tested', 'asrs-forms-engine' ); ?></th>
							<td>
								<?php echo $this->format_datetime( (string) $status['last_tested_at'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
								<?php if ( ! empty( $status['last_test_error'] ) ) : ?>
									<br><small class="asrs-text-danger"><?php echo esc_html( (string) $status['last_test_error'] ); ?></small>
								<?php endif; ?>
							</td>
						</tr>
					</table>
				</div>

				<div class="asrs-panel">
					<h2><?php esc_html_e( 'Self-Client Grant Token Exchange', 'asrs-forms-engine' ); ?></h2>
					<p class="description">
						<?php esc_html_e( 'Save Client ID & Client Secret first, paste a temporary Zoho Self-Client Grant Token below, and click Exchange to store your permanent Refresh Token automatically.', 'asrs-forms-engine' ); ?>
					</p>
					<p>
						<input type="text" id="asrs-grant-token-input" class="large-text code" placeholder="1000.xxxxxxxx.xxxxxxxx" />
					</p>
					<p>
						<button type="button" id="asrs-exchange-grant-btn" class="button button-secondary">
							<?php esc_html_e( 'Exchange Grant Token for Refresh Token', 'asrs-forms-engine' ); ?>
						</button>
					</p>
				</div>

				<div class="asrs-panel">
					<h2><?php esc_html_e( 'Test Record Creation', 'asrs-forms-engine' ); ?></h2>
					<p class="description"><?php esc_html_e( 'Verify end-to-end CRM write access by creating a sample test record in Zoho CRM API v7.', 'asrs-forms-engine' ); ?></p>
					<p style="display:flex;gap:8px;align-items:center;">
						<select id="asrs-test-record-module">
							<option value="Leads">Leads</option>
							<option value="Contacts">Contacts</option>
							<option value="Accounts">Accounts</option>
						</select>
						<button type="button" id="asrs-create-test-record-btn" class="button button-secondary">
							<?php esc_html_e( 'Create Test Record', 'asrs-forms-engine' ); ?>
						</button>
					</p>
				</div>
			</div>
		</div>

		<div class="asrs-panel" id="asrs-recaptcha-integration-panel">
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

			<form method="post">
				<?php wp_nonce_field( 'asrs_save_recaptcha_settings_nonce' ); ?>

				<table class="form-table">
					<tr>
						<th><label for="zoho_page_recaptcha_enabled"><?php esc_html_e( 'Enable reCAPTCHA', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="zoho_page_recaptcha_enabled" name="recaptcha_enabled" value="1" <?php checked( $rc_enabled ); ?> />
								<?php esc_html_e( 'Protect all frontend forms with Google reCAPTCHA before saving or syncing leads to Zoho CRM', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="zoho_page_recaptcha_version"><?php esc_html_e( 'reCAPTCHA Version', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<select id="zoho_page_recaptcha_version" name="recaptcha_version" class="regular-text asrs-recaptcha-version-select">
								<option value="v2" <?php selected( $rc_version, 'v2' ); ?>>
									<?php esc_html_e( 'v2 — "I\'m not a robot" checkbox', 'asrs-forms-engine' ); ?>
								</option>
								<option value="v3" <?php selected( $rc_version, 'v3' ); ?>>
									<?php esc_html_e( 'v3 — Invisible score-based', 'asrs-forms-engine' ); ?>
								</option>
							</select>
							<p class="description">
								<?php esc_html_e( 'Register keys at google.com/recaptcha/admin for this domain. Key type must match the selected version.', 'asrs-forms-engine' ); ?>
							</p>
						</td>
					</tr>
					<tr>
						<th><label for="zoho_page_recaptcha_site_key"><?php esc_html_e( 'Site Key', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<input type="text" id="zoho_page_recaptcha_site_key" name="recaptcha_site_key" class="regular-text code" value="<?php echo esc_attr( (string) $gen_cfg['recaptcha_site_key'] ); ?>" placeholder="<?php esc_attr_e( '6Lc...', 'asrs-forms-engine' ); ?>" />
						</td>
					</tr>
					<tr>
						<th><label for="zoho_page_recaptcha_secret_key"><?php esc_html_e( 'Secret Key', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<input type="password" id="zoho_page_recaptcha_secret_key" name="recaptcha_secret_key" class="regular-text code" value="<?php echo esc_attr( Crypto::mask( (string) $gen_cfg['recaptcha_secret_key'] ) ); ?>" autocomplete="new-password" />
							<p class="description"><?php esc_html_e( 'Encrypted at rest with AES-256-CBC. Leave masked to keep existing secret, or enter __CLEAR__ to remove.', 'asrs-forms-engine' ); ?></p>
						</td>
					</tr>
					<tr class="asrs-recaptcha-v3-row" <?php echo 'v3' !== $rc_version ? 'style="display:none;"' : ''; ?>>
						<th><label for="zoho_page_recaptcha_min_score"><?php esc_html_e( 'v3 Minimum Score', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<input type="number" id="zoho_page_recaptcha_min_score" name="recaptcha_min_score" min="0" max="1" step="0.1" style="width:90px;" value="<?php echo esc_attr( (string) ( $gen_cfg['recaptcha_min_score'] ?? 0.5 ) ); ?>" />
							<p class="description"><?php esc_html_e( 'Score threshold from 0.0 to 1.0 (recommended 0.5) for v3 verification.', 'asrs-forms-engine' ); ?></p>
						</td>
					</tr>
				</table>

				<p class="submit">
					<button type="submit" name="asrs_save_recaptcha_settings" value="1" class="button button-primary">
						<?php esc_html_e( 'Save reCAPTCHA Settings', 'asrs-forms-engine' ); ?>
					</button>
				</p>
			</form>
		</div>

		<div class="asrs-panel">
			<div class="asrs-panel-header">
				<h2>
					<?php
					printf(
						/* translators: %d: module count */
						esc_html__( 'Discovered Zoho CRM Modules (%d)', 'asrs-forms-engine' ),
						count( $modules )
					);
					?>
				</h2>
			</div>
			<?php if ( empty( $modules ) ) : ?>
				<p class="asrs-empty"><?php esc_html_e( 'Click "Sync Modules" above to fetch available modules from Zoho CRM API v7.', 'asrs-forms-engine' ); ?></p>
			<?php else : ?>
				<table class="wp-list-table widefat fixed striped">
					<thead>
						<tr>
							<th><?php esc_html_e( 'Module Name', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'API Name', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'Type', 'asrs-forms-engine' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $modules as $mod ) : ?>
							<tr>
								<td><strong><?php echo esc_html( (string) $mod['plural_label'] ); ?></strong></td>
								<td><code><?php echo esc_html( (string) $mod['api_name'] ); ?></code></td>
								<td><?php echo esc_html( ucfirst( (string) $mod['generated_type'] ) ); ?></td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>
			<?php endif; ?>
		</div>

		<?php
		$this->render_footer();
	}
}
