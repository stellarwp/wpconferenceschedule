<?php

/**
 * Provider for Admin Related Functionality.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * Provides the functionality to register and manage post types for the conference.
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
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since TBD
	 */
	public function add_actions() {
		add_action( 'admin_menu', [ $this, 'add_conference_schedule_menu' ] );
		add_action( 'admin_menu', [ $this, 'organize_post_types' ] );
		add_action( 'admin_init', [ $this, 'options_init' ] );
		add_action( 'admin_menu', [ $this, 'options_page' ] );
	}

	/**
	 * Registers the sessions post type.
	 *
	 * @since TBD
	 */
	public function add_conference_schedule_menu() {
		$this->container->make( Menu::class )->add_conference_schedule_menu();
	}

	/**
	 * Organizes the post types under the Conference Schedule menu item.
	 *
	 * @since TBD
	 */
	public function organize_post_types() {
		$this->container->make( Menu::class )->organize_post_types();
	}

	/**
	 * Initializes settings and fields.
	 *
	 * @since TBD
	 */
	public function options_init() {
		$this->container->make( Settings::class )->init();
	}

	/**
	 * Registers options page for settings.
	 *
	 * @since TBD
	 */
	public function options_page() {
		$this->container->make( Settings::class )->options_page();
	}
}
