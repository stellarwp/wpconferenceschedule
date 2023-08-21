<?php
/**
 * Handles Setup of Speakers Post Types.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Conference\Plugin;

/**
 * Class Speakers
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */
class Speakers {

	/**
	 * Registers the 'wpcs_speaker' post type.
	 *
	 * @since TBD
	 */
	public function register_post_type() {

		// Speaker post type labels.
		$speakerlabels = [
			'name'               => _x( 'Speakers', 'Speaker post type label', 'wp-conference-schedule' ),
			'singular_name'      => _x( 'Speaker', 'Speaker post type label', 'wp-conference-schedule' ),
			'add_new'            => _x( 'Add New', 'Speaker post type label', 'wp-conference-schedule' ),
			'add_new_item'       => _x( 'Create New Speaker', 'Speaker post type label', 'wp-conference-schedule' ),
			'edit'               => _x( 'Edit', 'Speaker post type label', 'wp-conference-schedule' ),
			'edit_item'          => _x( 'Edit Speaker', 'Speaker post type label', 'wp-conference-schedule' ),
			'new_item'           => _x( 'New Speaker', 'Speaker post type label', 'wp-conference-schedule' ),
			'view'               => _x( 'View Speaker', 'Speaker post type label', 'wp-conference-schedule' ),
			'view_item'          => _x( 'View Speaker', 'Speaker post type label', 'wp-conference-schedule' ),
			'search_items'       => _x( 'Search Speakers', 'Speaker post type label', 'wp-conference-schedule' ),
			'not_found'          => _x( 'No speakers found', 'Speaker post type label', 'wp-conference-schedule' ),
			'not_found_in_trash' => _x( 'No speakers found in Trash', 'Speaker post type label', 'wp-conference-schedule' ),
			'parent_item_colon'  => _x( 'Parent Speaker:', 'Speaker post type label', 'wp-conference-schedule' ),
		];

		// Register speaker post type.
		register_post_type( Plugin::SPEAKER_POSTTYPE, [
			'labels'             => $speakerlabels,
			'rewrite'            => [ 'slug' => 'speakers', 'with_front' => false ],
			'supports'           => [ 'title', 'editor', 'revisions', 'thumbnail', 'page-attributes', 'excerpt' ],
			'menu_position'      => 20,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'can_export'         => true,
			'capability_type'    => 'post',
			'hierarchical'       => false,
			'query_var'          => true,
			'show_in_menu'       => false,
			'show_in_rest'       => true,
			'rest_base'          => 'speakers',
			'has_archive'        => false,
		] );
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
	public function change_title_text( $title, $post ) {
		if ( $post->post_type === Plugin::SPEAKER_POSTTYPE ) {
			$title = _x( 'Enter Speaker Full Name Here', 'Speaker title placeholder', 'wp-conference-schedule' );
		}

		return $title;
	}
}
