<?php

/**
 * Handles Setup of Tags Taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;

/**
 * Class Tags
 *
 * Handles the registration and management of the Tags taxonomy.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */
class Tags extends Abstract_Taxonomy {

	/**
	 * @inheritdoc
	 */
	public function register_taxonomy() {

		// Labels for tags.
		$tag_labels = [
			'name'          => _x( 'Tags', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'singular_name' => _x( 'Tag', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'search_items'  => _x( 'Search Tags', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'popular_items' => _x( 'Popular Tags', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'all_items'     => _x( 'All Tags', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'edit_item'     => _x( 'Edit Tag', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'update_item'   => _x( 'Update Tag', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'add_new_item'  => _x( 'Add Tag', 'Tags taxonomy label', 'wp-conference-schedule' ),
			'new_item_name' => _x( 'New Tag', 'Tags taxonomy label', 'wp-conference-schedule' ),
		];

		$args = [
			'labels'            => $tag_labels,
			'rewrite'           => [ 'slug' => 'session_tag' ],
			'query_var'         => 'session_tag',
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_in_rest'      => true,
			'show_admin_column' => true,
			'rest_base'         => 'session_tag',
		];

		/**
		 * Filters the arguments for registering the 'wpcs_session_tag' taxonomy.
		 *
		 * @since TBD
		 *
		 * @param array $args The arguments for registering the taxonomy.
		 *
		 * @return array The filtered arguments.
		 */
		$args = apply_filters( 'tec_conference_schedule_wpcs_session_tag_taxonomy_args', $args );

		// Register the Tags taxonomy.
		$this->taxonomy_object = register_taxonomy( Plugin::TAGS_TAXONOMY, Plugin::SESSION_POSTTYPE, $args );
	}
}
