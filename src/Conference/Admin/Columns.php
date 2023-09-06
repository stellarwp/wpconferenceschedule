<?php
/**
 * Organizes Conference Schedule Custom Columns in the admin list.
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Admin\WP_Query;
use TEC\Conference\Plugin;

/**
 * Class Conference_Schedule
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */
class Columns {

	/**
	 * Runs during pre_get_posts in admin.
	 *
	 * @since TBD
	 *
	 * @param WP_Query $query The WP_Query object.
	 */
	public function admin_sessions_pre_get_posts( $query ) {
		if ( ! is_admin() ) {
			return;
		}

		if ( ! $query->is_main_query() ) {
			return;
		}

		$current_screen = get_current_screen();

		// Order by session time.
		if (
			$current_screen->id === 'edit-wpcs_session'
			&& $query->get( 'orderby' ) === '_wpcs_session_time'
		) {
			$query->set( 'meta_key', '_wpcs_session_time' );
			$query->set( 'orderby', 'meta_value_num' );
		}
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
		switch ( $column ) {
			case 'conference_session_time':
				$session_time = absint( get_post_meta( get_the_ID(), '_wpcs_session_time', true ) );
				$session_time = $session_time ? date( get_option( 'time_format' ), $session_time ) : '&mdash;';
				echo esc_html( $session_time );
				break;
			default:
		}
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
		$current_filter = current_filter();

		switch ( $current_filter ) {
			case 'manage_wpcs_session_posts_columns':
				$columns = array_slice( $columns, 0, 1, true ) +
					['conference_session_time' => __( 'Time', 'wp-conference-schedule' )] +
					array_slice( $columns, 1, null, true );
				break;
			default:
		}

		return $columns;
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
		$current_filter = current_filter();

		if ( $current_filter !== 'manage_edit-wpcs_session_sortable_columns' ) {
			return $sortable;
		}

		$sortable['conference_session_time'] = '_wpcs_session_time';

		return $sortable;
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
		$post = get_post();

		if ( $post->post_type !== Plugin::SESSION_POSTTYPE ) {
			return $states;
		}

		$session_type = get_post_meta( $post->ID, '_wpcs_session_type', true );
		if ( ! in_array( $session_type, [ 'session', 'custom', 'mainstage' ], true ) ) {
			$session_type = 'session';
		}

		if ( $session_type === 'session' ) {
			$states['wpcs-session-type'] = _x( 'Session', 'The session status name to display next to the title in the admin list.', 'wp-conference-schedule' );
		} elseif ( $session_type === 'custom' ) {
			$states['wpcs-session-type'] = _x( 'Custom', 'The session status name to display next to the title in the admin list.', 'wp-conference-schedule' );
		} elseif ( $session_type === 'mainstage' ) {
			$states['wpcs-session-type'] = _x( 'Mainstage', 'The session status name to display next to the title in the admin list.', 'wp-conference-schedule' );
		}

		return $states;
	}
}
