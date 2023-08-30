<?php

namespace TEC\Conference\Contracts;

use TEC\Conference\Vendor\StellarWP\ContainerContract\ContainerInterface;
use TEC\Conference\Exceptions\Not_Bound_Exception;
use TEC\Conference\Vendor\lucatume\DI52\Container as DI52_Container;

class Container extends DI52_Container implements ContainerInterface {
	/**
	 * Finds an entry of the container by its identifier and returns it.
	 *
	 * @since TBD
	 *
	 * @param string $id A fully qualified class or interface name or an already built object.
	 *
	 * @return mixed The entry for an id.
	 * @throws Not_Bound_Exception Error while retrieving the entry.
	 *
	 */
	public function get( $id ) {
		try {
			return parent::get( $id );
		} catch ( \Exception $e ) {
			// Do not chain the previous exception into ours, as it makes the error log confusing.
			throw new Not_Bound_Exception( $e->getMessage(), $e->getCode() );
		}
	}

	/**
	 * Overrides the parent method to fire an action when a service provider is registered.
	 *
	 * @since TBD
	 *
	 * @param string $serviceProviderClass The service provider class name.
	 * @param string ...$alias             Optional. The alias(es) to register the service provider with.
	 *
	 * @return void
	 *
	 * @throws \TEC\Conference\Vendor\lucatume\DI52\ContainerException If the provider class is marked as deferred but
	 *                                                      does not provide a set of deferred registrations.
	 */
	public function register( $serviceProviderClass, ...$alias ) {
		// Register the provider with the parent container.
		parent::register( $serviceProviderClass, ...$alias );

		/**
		 * Fires when a service provider is registered by the container.
		 *
		 * @since TBD
		 *
		 * @param string        $serviceProviderClass The service provider class name.
		 * @param array<string> $alias                The alias(es) the service provider was registered with.
		 */
		do_action( 'tec_conference_container_registered_provider', $serviceProviderClass, $alias );

		/**
		 * Fires a class-specific action when a service provider is registered by the container.
		 *
		 * @since TBD
		 *
		 * @param array<string> $alias The alias(es) the service provider was registered with.
		 */
		do_action( 'tec_conference_container_registered_provider_' . $serviceProviderClass, $alias );

		if (
			// Back compat with older definition of Service Provider.
			! property_exists( $serviceProviderClass, 'registration_action' )
			// New definition of Service Provider: default action is empty.
			|| empty( $serviceProviderClass::$registration_action )
		) {
			return;
		}

		/**
		 * Fires a custom action defined by the Service Provider when it's registered.
		 *
		 * @since TBD
		 */
		do_action( $serviceProviderClass::$registration_action, $serviceProviderClass, $alias );
	}

	/**
	 * Registers a service provider on a given action is dispatched.
	 *
	 * @since TBD
	 *
	 * @param string $action The action to register the provider on.
	 * @param string $class The service provider class name.
	 * @param string ...$alias Optional. The alias(es) to register the service provider with.
	 *
	 * @return void The Service Provider is registered when the action fires,
	 *               or immediately if the action has already fired.
	 *
	 * @throws \TEC\Conference\Vendor\lucatume\DI52\ContainerException If the provider class is marked as deferred but
	 *                                                      does not provide a set of deferred registrations.
	 */
	public function register_on_action( string $action, string $class, string ...$alias ): void {
		if ( did_action( $action ) ) {
			// If the action has already fired, register the provider immediately.
			$this->register( $class, ...$alias );

			return;
		}

		// If the action has not fired yet, register the provider when it does.
		$registration_closure = function () use ( $action, $class, $alias, &$registration_closure ) {
			// Remove the closure from the action to avoid calling it again.
			remove_action( $action, $registration_closure );
			$this->register( $class, ...$alias );
		};
		add_action( $action, $registration_closure );
	}
}
