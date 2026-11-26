<?php
/**
 * Automatic Shortcode Manager for all PHP-registered forms.
 *
 * Registers:
 *  1) [asrs_form id="form-slug"]
 *  2) [asrs_form_form_slug] automatically for every registered form.
 *
 * @package ASRS\FormsEngine\Shortcode
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Shortcode;

use ASRS\FormsEngine\Forms\FormRegistry;
use ASRS\FormsEngine\Frontend\FormRenderer;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ShortcodeManager {

	private FormRegistry $registry;
	private FormRenderer $renderer;

	public function __construct( FormRegistry $registry, FormRenderer $renderer ) {
		$this->registry = $registry;
		$this->renderer = $renderer;
	}

	/**
	 * Register universal and per-form shortcodes.
	 */
	public function register_shortcodes(): void {
		add_shortcode( 'asrs_form', array( $this, 'render_universal_shortcode' ) );

		foreach ( $this->registry->all() as $form ) {
			$tag     = FormRegistry::get_dedicated_shortcode_tag( $form->get_id() );
			$form_id = $form->get_id();

			add_shortcode(
				$tag,
				function ( $atts = array() ) use ( $form_id ): string {
					$atts       = is_array( $atts ) ? $atts : array();
					$atts['id'] = $form_id;
					return $this->render_universal_shortcode( $atts );
				}
			);
		}
	}

	/**
	 * Render [asrs_form id="..."] shortcode.
	 *
	 * @param array<string, mixed>|string $atts Shortcode attributes.
	 */
	public function render_universal_shortcode( $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'id'         => '',
				'show_title' => 'true',
				'class'      => '',
			),
			is_array( $atts ) ? $atts : array(),
			'asrs_form'
		);

		$form_id = sanitize_key( (string) $atts['id'] );
		if ( '' === $form_id ) {
			return current_user_can( 'manage_options' )
				? '<p class="asrs-shortcode-error">' . esc_html__( 'ASRS Forms Error: Missing "id" attribute in shortcode.', 'asrs-forms-engine' ) . '</p>'
				: '';
		}

		$form = $this->registry->get( $form_id );
		if ( ! $form ) {
			return current_user_can( 'manage_options' )
				? '<p class="asrs-shortcode-error">' . sprintf(
					/* translators: %s: form id */
					esc_html__( 'ASRS Forms Error: Form "%s" is not registered.', 'asrs-forms-engine' ),
					esc_html( $form_id )
				) . '</p>'
				: '';
		}

		return $this->renderer->render( $form, $atts );
	}
}
