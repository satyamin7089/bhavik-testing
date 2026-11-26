<?php
/**
 * Central form registry that auto-discovers forms inside /forms/*.php.
 *
 * @package ASRS\FormsEngine\Forms
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Forms;

use InvalidArgumentException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class FormRegistry {

	/**
	 * Registered form instances keyed by form ID.
	 *
	 * @var array<string, FormInterface>
	 */
	private array $forms = array();

	private bool $initialized = false;

	/**
	 * Auto-load all form definitions from /forms/*.php inside the plugin.
	 */
	public function init(): void {
		if ( $this->initialized ) {
			return;
		}
		$this->initialized = true;

		$this->load_forms_directory( ASRS_FORMS_PATH . 'forms' );

		/**
		 * Optional hook if developers also want to register forms programmatically.
		 *
		 * @param FormRegistry $registry Central form registry.
		 */
		do_action( 'asrs_forms_register', $this );
	}

	/**
	 * Scan a directory for *.php form config files and register them.
	 *
	 * @param string $directory Absolute directory path.
	 */
	public function load_forms_directory( string $directory ): void {
		if ( ! is_dir( $directory ) ) {
			return;
		}

		$files = glob( trailingslashit( $directory ) . '*.php' );
		if ( ! is_array( $files ) ) {
			return;
		}

		sort( $files );

		foreach ( $files as $file ) {
			$definition = require $file;

			if ( $definition instanceof FormInterface ) {
				$this->register( $definition );
				continue;
			}

			if ( is_array( $definition ) ) {
				if ( empty( $definition['id'] ) ) {
					$definition['id'] = basename( $file, '.php' );
				}
				$this->register( new ArrayForm( $definition ) );
			}
		}
	}

	/**
	 * Register a PHP form definition.
	 *
	 * @param FormInterface $form Form instance.
	 * @throws InvalidArgumentException If form ID is empty.
	 */
	public function register( FormInterface $form ): void {
		$id = sanitize_key( $form->get_id() );
		if ( '' === $id ) {
			throw new InvalidArgumentException( 'ASRS Form ID cannot be empty.' );
		}

		$this->forms[ $id ] = $form;
	}

	public function get( string $form_id ): ?FormInterface {
		return $this->forms[ sanitize_key( $form_id ) ] ?? null;
	}

	public function has( string $form_id ): bool {
		return isset( $this->forms[ sanitize_key( $form_id ) ] );
	}

	/**
	 * @return array<string, FormInterface>
	 */
	public function all(): array {
		return $this->forms;
	}

	public static function get_dedicated_shortcode_tag( string $form_id ): string {
		return 'asrs_form_' . str_replace( '-', '_', sanitize_key( $form_id ) );
	}

	/**
	 * @return array{universal: string, dedicated: string}
	 */
	public static function get_shortcodes( string $form_id ): array {
		$safe_id = sanitize_key( $form_id );
		return array(
			'universal' => sprintf( '[asrs_form id="%s"]', $safe_id ),
			'dedicated' => sprintf( '[%s]', self::get_dedicated_shortcode_tag( $safe_id ) ),
		);
	}
}
