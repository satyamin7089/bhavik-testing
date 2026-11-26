<?php
/**
 * Admin Page: Forms (Auto-loaded from /forms/*.php inside plugin) & Zoho CRM Field Mapping.
 *
 * @package ASRS\FormsEngine\Admin\Pages
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Admin\Pages;

use ASRS\FormsEngine\Forms\FormInterface;
use ASRS\FormsEngine\Forms\FormRegistry;
use ASRS\FormsEngine\Repositories\FormMappingRepository;
use ASRS\FormsEngine\Repositories\SubmissionRepository;
use ASRS\FormsEngine\Services\Zoho\ZohoCrmService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormsPage extends AbstractPage {

	private FormRegistry $registry;
	private SubmissionRepository $submissions;
	private FormMappingRepository $mappings;
	private ZohoCrmService $zoho_crm;

	public function __construct(
		FormRegistry $registry,
		SubmissionRepository $submissions,
		FormMappingRepository $mappings,
		ZohoCrmService $zoho_crm
	) {
		$this->registry    = $registry;
		$this->submissions = $submissions;
		$this->mappings    = $mappings;
		$this->zoho_crm    = $zoho_crm;
	}

	public function render(): void {
		// phpcs:ignore WordPress.Security.NonceVerification.Recommended
		$mapping_form_id = isset( $_GET['map_form'] ) ? sanitize_key( wp_unslash( (string) $_GET['map_form'] ) ) : '';

		if ( '' !== $mapping_form_id ) {
			$form = $this->registry->get( $mapping_form_id );
			if ( $form ) {
				$this->render_mapping_editor( $form );
				return;
			}
		}

		$this->render_forms_list();
	}

	private function render_forms_list(): void {
		$forms     = $this->registry->all();
		$sub_stats = $this->submissions->get_stats();

		$this->render_header(
			__( 'Registered Forms', 'asrs-forms-engine' ),
			__( 'Forms are auto-discovered from the /forms/*.php folder inside this plugin—no functions.php edits required.', 'asrs-forms-engine' )
		);
		?>

		<div class="asrs-panel">
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Form Title & ID', 'asrs-forms-engine' ); ?></th>
						<th style="width:90px;"><?php esc_html_e( 'Fields', 'asrs-forms-engine' ); ?></th>
						<th><?php esc_html_e( 'Shortcodes', 'asrs-forms-engine' ); ?></th>
						<th style="width:120px;"><?php esc_html_e( 'Entries', 'asrs-forms-engine' ); ?></th>
						<th><?php esc_html_e( 'Zoho CRM Mapping', 'asrs-forms-engine' ); ?></th>
						<th style="width:180px;"><?php esc_html_e( 'Actions', 'asrs-forms-engine' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php if ( empty( $forms ) ) : ?>
						<tr>
							<td colspan="6"><?php esc_html_e( 'No forms found in /forms/*.php.', 'asrs-forms-engine' ); ?></td>
						</tr>
					<?php else : ?>
						<?php foreach ( $forms as $form ) : ?>
							<?php
							$fid        = $form->get_id();
							$shortcodes = FormRegistry::get_shortcodes( $fid );
							$mapping    = $this->mappings->get_effective_mapping( $form );
							$sub_count  = $sub_stats['by_form'][ $fid ] ?? 0;
							?>
							<tr>
								<td>
									<strong><?php echo esc_html( $form->get_title() ); ?></strong>
									<br><code><?php echo esc_html( $fid ); ?></code>
									<?php if ( '' !== $form->get_description() ) : ?>
										<p class="description"><?php echo esc_html( $form->get_description() ); ?></p>
									<?php endif; ?>
								</td>
								<td><?php echo esc_html( (string) count( $form->get_fields() ) ); ?></td>
								<td>
									<div class="asrs-shortcode-pill">
										<code><?php echo esc_html( $shortcodes['universal'] ); ?></code>
										<button type="button" class="button button-small asrs-copy-btn" data-copy="<?php echo esc_attr( $shortcodes['universal'] ); ?>">
											<?php esc_html_e( 'Copy', 'asrs-forms-engine' ); ?>
										</button>
									</div>
									<div class="asrs-shortcode-pill" style="margin-top:4px;">
										<code><?php echo esc_html( $shortcodes['dedicated'] ); ?></code>
										<button type="button" class="button button-small asrs-copy-btn" data-copy="<?php echo esc_attr( $shortcodes['dedicated'] ); ?>">
											<?php esc_html_e( 'Copy', 'asrs-forms-engine' ); ?>
										</button>
									</div>
								</td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=asrs-forms&form_id=' . $fid ) ); ?>">
										<?php echo esc_html( number_format_i18n( $sub_count ) ); ?>
									</a>
								</td>
								<td>
									<?php if ( ! empty( $mapping['enabled'] ) ) : ?>
										<span class="asrs-badge asrs-badge-success"><?php esc_html_e( 'Enabled', 'asrs-forms-engine' ); ?></span>
										<br><small><?php echo esc_html( sprintf( __( 'Module: %s (%d mapped)', 'asrs-forms-engine' ), (string) $mapping['module'], count( $mapping['field_map'] ) ) ); ?></small>
									<?php else : ?>
										<span class="asrs-badge asrs-badge-muted"><?php esc_html_e( 'Disabled', 'asrs-forms-engine' ); ?></span>
									<?php endif; ?>
								</td>
								<td>
									<a href="<?php echo esc_url( admin_url( 'admin.php?page=asrs-forms-list&map_form=' . $fid ) ); ?>" class="button button-primary button-small">
										<?php esc_html_e( 'Schema & Zoho Map', 'asrs-forms-engine' ); ?>
									</a>
								</td>
							</tr>
						<?php endforeach; ?>
					<?php endif; ?>
				</tbody>
			</table>
		</div>

		<div class="asrs-panel">
			<h2><?php esc_html_e( 'How to Add Another Form Inside This Plugin', 'asrs-forms-engine' ); ?></h2>
			<p class="description">
				<?php
				printf(
					/* translators: %s: forms folder path */
					esc_html__( 'Create any new PHP file inside %s (for example: forms/sample-request.php). It is loaded automatically with its own shortcode and Zoho CRM mapping:', 'asrs-forms-engine' ),
					'<code>asrs-forms-engine/forms/</code>'
				);
				?>
			</p>
			<pre class="asrs-code-block">&lt;?php
