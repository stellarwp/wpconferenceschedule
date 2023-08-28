<?php

/**
 * Handles Setup of Sponsor Levels Taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Sponsor_Levels
 *
 * Handles the registration and management of the Sponsor Levels taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */
class Sponsor_Levels {

	/**
	 * Registers the 'wpcs_sponsor_level' taxonomy.
	 *
	 * @since TBD
	 */
	public function register_taxonomy() {

		// Labels for sponsor levels.
		$sponsor_level_labels = [
			'name'          => _x( 'Sponsor Levels', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'singular_name' => _x( 'Sponsor Level', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'search_items'  => _x( 'Search Sponsor Levels', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'popular_items' => _x( 'Popular Sponsor Levels', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'all_items'     => _x( 'All Sponsor Levels', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'edit_item'     => _x( 'Edit Sponsor Level', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'update_item'   => _x( 'Update Sponsor Level', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'add_new_item'  => _x( 'Add Sponsor Level', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
			'new_item_name' => _x( 'New Sponsor Level', 'Sponsor Levels taxonomy label', 'wp-conference-schedule' ),
		];

		$args = [
			'labels'            => $sponsor_level_labels,
			'rewrite'           => [ 'slug' => 'sponsor-level' ],
			'query_var'         => 'sponsor-level',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_sponsor_level',
		];

		/**
		 * Filters the arguments for registering the 'wpcsp_sponsor_level' taxonomy.
		 *
		 * @since TBD
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_wpcsp_sponsor_level_taxonomy_args', $args );

		// Register the Sponsor Levels taxonomy.
		register_taxonomy( Plugin::SPONSOR_LEVEL_TAXONOMY, Plugin::SPONSOR_POSTTYPE, $args );
	}
}
