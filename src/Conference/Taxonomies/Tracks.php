<?php

/**
 * Handles Setup of Tracks Taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Tracks
 *
 * Handles the registration and management of the Tracks taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */
class Tracks extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for tracks.
		$track_labels = [
			'name'          => _x( 'Tracks', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'singular_name' => _x( 'Track', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'search_items'  => _x( 'Search Tracks', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'popular_items' => _x( 'Popular Tracks', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'all_items'     => _x( 'All Tracks', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'edit_item'     => _x( 'Edit Track', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'update_item'   => _x( 'Update Track', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'add_new_item'  => _x( 'Add Track', 'Tracks taxonomy label', 'wp-conference-schedule' ),
			'new_item_name' => _x( 'New Track', 'Tracks taxonomy label', 'wp-conference-schedule' ),
		];

		$args = [
			'labels'            => $track_labels,
			'rewrite'           => [ 'slug' => 'track' ],
			'query_var'         => 'track',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_track',
		];

		/**
		 * Filters the arguments for registering the 'wpcs_track' taxonomy.
		 *
		 * @since TBD
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_wpcs_track_taxonomy_args', $args );

		// Register the Tracks taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::TRACK_TAXONOMY, Plugin::SESSION_POSTTYPE, $args );
	}
}