return [
    'id'          =&gt; 'sample-request',
    'title'       =&gt; 'Polymer Sample Request',
    'zoho'        =&gt; [
        'enabled'          =&gt; true,
        'module'           =&gt; 'Leads',
        'trigger_workflow' =&gt; true,
        'field_map'        =&gt; [
            'full_name' =&gt; 'Last_Name',
            'email'     =&gt; 'Email',
            'company'   =&gt; 'Company',
            'grade'     =&gt; 'Description',
        ],
    ],
    'fields'      =&gt; [
        'full_name' =&gt; [ 'label' =&gt; 'Full Name', 'type' =&gt; 'text', 'required' =&gt; true ],
        'email'     =&gt; [ 'label' =&gt; 'Business Email', 'type' =&gt; 'email', 'required' =&gt; true ],
        'company'   =&gt; [ 'label' =&gt; 'Company Name', 'type' =&gt; 'text', 'required' =&gt; false ],
        'grade'     =&gt; [ 'label' =&gt; 'Grade', 'type' =&gt; 'select', 'required' =&gt; true, 'options' =&gt; [ 'PP', 'HDPE', 'ABS' ] ],
    ],
];</pre>
		</div>

		<?php
		$this->render_footer();
	}

	private function render_mapping_editor( FormInterface $form ): void {
		$fid          = $form->get_id();
		$mapping      = $this->mappings->get_effective_mapping( $form );
		$modules      = $this->zoho_crm->get_cached_modules();
		$fields_cache = $this->zoho_crm->get_cached_fields_map();
		$current_mod  = (string) ( $mapping['module'] ?: 'Leads' );
		$zoho_fields  = $fields_cache[ $current_mod ]['fields'] ?? array();
		$shortcodes   = FormRegistry::get_shortcodes( $fid );

		$back_btn = sprintf(
			'<a href="%s" class="button button-secondary">← %s</a>',
			esc_url( admin_url( 'admin.php?page=asrs-forms-list' ) ),
			esc_html__( 'Back to Forms', 'asrs-forms-engine' )
		);

		$this->render_header(
			sprintf(
				/* translators: %s: form title */
				__( 'Form Schema & Zoho Mapping: %s', 'asrs-forms-engine' ),
				$form->get_title()
			),
			sprintf( 'Form ID: %s | Shortcode: %s', $fid, $shortcodes['universal'] ),
			$back_btn
		);
		?>

		<div class="asrs-panel">
			<h2><?php esc_html_e( 'PHP Field Schema', 'asrs-forms-engine' ); ?></h2>
			<table class="wp-list-table widefat fixed striped">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Field Key', 'asrs-forms-engine' ); ?></th>
						<th><?php esc_html_e( 'Label', 'asrs-forms-engine' ); ?></th>
						<th><?php esc_html_e( 'Type', 'asrs-forms-engine' ); ?></th>
						<th><?php esc_html_e( 'Required', 'asrs-forms-engine' ); ?></th>
						<th><?php esc_html_e( 'Options / Rules', 'asrs-forms-engine' ); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ( $form->get_fields() as $field ) : ?>
						<tr>
							<td><code><?php echo esc_html( $field->get_name() ); ?></code></td>
							<td><?php echo esc_html( $field->get_label() ); ?></td>
							<td><code><?php echo esc_html( $field->get_type() ); ?></code></td>
							<td><?php echo $field->is_required() ? '<span class="asrs-badge asrs-badge-info">Required</span>' : 'Optional'; ?></td>
							<td>
								<?php if ( ! empty( $field->get_options() ) ) : ?>
									<small><?php echo esc_html( implode( ', ', array_keys( $field->get_options() ) ) ); ?></small>
								<?php elseif ( ! empty( $field->get_rules() ) ) : ?>
									<small><code><?php echo esc_html( wp_json_encode( $field->get_rules() ) ?: '' ); ?></code></small>
								<?php else : ?>
									—
								<?php endif; ?>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>

		<div class="asrs-panel">
			<div class="asrs-panel-header">
				<h2><?php esc_html_e( 'Zoho CRM API v7 Field Mapping', 'asrs-forms-engine' ); ?></h2>
				<div>
					<button type="button" class="button button-secondary" id="asrs-refresh-module-fields-btn">
						<?php esc_html_e( 'Fetch Module Fields from Zoho', 'asrs-forms-engine' ); ?>
					</button>
				</div>
			</div>

			<form id="asrs-form-mapping-editor" data-form-id="<?php echo esc_attr( $fid ); ?>">
				<table class="form-table">
					<tr>
						<th><label for="asrs-map-enabled"><?php esc_html_e( 'Enable Zoho CRM Sync', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="asrs-map-enabled" name="zoho_enabled" value="1" <?php checked( ! empty( $mapping['enabled'] ) ); ?> />
								<?php esc_html_e( 'Automatically sync submissions from this form to Zoho CRM', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
					<tr>
						<th><label for="asrs-map-module"><?php esc_html_e( 'Target Zoho Module', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<?php if ( ! empty( $modules ) ) : ?>
								<select id="asrs-map-module" name="zoho_module" class="regular-text">
									<?php foreach ( $modules as $mod ) : ?>
										<option value="<?php echo esc_attr( (string) $mod['api_name'] ); ?>" <?php selected( $current_mod, (string) $mod['api_name'] ); ?>>
											<?php echo esc_html( (string) $mod['plural_label'] . ' (' . $mod['api_name'] . ')' ); ?>
										</option>
									<?php endforeach; ?>
								</select>
							<?php else : ?>
								<input type="text" id="asrs-map-module" name="zoho_module" class="regular-text" value="<?php echo esc_attr( $current_mod ); ?>" placeholder="Leads" />
								<p class="description">
									<?php esc_html_e( 'Click "Sync Modules" on the Zoho CRM page to populate the dropdown, or type a module API name (e.g., Leads, Contacts).', 'asrs-forms-engine' ); ?>
								</p>
							<?php endif; ?>
						</td>
					</tr>
					<tr>
						<th><label for="asrs-map-workflow"><?php esc_html_e( 'Trigger Zoho Automations', 'asrs-forms-engine' ); ?></label></th>
						<td>
							<label>
								<input type="checkbox" id="asrs-map-workflow" name="trigger_workflow" value="1" <?php checked( ! empty( $mapping['trigger_workflow'] ) ); ?> />
								<?php esc_html_e( 'Execute Zoho CRM workflow rules, approvals, and blueprints when lead is inserted', 'asrs-forms-engine' ); ?>
							</label>
						</td>
					</tr>
				</table>

				<h3><?php esc_html_e( 'Field Mapping Table', 'asrs-forms-engine' ); ?></h3>
				<table class="wp-list-table widefat fixed striped" id="asrs-mapping-fields-table">
					<thead>
						<tr>
							<th><?php esc_html_e( 'WordPress Form Field', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'Zoho CRM Field API Name', 'asrs-forms-engine' ); ?></th>
							<th><?php esc_html_e( 'Discovered Zoho Field Suggestion', 'asrs-forms-engine' ); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php foreach ( $form->get_fields() as $field ) : ?>
							<?php
							$fname      = $field->get_name();
							$mapped_val = (string) ( $mapping['field_map'][ $fname ] ?? '' );
							?>
							<tr>
								<td>
									<strong><?php echo esc_html( $field->get_label() ); ?></strong>
									<br><code><?php echo esc_html( $fname ); ?></code>
									<?php if ( $field->is_required() ) : ?>
										<span class="asrs-required">*</span>
									<?php endif; ?>
								</td>
								<td>
									<input type="text"
										class="regular-text asrs-zoho-api-input"
										name="field_map[<?php echo esc_attr( $fname ); ?>]"
										value="<?php echo esc_attr( $mapped_val ); ?>"
										placeholder="e.g. Last_Name, Email, Company" />
								</td>
								<td>
									<select class="asrs-zoho-field-picker" data-target="field_map[<?php echo esc_attr( $fname ); ?>]">
										<option value=""><?php esc_html_e( '— Select discovered Zoho field —', 'asrs-forms-engine' ); ?></option>
										<?php foreach ( $zoho_fields as $zf ) : ?>
											<option value="<?php echo esc_attr( (string) $zf['api_name'] ); ?>" <?php selected( $mapped_val, (string) $zf['api_name'] ); ?>>
												<?php
												$lookup_tag = ( 'lookup' === ( $zf['data_type'] ?? '' ) && ! empty( $zf['lookup_module'] ) )
													? ' [Lookup -> ' . (string) $zf['lookup_module'] . ']'
													: '';
												echo esc_html(
													sprintf(
														'%s (%s)%s%s',
														(string) $zf['field_label'],
														(string) $zf['api_name'],
														$lookup_tag,
														! empty( $zf['system_mandatory'] ) ? ' *' : ''
													)
												);
												?>
											</option>
										<?php endforeach; ?>
									</select>
								</td>
							</tr>
						<?php endforeach; ?>
					</tbody>
				</table>

				<h3 style="margin-top:20px;"><?php esc_html_e( 'Static Zoho Field Defaults', 'asrs-forms-engine' ); ?></h3>
				<p class="description"><?php esc_html_e( 'Optional fixed Zoho field values attached to every record created by this form (e.g., Lead_Status = Not Contacted).', 'asrs-forms-engine' ); ?></p>
				<div id="asrs-static-values-container">
					<?php
					$static_vals = is_array( $mapping['static_values'] ?? null ) ? $mapping['static_values'] : array();
					if ( empty( $static_vals ) ) {
						$static_vals = array( '' => '' );
					}
					foreach ( $static_vals as $s_key => $s_val ) :
						?>
						<div class="asrs-static-row">
							<input type="text" class="asrs-static-key" value="<?php echo esc_attr( (string) $s_key ); ?>" placeholder="Zoho API Name (e.g. Lead_Status)" />
							<input type="text" class="asrs-static-val" value="<?php echo esc_attr( (string) $s_val ); ?>" placeholder="Value (e.g. Not Contacted)" />
							<button type="button" class="button button-small asrs-remove-static-row">&times;</button>
						</div>
					<?php endforeach; ?>
				</div>
				<p>
					<button type="button" class="button button-secondary" id="asrs-add-static-row">+ <?php esc_html_e( 'Add Static Field', 'asrs-forms-engine' ); ?></button>
				</p>

				<p class="submit">
					<button type="submit" class="button button-primary button-large" id="asrs-save-mapping-btn">
						<?php esc_html_e( 'Save Zoho Mapping', 'asrs-forms-engine' ); ?>
					</button>
					<span id="asrs-mapping-feedback" class="asrs-inline-feedback"></span>
				</p>
			</form>
		</div>

		<?php
		$this->render_footer();
	}
}
