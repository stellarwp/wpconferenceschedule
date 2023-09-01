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
use TEC\Conference\Admin\Meta\Session as Session_Meta;
use TEC\Conference\Admin\Meta\Speaker as Speaker_Meta;
use TEC\Conference\Admin\Meta\Sponsor as Sponsor_Meta;


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
		$this->add_filters();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since TBD
	 */
	protected function add_actions() {
		add_action( 'admin_menu', [ $this, 'add_conference_schedule_menu' ] );
		add_action( 'admin_menu', [ $this, 'organize_post_types' ] );
		add_action( 'pre_get_posts', [ $this, 'admin_sessions_pre_get_posts' ] );
		add_action( 'manage_posts_custom_column', [ $this, 'manage_post_types_columns_output' ], 10, 2 );

		add_action( 'admin_init', [ $this, 'options_init' ] );
		add_action( 'admin_menu', [ $this, 'options_page' ] );

		add_action( 'save_post', [ $this, 'save_post_session' ], 10, 2 );
		add_action( 'cmb2_admin_init', [ $this, 'session_metabox' ] );
		add_action( 'add_meta_boxes', [ $this, 'add_meta_boxes' ] );

		add_action( 'cmb2_admin_init', [ $this, 'speaker_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'sponsor_metabox' ] );
		add_action( 'cmb2_admin_init', [ $this, 'sponsor_level_metabox' ] );
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
	 * Runs during pre_get_posts in admin.
	 *
	 * @since TBD
	 *
	 * @param WP_Query $query The WP_Query object.
	 */
	public function admin_sessions_pre_get_posts( $query ) {
		$this->container->make( Columns::class )->admin_sessions_pre_get_posts( $query );
	}

	/**
	 * Output for custom columns in the admin screen.
	 *
	 * @since TBD
	 *
	 * @param string $column The name of the current column.
	 * @param int $post_id The ID of the current post.
	 */
	public function manage_post_types_columns_output( string $column, int $post_id ) {
		$this->container->make( Columns::class )->manage_post_types_columns_output( $column, $post_id );
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

	/**
	 * Saves post session details.
	 *
	 * @since TBD
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_post_session( $post_id, $post ) {
		$this->container->make( Session_Meta::class )->save_post_session( $post_id, $post );
	}

	/**
	 * Adds the session information meta box.
	 *
	 * @since TBD
	 */
	public function session_metabox() {
		$this->container->make( Session_Meta::class )->session_metabox();
	}

	/**
	 * Adds meta boxes for the session post type.
	 *
	 * @since TBD
	 */
	public function add_meta_boxes() {
		$this->container->make( Session_Meta::class )->add_meta_boxes();
	}

	/**
	 * Adds the speaker information meta box.
	 *
	 * @since TBD
	 */
	public function speaker_metabox() {
		$this->container->make( Speaker_Meta::class )->speaker_metabox();
	}

	/**
	 * Adds the sponsor information meta box.
	 *
	 * @since TBD
	 */
	public function sponsor_metabox() {
		$this->container->make( Sponsor_Meta::class )->sponsor_metabox();
	}

	public function sponsor_level_metabox() {
		$this->container->make( Sponsor_Meta::class )->sponsor_level_metabox();
	}

	/**
	 * Adds required actions for post types.
	 *
	 * @since TBD
	 */
	protected function add_filters() {
		add_filter( 'manage_wpcs_session_posts_columns',[ $this, 'manage_post_types_columns' ] );
		add_filter( 'manage_edit-wpcs_session_sortable_columns', [ $this, 'manage_sortable_columns' ] );
		add_filter( 'display_post_states', [ $this, 'display_post_states' ] );
		add_filter( 'wpcs_filter_session_speaker_meta_field', [ $this, 'filter_session_speaker_meta_field' ] );
	}

	/**
	 * Adds or modifies the columns in the admin screen for custom post types.
	 *
	 * @since TBD
	 *
	 * @param array $columns The existing columns.
	 *
	 * @return array The modified columns.
	 */
	public function manage_post_types_columns( array $columns ): array {
		return $this->container->make( Columns::class )->manage_post_types_columns( $columns );
	}

	/**
	 * Defines sortable columns in the admin screen.
	 *
	 * @since TBD
	 *
	 * @param array $sortable The existing sortable columns.
	 *
	 * @return array The modified sortable columns.
	 */
	public function manage_sortable_columns( array $sortable ): array {
		return $this->container->make( Columns::class )->manage_sortable_columns( $sortable );
	}

	/**
	 * Displays post states in the admin screen.
	 *
	 * @since TBD
	 *
	 * @param array $states The existing post states.
	 *
	 * @return array The modified post states.
	 */
	public function display_post_states( array $states ): array {
		return $this->container->make( Columns::class )->display_post_states( $states );
	}

	/**
	 * Filters session speaker meta field.
	 *
	 * @since TBD
	 *
	 * @param array $cmb The current CMB2 box object.
	 */
	public function filter_session_speaker_meta_field( $cmb ) {
		return $this->container->make( Speaker_Meta::class )->filter_session_speaker_meta_field( $cmb );
	}
}
