<?php
/**
 * Shortcodes: [asrs_careers] and [asrs_careers_apply].
 *
 * @package ASRS\RecruitEngine\Shortcode
 */

declare(strict_types=1);

namespace ASRS\RecruitEngine\Shortcode;

use ASRS\RecruitEngine\Frontend\CareersRenderer;
use ASRS\RecruitEngine\Zoho\RecruitService;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ShortcodeManager {

	private CareersRenderer $renderer;
	private RecruitService $recruit;

	public function __construct( CareersRenderer $renderer, RecruitService $recruit ) {
		$this->renderer = $renderer;
		$this->recruit  = $recruit;
	}

	public function register(): void {
		add_shortcode( 'asrs_careers', array( $this, 'render_careers' ) );
		add_shortcode( 'asrs_careers_apply', array( $this, 'render_apply' ) );
	}

	/**
	 * @param array<string, string>|string $atts
	 */
	public function render_careers( $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'department' => '',
				'heading'    => '',
				'subtitle'   => '',
			),
			is_array( $atts ) ? $atts : array(),
			'asrs_careers'
		);

		$filtered = array_filter( $atts, static fn( string $v ): bool => '' !== $v );
		return $this->renderer->render_portal( $filtered );
	}

	/**
	 * @param array<string, string>|string $atts
	 */
	public function render_apply( $atts = array() ): string {
		$atts = shortcode_atts(
			array(
				'job_id'    => '',
				'job_title' => '',
			),
			is_array( $atts ) ? $atts : array(),
			'asrs_careers_apply'
		);

		$job_id    = sanitize_text_field( (string) $atts['job_id'] );
		$job_title = sanitize_text_field( (string) $atts['job_title'] );

		if ( '' !== $job_id && '' === $job_title ) {
			$job = $this->recruit->find_cached_job( $job_id );
			if ( $job ) {
				$job_title = (string) $job['title'];
			}
		}

		return $this->renderer->render_application_form( $job_id, $job_title );
	}
}
