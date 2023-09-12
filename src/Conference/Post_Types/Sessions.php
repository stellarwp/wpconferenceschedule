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
class Sessions extends Abstract_Post_Type {

	/**
	 * @inheritDoc
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

		// Arguments for the post type.
		$args = [
			'labels'          => $sessionlabels,
			'rewrite'         => [ 'slug' => 'sessions', 'with_front' => false ],
			'supports'        => [ 'title', 'editor', 'author', 'revisions', 'thumbnail', 'custom-fields' ],
			'public'          => true,
			'show_ui'         => true,
			'can_export'      => true,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'query_var'       => true,
			'show_in_menu'    => false,
			'show_in_rest'    => true,
			'rest_base'       => 'sessions',
		];

		/**
		 * Filters the arguments for registering the 'sessions' post type.
		 *
		 * @since TBD
		 *
		 * @param array $args The arguments for registering the post type.
		 *
		 * @return array The filtered arguments.
		 */
		apply_filters( 'tec_conference_schedule_sessions_post_type_args', $args );

		$this->post_type_object = register_post_type( Plugin::SESSION_POSTTYPE, $args );
	}

	/**
	 * @inheritDoc
	 */
	public function get_title_text(): string {
		return _x( 'Enter Session Title Here', 'Session title placeholder', 'wp-conference-schedule' );
	}

	/**
	 * Displays custom post types in the "At a Glance" dashboard widget.
	 *
	 * @since TBD
	 */
	public function cpt_at_glance() {
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

	/**
	 * Sets the single template for the session post type.
	 *
	 * @since TBD
	 *
	 * @param string $single_template The single template path.
	 *
	 * @return string The single template path.
	 */
	public function set_single_template( $single_template ) {
		global $post;

		if ( $post->post_type !== Plugin::SESSION_POSTTYPE ) {
			return $single_template;
		}

		return trailingslashit( dirname( CONFERENCE_SCHEDULE_FILE ) ) . 'templates/session-template.php';
	}
}
