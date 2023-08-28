<?php

/**
 * Provider for Taxonomy Related Functionality.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * Provides the functionality to register and manage post types for the conference.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 */
	public function register() {
		// Register the SP on the container.
		$this->container->singleton( 'tec.conference.taxonomies.provider', $this );
		$this->container->singleton( Tracks::class, Tracks::class );
		$this->container->singleton( Locations::class, Locations::class );
		$this->container->singleton( Tags::class, Tags::class );
		$this->container->singleton( Sponsor_Levels::class, Sponsor_Levels::class );
		$this->container->singleton( Groups::class, Groups::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds required actions for taxonomies.
	 *
	 * @since TBD
	 */
	protected function add_actions() {
		add_action( 'init', [ $this, 'register_tracks_taxonomy' ] );
		add_action( 'init', [ $this, 'register_locations_taxonomy' ] );
		add_action( 'init', [ $this, 'register_tags_taxonomy' ] );
		add_action( 'init', [ $this, 'register_sponsor_level_taxonomy' ] );
		add_action( 'init', [ $this, 'register_groups_taxonomy' ] );
	}

	/**
	 * Registers the tracks taxonomy.
	 *
	 * @since TBD
	 */
	public function register_tracks_taxonomy() {
		$this->container->make( Tracks::class )->register_taxonomy();
	}

	/**
	 * Registers the locations taxonomy.
	 *
	 * @since TBD
	 */
	public function register_locations_taxonomy() {
		$this->container->make( Locations::class )->register_taxonomy();
	}

	/**
	 * Registers the tags taxonomy.
	 *
	 * @since TBD
	 */
	public function register_tags_taxonomy() {
		$this->container->make( Tags::class )->register_taxonomy();
	}

	/**
	 * Registers the sponsor level taxonomy.
	 *
	 * @since TBD
	 */
	public function register_sponsor_level_taxonomy() {
		$this->container->make( Sponsor_Levels::class )->register_taxonomy();
	}

	/**
	 * Registers the groups taxonomy.
	 *
	 * @since TBD
	 */
	public function register_groups_taxonomy() {
		$this->container->make( Groups::class )->register_taxonomy();
	}

	/**
	 * Adds required filters for taxonomies.
	 *
	 * @since TBD
	 */
	protected function add_filters() {

	}
}
