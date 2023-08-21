<?php
/**
 * Handles Setup of Post Types.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Conference\Plugin;

/**
 * Class Sessions
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */
class Sessions {

	/**
	 * Registers the 'wpcs_session' post type.
	 *
	 * @since TBD
	 */
	public function register_post_type() {

		// Session post type labels.
		$sessionlabels = [
			'name'               => _x( 'Sessions', 'Session post type label', 'wp-conference-schedule' ),
			'singular_name'      => _x( 'Session', 'Session post type label', 'wp-conference-schedule' ),
			'add_new'            => _x( 'Add New', 'Session post type label', 'wp-conference-schedule' ),
			'add_new_item'       => _x( 'Create New Session', 'Session post type label', 'wp-conference-schedule' ),
			'edit'               => _x( 'Edit', 'Session post type label', 'wp-conference-schedule' ),
			'edit_item'          => _x( 'Edit Session', 'Session post type label', 'wp-conference-schedule' ),
			'new_item'           => _x( 'New Session', 'Session post type label', 'wp-conference-schedule' ),
			'view'               => _x( 'View Session', 'Session post type label', 'wp-conference-schedule' ),
			'view_item'          => _x( 'View Session', 'Session post type label', 'wp-conference-schedule' ),
			'search_items'       => _x( 'Search Sessions', 'Session post type label', 'wp-conference-schedule' ),
			'not_found'          => _x( 'No sessions found', 'Session post type label', 'wp-conference-schedule' ),
			'not_found_in_trash' => _x( 'No sessions found in Trash', 'Session post type label', 'wp-conference-schedule' ),
			'parent_item_colon'  => _x( 'Parent Session:', 'Session post type label', 'wp-conference-schedule' ),
		];

		// Register session post type.
		register_post_type( Plugin::SESSION_POSTTYPE, [
			'labels'          => $sessionlabels,
			'rewrite'         => [ 'slug' => 'sessions', 'with_front' => false ],
			'supports'        => [ 'title', 'editor', 'author', 'revisions', 'thumbnail', 'custom-fields' ],
			'menu_position'   => 21,
			'public'          => true,
			'show_ui'         => true,
			'can_export'      => true,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'query_var'       => true,
			'show_in_menu'    => false,
			'show_in_rest'    => true,
			'rest_base'       => 'sessions',
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
		if ( $post->post_type === Plugin::SESSION_POSTTYPE ) {
			$title = _x( 'Enter Session Title Here', 'Session title placeholder', 'wp-conference-schedule' );
		}

		return $title;
	}

	/**
	 * Displays custom post types in the "At a Glance" dashboard widget.
	 *
	 * @since TBD
	 */
	function cpt_at_glance() {
		$args     = [
			'public'   => true,
			'_builtin' => false
		];
		$output   = 'object';
		$operator = 'and';

		$post_types = get_post_types( $args, $output, $operator );
		foreach ( $post_types as $post_type ) {
			$num_posts = wp_count_posts( $post_type->name );
			$num       = number_format_i18n( $num_posts->publish );
			$text      = _n( $post_type->labels->singular_name, $post_type->labels->name, intval( $num_posts->publish ) );
			if ( current_user_can( 'edit_posts' ) ) {
				$output = '<a href="edit.php?post_type=' . $post_type->name . '">' . $num . ' ' . $text . '</a>';
				echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
			} else {
				$output = '<span>' . $num . ' ' . $text . '</span>';
				echo '<li class="post-count ' . $post_type->name . '-count">' . $output . '</li>';
			}
		}
	}

}
