<?php
/**
 * Immutable form field definition value object.
 *
 * @package ASRS\FormsEngine\Forms
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Forms;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Field {

	private string $name;
	private string $label;
	private string $type;
	private bool $required;
	private string $placeholder;
	private string $default_value;
	private string $help_text;
	private string $css_class;
	private string $width;

	/**
	 * Select/Radio options as associative array [value => label].
	 *
	 * @var array<string, string>
	 */
	private array $options;

	/**
	 * Additional HTML attributes and validation hints.
	 *
	 * @var array<string, mixed>
	 */
	private array $attributes;

	/**
	 * Validation constraints (min_length, max_length, pattern).
	 *
	 * @var array<string, mixed>
	 */
	private array $rules;

	/**
	 * Construct a Field from configuration array.
	 *
	 * @param array<string, mixed> $config Field settings.
	 */
	public function __construct( array $config ) {
		$this->name          = sanitize_key( (string) ( $config['name'] ?? '' ) );
		$this->label         = (string) ( $config['label'] ?? ucfirst( str_replace( '_', ' ', $this->name ) ) );
		$this->type          = sanitize_key( (string) ( $config['type'] ?? 'text' ) );
		$this->required      = ! empty( $config['required'] );
		$this->placeholder   = (string) ( $config['placeholder'] ?? '' );
		$this->default_value = (string) ( $config['default'] ?? '' );
		$this->help_text     = (string) ( $config['help_text'] ?? '' );
		$this->css_class     = (string) ( $config['css_class'] ?? '' );
		$width               = sanitize_key( (string) ( $config['width'] ?? 'full' ) );
		$this->width         = in_array( $width, array( 'half', 'full' ), true ) ? $width : 'full';
		$this->attributes    = is_array( $config['attributes'] ?? null ) ? $config['attributes'] : array();
		$this->rules         = is_array( $config['rules'] ?? null ) ? $config['rules'] : array();

		$raw_options   = is_array( $config['options'] ?? null ) ? $config['options'] : array();
		$this->options = array();
		foreach ( $raw_options as $key => $val ) {
			if ( is_int( $key ) ) {
				$this->options[ (string) $val ] = (string) $val;
			} else {
				$this->options[ (string) $key ] = (string) $val;
			}
		}
	}

	/**
	 * Fluent factory helper.
	 *
	 * @param string               $name   Field name.
	 * @param string               $label  Human label.
	 * @param string               $type   Input type (text, email, select, textarea, tel, url, number, checkbox, radio, hidden).
	 * @param array<string, mixed> $config Additional field config.
	 */
	public static function make( string $name, string $label, string $type = 'text', array $config = array() ): self {
		$config['name']  = $name;
		$config['label'] = $label;
		$config['type']  = $type;
		return new self( $config );
	}

	public function get_name(): string {
		return $this->name;
	}

	public function get_label(): string {
		return $this->label;
	}

	public function get_type(): string {
		return $this->type;
	}

	public function is_required(): bool {
		return $this->required;
	}

	public function get_placeholder(): string {
		return $this->placeholder;
	}

	public function get_default_value(): string {
		return $this->default_value;
	}

	public function get_help_text(): string {
		return $this->help_text;
	}

	public function get_css_class(): string {
		return $this->css_class;
	}

	public function get_width(): string {
		return $this->width;
	}

	/**
	 * @return array<string, string>
	 */
	public function get_options(): array {
		return $this->options;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_attributes(): array {
		return $this->attributes;
	}

	/**
	 * @return array<string, mixed>
	 */
	public function get_rules(): array {
		return $this->rules;
	}

	/**
	 * Convert field definition to serializable array for admin inspection.
	 *
	 * @return array<string, mixed>
	 */
	public function to_array(): array {
		return array(
			'name'        => $this->name,
			'label'       => $this->label,
			'type'        => $this->type,
			'required'    => $this->required,
			'placeholder' => $this->placeholder,
			'default'     => $this->default_value,
			'help_text'   => $this->help_text,
			'width'       => $this->width,
			'options'     => $this->options,
			'rules'       => $this->rules,
		);
	}
}
