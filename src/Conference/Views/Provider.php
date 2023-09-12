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

		add_shortcode( 'wpcs_speakers', [ $this, 'render_speakers_shortcode' ] );
		add_shortcode( 'wpcs_sponsors', [ $this, 'render_sponsors_shortcode' ] );

		// Single Session.
		add_action( 'wpsc_single_taxonomies', [ $this, 'single_session_tags' ] );
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
	 * The [wpcs_speakers] shortcode handler.
	 *
	 * @since TBD
	 *
	 * @param array<string|mixed> $attr Array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function render_speakers_shortcode( $props ) {
		return $this->container->make( Shortcode\Schedule::class )->render_shortcode( $props );
	}

	/**
	 * The [wpcs_sponsors] shortcode handler.
	 *
	 * @since TBD
	 *
	 * @param array<string|mixed> $attr Array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function render_sponsors_shortcode( $props ) {
		return $this->container->make( Shortcode\Schedule::class )->render_shortcode( $props );
	}

	/**
	 * Adds single sessions tags.
	 *
	 * @since TBD
	 */
	public function single_session_tags() {
		$this->container->make( Filter_Modifications::class )->single_session_tags();
	}

	/**
	 * Adds required filters for views.
	 *
	 * @since TBD
	 */
	protected function add_filters() {
		// Schedule Shortcode.
		add_filter( 'wpcs_filter_session_speakers', [ $this, 'filter_session_speakers' ], 11, 2 );
		add_filter( 'wpcs_session_content_header', [ $this, 'session_content_header' ], 11, 1 );
		add_filter( 'wpcs_session_content_footer', [ $this, 'session_sponsors' ], 11, 1 );

		// Single Session.
		add_filter( 'wpcs_filter_single_session_speakers', [ $this, 'filter_single_session_speakers' ], 11, 2 );
	}

	/**
	 * Filters session speakers output based on speaker display type.
	 *
	 * @since TBD
	 *
	 * @param string $speakers_typed Predefined speakers typed.
	 * @param int    $session_id     Session post ID.
	 *
	 * @return string HTML output of session speakers.
	 */
	public function filter_session_speakers( $speakers_typed, $session_id ): string {
		return $this->container->make( Filter_Modifications::class )->filter_session_speakers( $speakers_typed, $session_id );
	}

	/**
	 * Generates session content header based on session tags.
	 *
	 * @since TBD
	 *
	 * @param int $session_id Session post ID.
	 *
	 * @return string HTML output of session content header.
	 */
	public function session_content_header( $session_id ) {
		return $this->container->make( Filter_Modifications::class )->session_content_header( $session_id );
	}

	/**
	 * Outputs session sponsors.
	 *
	 * @since TBD
	 *
	 * @param int $session_id The session ID.
	 *
	 * @return string The HTML of the session sponsors or empty string.
	 */
	public function session_sponsors( $session_id ): string {
		return $this->container->make( Filter_Modifications::class )->session_sponsors( $session_id );
	}

	/**
	 * Filters single session speakers output based on speaker display type.
	 *
	 * @since TBD
	 *
	 * @param string $speakers_typed Predefined speakers typed.
	 * @param int    $session_id     Session post ID.
	 *
	 * @return string HTML output of single session speakers.
	 */
	public function filter_single_session_speakers( $speakers_typed, $session_id ): string {
		return $this->container->make( Filter_Modifications::class )->filter_single_session_speakers( $speakers_typed, $session_id );
	}
}
