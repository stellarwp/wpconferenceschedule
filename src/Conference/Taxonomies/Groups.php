<?php

/**
 * Handles Setup of Groups Taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Groups
 *
 * Handles the registration and management of the Groups taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */
class Groups extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for groups.
		$group_labels = [
			'name'          => _x( 'Groups', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'singular_name' => _x( 'Group', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'search_items'  => _x( 'Search Groups', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'popular_items' => _x( 'Popular Groups', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'all_items'     => _x( 'All Groups', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'edit_item'     => _x( 'Edit Group', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'update_item'   => _x( 'Update Group', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'add_new_item'  => _x( 'Add Group', 'Groups taxonomy label', 'wp-conference-schedule' ),
			'new_item_name' => _x( 'New Group', 'Groups taxonomy label', 'wp-conference-schedule' ),
		];

		$args = [
			'labels'            => $group_labels,
			'rewrite'           => [ 'slug' => 'session_group' ],
			'query_var'         => 'session_group',
			'hierarchical'      => true,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_group',
		];

		/**
		 * Filters the arguments for registering the 'wpcsp_group' taxonomy.
		 *
		 * @since TBD
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_wpcsp_group_taxonomy_args', $args );

		// Register the Groups taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::GROUP_TAXONOMY, Plugin::SPEAKER_POSTTYPE, $args );
	}
}
