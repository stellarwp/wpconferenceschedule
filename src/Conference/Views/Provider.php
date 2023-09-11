<?php

/**
 * Provider for Views of the plugin.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Views
 */

namespace TEC\Conference\Views;

use TEC\Conference\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * Provides the functionality to register and manage views for the plugin.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 */
	public function register() {
		// Register the SP on the container.
		$this->container->singleton( 'tec.conference.admin.provider', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds required actions for views.
	 *
	 * @since TBD
	 */
	protected function add_actions() {
		add_shortcode( 'wpcs_schedule', [ $this, 'render_schedule_shortcode' ] );
		add_action( 'wp_enqueue_scripts', [ $this, 'register_views_assets' ] );
	}

	/**
	 * Schedule Block and Shortcode Dynamic content Output.
	 *
	 * @since TBD
	 *
	 * @param array<string|mixed> $attr Array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function render_schedule_shortcode( $props ) {
		return $this->container->make( Shortcode\Schedule::class )->render_shortcode( $props );
	}

	/**
	 * Registers the view assets.
	 *
	 * @since TBD
	 */
	public function register_views_assets() {
		$this->container->make( Assets::class )->register_views_assets();
	}

	/**
	 * Adds required filters for views.
	 *
	 * @since TBD
	 */
	protected function add_filters() {
	}

}
