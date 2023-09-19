<?php

/**
 * Provider for Editor Related Functionality.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Editor
 */

namespace TEC\Conference\Editor;

use TEC\Conference\Contracts\Service_Provider;

/**
 * Class Provider
 *
 *
 * @since   TBD
 *
 * @package TEC\Conference\Editor
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 */
	public function register() {
		// Register the SP on the container.
		$this->container->singleton( 'tec.conference.editor.provider', $this );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since TBD
	 */
	protected function add_actions() {
		add_action( 'init', [ $this, 'register_block' ] );
		add_action( 'admin_enqueue_scripts', [ $this, 'register_editor_assets' ] );
	}

	/**
	 * Registers the conference schedule block.
	 *
	 * @since TBD
	 */
	public function register_block() {
		$this->container->make( Block::class )->register_block();
	}

	/**
	 * Registers the editor assets.
	 *
	 * @since TBD
	 */
	public function register_editor_assets() {
		$this->container->make( Assets::class )->register_editor_assets();
	}

	/**
	 * Adds required filters for editor.
	 *
	 * @since TBD
	 */
	protected function add_filters() {}
}
