<?php

/**
 * Provider for Post Type Related Functionality.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Common\Contracts\Service_Provider;

/**
 * Class Provider
 *
 * Provides the functionality to register and manage post types for the conference.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */
class Provider extends Service_Provider {

	/**
	 * Binds and sets up implementations.
	 *
	 * @since TBD
	 */
	public function register() {
		// Register the SP on the container.
		$this->container->singleton( 'tec.conference.posttype.provider', $this );
		$this->container->singleton( Sessions::class, Sessions::class );
		$this->container->singleton( Speakers::class, Speakers::class );
		$this->container->singleton( Sponsors::class, Sponsors::class );

		$this->add_actions();
		$this->add_filters();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since TBD
	 */
	public function add_actions() {
		// Sessions.
		add_action( 'init', [ $this, 'register_sessions_post_type' ] );
		add_action( 'dashboard_glance_items', [ $this, 'sessions_cpt_at_glance' ] );

		// Speakers.
		add_action( 'init', [ $this, 'register_speakers_post_type' ] );

		// Sponsors.
		add_action( 'init', [ $this, 'register_sponsors_post_type' ] );
	}

	/**
	 * Registers the sessions post type.
	 *
	 * @since TBD
	 */
	public function register_sessions_post_type() {
		$this->container->make( Sessions::class )->register_post_type();
	}

	/**
	 * Displays custom post types in the "At a Glance" dashboard widget.
	 *
	 * @since TBD
	 */
	public function sessions_cpt_at_glance() {
		$this->container->make( Sessions::class )->cpt_at_glance();
	}

	/**
	 * Registers the speakers post type.
	 *
	 * @since TBD
	 */
	public function register_speakers_post_type() {
		$this->container->make( Speakers::class )->register_post_type();
	}

	/**
	 * Registers the sponsors post type.
	 *
	 * @since TBD
	 */
	public function register_sponsors_post_type() {
		$this->container->make( Sponsors::class )->register_post_type();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since TBD
	 */
	public function add_filters() {
		// Sessions.
		add_action( 'enter_title_here', [ $this, 'change_sessions_title_text' ], 10, 2 );

		// Speakers.
		add_action( 'enter_title_here', [ $this, 'change_speakers_title_text' ], 10, 2 );

		// Sponsors.
		add_action( 'enter_title_here', [ $this, 'change_sponsors_title_text' ], 10, 2 );
	}

	/**
	 * Changes the title placeholder text for the 'Sessions' post type.
	 *
	 * @since TBD
	 *
	 * @param string  $title The current placeholder text.
	 * @param WP_Post $post  The current post object.
	 *
	 * @return string The modified placeholder text.
	 */
	public function change_sessions_title_text( $title, $post ) {
		return $this->container->make( Sessions::class )->change_title_text( $title, $post );
	}

	/**
	 * Changes the title placeholder text for the 'Speakers' post type.
	 *
	 * @since TBD
	 *
	 * @param string  $title The current placeholder text.
	 * @param WP_Post $post  The current post object.
	 *
	 * @return string The modified placeholder text.
	 */
	public function change_speakers_title_text( $title, $post ) {
		return $this->container->make( Speakers::class )->change_title_text( $title, $post );
	}

	/**
	 * Changes the title placeholder text for the 'Sponsors' post type.
	 *
	 * @since TBD
	 *
	 * @param string  $title The current placeholder text.
	 * @param WP_Post $post  The current post object.
	 *
	 * @return string The modified placeholder text.
	 */
	public function change_sponsors_title_text( $title, $post ) {
		return $this->container->make( Sponsors::class )->change_title_text( $title, $post );
	}
}
