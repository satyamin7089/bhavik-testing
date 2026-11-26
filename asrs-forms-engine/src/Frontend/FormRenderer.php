<?php
/**
 * HTML renderer for PHP-registered forms.
 *
 * @package ASRS\FormsEngine\Frontend
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Frontend;

use ASRS\FormsEngine\Core\Settings;
use ASRS\FormsEngine\Forms\Field;
use ASRS\FormsEngine\Forms\FormInterface;
use ASRS\FormsEngine\Security\ReCaptchaVerifier;
use ASRS\FormsEngine\Security\TurnstileVerifier;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormRenderer {

	private AssetsManager $assets;
	private TurnstileVerifier $turnstile;
	private ReCaptchaVerifier $recaptcha;
	private Settings $settings;

	public function __construct(
		AssetsManager $assets,
		TurnstileVerifier $turnstile,
		ReCaptchaVerifier $recaptcha,
		Settings $settings
	) {
		$this->assets    = $assets;
		$this->turnstile = $turnstile;
		$this->recaptcha = $recaptcha;
		$this->settings  = $settings;
	}

	/**
	 * Render a registered form to HTML.
	 *
	 * @param FormInterface        $form Form instance.
	 * @param array<string, mixed> $atts Shortcode attributes.
	 */
	public function render( FormInterface $form, array $atts = array() ): string {
		$this->assets->enqueue_on_demand();

		$form_id       = $form->get_id();
		$form_settings = $form->get_settings();
		$instance_id   = 'asrs-form-' . $form_id . '-' . wp_rand( 100, 9999 );
		$extra_class   = trim( (string) ( $form_settings['css_class'] ?? '' ) . ' ' . (string) ( $atts['class'] ?? '' ) );
		$default_show  = isset( $form_settings['show_title'] ) ? (bool) $form_settings['show_title'] : true;
		$show_title    = isset( $atts['show_title'] )
			? filter_var( $atts['show_title'], FILTER_VALIDATE_BOOLEAN )
			: $default_show;

		$submit_label     = (string) ( $form_settings['submit_label'] ?? __( 'Send Enquiry', 'asrs-forms-engine' ) );
		$submitting_label = (string) ( $form_settings['submitting_label'] ?? __( 'Sending...', 'asrs-forms-engine' ) );

		ob_start();
		?>
		<div class="asrs-form-wrapper <?php echo esc_attr( $extra_class ); ?>"
			id="<?php echo esc_attr( $instance_id ); ?>"
			data-form-id="<?php echo esc_attr( $form_id ); ?>">

			<?php if ( $show_title && '' !== $form->get_title() ) : ?>
				<h3 class="asrs-form-title"><?php echo esc_html( $form->get_title() ); ?></h3>
			<?php endif; ?>

			<form class="asrs-form" method="post" novalidate>
				<input type="hidden" name="form_id" value="<?php echo esc_attr( $form_id ); ?>" />
				<input type="hidden" name="asrs_form_nonce" value="<?php echo esc_attr( wp_create_nonce( 'asrs_form_submit_' . $form_id ) ); ?>" />
				<input type="hidden" name="asrs_rendered_at" value="<?php echo esc_attr( (string) time() ); ?>" />

				<?php if ( (bool) $this->settings->get( 'honeypot_enabled', true ) ) : ?>
					<div class="asrs-hp-field" aria-hidden="true">
						<label for="<?php echo esc_attr( $instance_id . '-hp' ); ?>">
							<?php esc_html_e( 'Leave this field empty', 'asrs-forms-engine' ); ?>
						</label>
						<input type="text"
							id="<?php echo esc_attr( $instance_id . '-hp' ); ?>"
							name="asrs_hp_website"
							value=""
							tabindex="-1"
							autocomplete="off" />
					</div>
				<?php endif; ?>

				<div class="asrs-form-fields">
					<?php
					foreach ( $form->get_fields() as $field ) {
						$this->render_field( $field, $instance_id );
					}
					?>
				</div>

				<?php if ( $this->turnstile->is_enabled() ) : ?>
					<div class="asrs-field-group asrs-width-full asrs-turnstile-group">
						<div class="cf-turnstile" data-sitekey="<?php echo esc_attr( $this->turnstile->get_site_key() ); ?>"></div>
					</div>
				<?php endif; ?>

				<?php if ( $this->recaptcha->is_enabled() ) : ?>
					<?php if ( 'v3' === $this->recaptcha->get_version() ) : ?>
						<input type="hidden" name="g-recaptcha-response" class="asrs-recaptcha-token" value="" />
					<?php else : ?>
						<div class="asrs-field-group asrs-width-full asrs-recaptcha-group" data-field-name="recaptcha">
							<div class="asrs-g-recaptcha" data-sitekey="<?php echo esc_attr( $this->recaptcha->get_site_key() ); ?>"></div>
							<div class="asrs-field-error" role="alert"></div>
						</div>
					<?php endif; ?>
				<?php endif; ?>

				<div class="asrs-form-messages" aria-live="polite"></div>

				<div class="asrs-form-actions">
					<button type="submit"
						class="asrs-btn asrs-btn-primary asrs-submit-btn"
						data-default-label="<?php echo esc_attr( $submit_label ); ?>"
						data-loading-label="<?php echo esc_attr( $submitting_label ); ?>">
						<span class="asrs-btn-label"><?php echo esc_html( $submit_label ); ?></span>
						<svg class="asrs-btn-arrow" viewBox="0 0 20 20" fill="none" aria-hidden="true">
							<path d="M4.167 10h11.666m0 0L10.833 5m5 5-5 5" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
						</svg>
					</button>
				</div>
			</form>
		</div>
		<?php
		$html = (string) ob_get_clean();

		return (string) apply_filters( 'asrs_forms_rendered_html', $html, $form, $atts );
	}

	private function render_field( Field $field, string $instance_id ): void {
		$name     = $field->get_name();
		$type     = $field->get_type();
		$width    = $field->get_width();
		$field_id = $instance_id . '-' . $name;
		$value    = $field->get_default_value();
		$req_attr = $field->is_required() ? 'required aria-required="true"' : '';
		$attrs    = $field->get_attributes();

		if ( 'hidden' === $type ) {
			printf(
				'<input type="hidden" id="%1$s" name="fields[%2$s]" value="%3$s" />',
				esc_attr( $field_id ),
				esc_attr( $name ),
				esc_attr( $value )
			);
			return;
		}
		?>
		<div class="asrs-field-group asrs-width-<?php echo esc_attr( $width ); ?> asrs-field-type-<?php echo esc_attr( $type ); ?> <?php echo esc_attr( $field->get_css_class() ); ?>"
			data-field-name="<?php echo esc_attr( $name ); ?>">
			<?php if ( 'checkbox' !== $type ) : ?>
				<label class="asrs-field-label" for="<?php echo esc_attr( $field_id ); ?>">
					<?php echo esc_html( $field->get_label() ); ?>
					<?php if ( $field->is_required() ) : ?>
						<span class="asrs-required" aria-hidden="true">*</span>
					<?php endif; ?>
				</label>
			<?php endif; ?>

			<?php
			switch ( $type ) {
				case 'textarea':
					$rows = isset( $attrs['rows'] ) ? max( 2, (int) $attrs['rows'] ) : 5;
					?>
					<textarea
						id="<?php echo esc_attr( $field_id ); ?>"
						name="fields[<?php echo esc_attr( $name ); ?>]"
						class="asrs-input asrs-textarea"
						rows="<?php echo esc_attr( (string) $rows ); ?>"
						placeholder="<?php echo esc_attr( $field->get_placeholder() ); ?>"
						<?php echo $req_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>><?php echo esc_textarea( $value ); ?></textarea>
					<?php
					break;

				case 'select':
					$is_placeholder = '' === $value ? ' asrs-select-placeholder' : '';
					?>
					<div class="asrs-select-wrap">
						<select
							id="<?php echo esc_attr( $field_id ); ?>"
							name="fields[<?php echo esc_attr( $name ); ?>]"
							class="asrs-input asrs-select<?php echo esc_attr( $is_placeholder ); ?>"
							<?php echo $req_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>>
							<option value=""><?php echo esc_html( $field->get_placeholder() ?: __( 'Select an option...', 'asrs-forms-engine' ) ); ?></option>
							<?php foreach ( $field->get_options() as $opt_val => $opt_label ) : ?>
								<option value="<?php echo esc_attr( (string) $opt_val ); ?>" <?php selected( $value, (string) $opt_val ); ?>>
									<?php echo esc_html( (string) $opt_label ); ?>
								</option>
							<?php endforeach; ?>
						</select>
					</div>
					<?php
					break;

				case 'radio':
					?>
					<div class="asrs-radio-group">
						<?php foreach ( $field->get_options() as $opt_val => $opt_label ) : ?>
							<label class="asrs-radio-option">
								<input type="radio"
									name="fields[<?php echo esc_attr( $name ); ?>]"
									value="<?php echo esc_attr( (string) $opt_val ); ?>"
									<?php checked( $value, (string) $opt_val ); ?>
									<?php echo $req_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
								<span><?php echo esc_html( (string) $opt_label ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
					<?php
					break;

				case 'checkbox':
					?>
					<label class="asrs-checkbox-option" for="<?php echo esc_attr( $field_id ); ?>">
						<input type="checkbox"
							id="<?php echo esc_attr( $field_id ); ?>"
							name="fields[<?php echo esc_attr( $name ); ?>]"
							value="1"
							<?php checked( $value, '1' ); ?>
							<?php echo $req_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
						<span>
							<?php echo esc_html( $field->get_label() ); ?>
							<?php if ( $field->is_required() ) : ?>
								<span class="asrs-required" aria-hidden="true">*</span>
							<?php endif; ?>
						</span>
					</label>
					<?php
					break;

				default:
					$html_type = in_array( $type, array( 'text', 'email', 'url', 'tel', 'number' ), true ) ? $type : 'text';
					?>
					<input type="<?php echo esc_attr( $html_type ); ?>"
						id="<?php echo esc_attr( $field_id ); ?>"
						name="fields[<?php echo esc_attr( $name ); ?>]"
						class="asrs-input"
						value="<?php echo esc_attr( $value ); ?>"
						placeholder="<?php echo esc_attr( $field->get_placeholder() ); ?>"
						<?php echo $req_attr; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?> />
					<?php
					break;
			}
			?>

			<?php if ( '' !== $field->get_help_text() ) : ?>
				<p class="asrs-field-help"><?php echo esc_html( $field->get_help_text() ); ?></p>
			<?php endif; ?>

			<div class="asrs-field-error" role="alert"></div>
		</div>
		<?php
	}
}
