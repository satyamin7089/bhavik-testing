<?php
/**
 * Lightweight PSR-style service container.
 *
 * @package ASRS\FormsEngine\Core
 */

declare(strict_types=1);

namespace ASRS\FormsEngine\Core;

use RuntimeException;

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Container {

	/**
	 * Registered factory bindings.
	 *
	 * @var array<string, callable>
	 */
	private array $bindings = array();

	/**
	 * Resolved singleton instances.
	 *
	 * @var array<string, object>
	 */
	private array $instances = array();

	/**
	 * Bind a shared singleton service factory.
	 *
	 * @param string   $id      Service identifier or class name.
	 * @param callable $factory Factory callback receiving this container.
	 */
	public function singleton( string $id, callable $factory ): void {
		$this->bindings[ $id ] = $factory;
	}

	/**
	 * Register an already-instantiated object.
	 *
	 * @param string $id       Service identifier.
	 * @param object $instance Service instance.
	 */
	public function set( string $id, object $instance ): void {
		$this->instances[ $id ] = $instance;
	}

	/**
	 * Retrieve a service instance.
	 *
	 * @param string $id Service identifier.
	 * @return mixed
	 * @throws RuntimeException When service is not bound.
	 */
	public function get( string $id ) {
		if ( isset( $this->instances[ $id ] ) ) {
			return $this->instances[ $id ];
		}

		if ( ! isset( $this->bindings[ $id ] ) ) {
			throw new RuntimeException( sprintf( 'Service "%s" is not registered in container.', $id ) );
		}

		$this->instances[ $id ] = ( $this->bindings[ $id ] )( $this );

		return $this->instances[ $id ];
	}

	/**
	 * Check whether a service exists.
	 *
	 * @param string $id Service identifier.
	 */
	public function has( string $id ): bool {
		return isset( $this->instances[ $id ] ) || isset( $this->bindings[ $id ] );
	}
}
