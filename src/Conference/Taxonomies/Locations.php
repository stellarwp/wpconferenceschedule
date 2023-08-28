<?php

/**
 * Handles Setup of Locations Taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Locations
 *
 * Handles the registration and management of the Locations taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */
class Locations {

	/**
	 * Registers the 'wpcs_location' taxonomy.
	 *
	 * @since TBD
	 */
	public function register_taxonomy() {

		// Labels for locations.
		$location_labels = [
			'name'          => _x( 'Locations', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'singular_name' => _x( 'Location', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'search_items'  => _x( 'Search Locations', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'popular_items' => _x( 'Popular Locations', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'all_items'     => _x( 'All Locations', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'edit_item'     => _x( 'Edit Location', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'update_item'   => _x( 'Update Location', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'add_new_item'  => _x( 'Add Location', 'Locations taxonomy label', 'wp-conference-schedule' ),
			'new_item_name' => _x( 'New Location', 'Locations taxonomy label', 'wp-conference-schedule' ),
		];

		$args = [
			'labels'            => $location_labels,
			'rewrite'           => [ 'slug' => 'location' ],
			'query_var'         => 'location',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_location',
		];

		/**
		 * Filters the arguments for registering the 'wpcs_location' taxonomy.
		 *
		 * @since TBD
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_wpcs_location_taxonomy_args', $args );

		// Register the Locations taxonomy.
		register_taxonomy( Plugin::LOCATION_TAXONOMY, Plugin::SESSION_POSTTYPE, $args );
	}
}
