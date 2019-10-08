<?php

/**
 * [schedule] shortcode and block functions.
 */

defined( 'WPINC' ) || die();

/**
 * Return an associative array of term_id -> term object mapping for all selected tracks.
 *
 * In case of 'all' is used as a value for $selected_tracks, information for all available tracks
 * gets returned.
 *
 * @param string $selected_tracks Comma-separated list of tracks to display or 'all'.
 *
 * @return array Associative array of terms with term_id as the key.
 */
function wpcs_get_schedule_tracks( $selected_tracks ) {
	$tracks = array();
	if ( 'all' === $selected_tracks ) {
		// Include all tracks.
		$tracks = get_terms( 'wpcs_track' );
	} else {
		// Loop through given tracks and look for terms.
		$terms = array_map( 'trim', explode( ',', $selected_tracks ) );

		foreach ( $terms as $term_slug ) {
			$term = get_term_by( 'slug', $term_slug, 'wpcs_track' );
			if ( $term ) {
				$tracks[ $term->term_id ] = $term;
			}
		}
	}

	return $tracks;
}

/**
 * Return a time-sorted associative array mapping timestamp -> track_id -> session id.
 *
 * @param string $schedule_date               Date for which the sessions should be retrieved.
 * @param bool   $tracks_explicitly_specified True if tracks were explicitly specified in the shortcode,
 *                                            false otherwise.
 * @param array  $tracks                      Array of terms for tracks from wpcs_get_schedule_tracks().
 *
 * @return array Associative array of session ids by time and track.
 */
function wpcs_get_schedule_sessions( $schedule_date, $tracks_explicitly_specified, $tracks ) {
	$query_args = array(
		'post_type'      => 'wpcs_session',
		'posts_per_page' => - 1,
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_wpcs_session_time',
				'compare' => 'EXISTS',
			),
		),
	);

	if ( $schedule_date && strtotime( $schedule_date ) ) {
		$query_args['meta_query'][] = array(
			'key'     => '_wpcs_session_time',
			'value'   => array(
				strtotime( $schedule_date ),
				strtotime( $schedule_date . ' +1 day' ),
			),
			'compare' => 'BETWEEN',
			'type'    => 'NUMERIC',
		);
	}

	if ( $tracks_explicitly_specified ) {
		// If tracks were provided, restrict the lookup in WP_Query.
		if ( ! empty( $tracks ) ) {
			$query_args['tax_query'][] = array(
				'taxonomy' => 'wpcs_track',
				'field'    => 'id',
				'terms'    => array_values( wp_list_pluck( $tracks, 'term_id' ) ),
			);
		}
	}

	// Loop through all sessions and assign them into the formatted
	// $sessions array: $sessions[ $time ][ $track ] = $session_id
	// Use 0 as the track ID if no tracks exist.
	$sessions       = array();
	$sessions_query = new WP_Query( $query_args );

	foreach ( $sessions_query->posts as $session ) {
		$time  = absint( get_post_meta( $session->ID, '_wpcs_session_time', true ) );
		$terms = get_the_terms( $session->ID, 'wpcs_track' );

		if ( ! isset( $sessions[ $time ] ) ) {
			$sessions[ $time ] = array();
		}

		if ( empty( $terms ) ) {
			$sessions[ $time ][0] = $session->ID;
		} else {
			foreach ( $terms as $track ) {
				$sessions[ $time ][ $track->term_id ] = $session->ID;
			}
		}
	}

	// Sort all sessions by their key (timestamp).
	ksort( $sessions );

	return $sessions;
}

/**
 * Return an array of columns identified by term ids to be used for schedule table.
 *
 * @param array $tracks                      Array of terms for tracks from wpcs_get_schedule_tracks().
 * @param array $sessions                    Array of sessions from wpcs_get_schedule_sessions().
 * @param bool  $tracks_explicitly_specified True if tracks were explicitly specified in the shortcode,
 *                                           false otherwise.
 *
 * @return array Array of columns identified by term ids.
 */
function wpcs_get_schedule_columns( $tracks, $sessions, $tracks_explicitly_specified ) {
	$columns = array();

	// Use tracks to form the columns.
	if ( $tracks ) {
		foreach ( $tracks as $track ) {
			$columns[ $track->term_id ] = $track->term_id;
		}
	} else {
		$columns[0] = 0;
	}

	// Remove empty columns unless tracks have been explicitly specified.
	if ( ! $tracks_explicitly_specified ) {
		$used_terms = array();

		foreach ( $sessions as $time => $entry ) {
			if ( is_array( $entry ) ) {
				foreach ( $entry as $term_id => $session_id ) {
					$used_terms[ $term_id ] = $term_id;
				}
			}
		}

		$columns = array_intersect( $columns, $used_terms );
		unset( $used_terms );
	}

	return $columns;
}

/**
 * Update and preprocess input attributes for [schedule] shortcode.
 *
 * @param array $attr Array of attributes from shortcode.
 *
 * @return array Array of attributes, after preprocessing.
 */
function wpcs_preprocess_schedule_attributes( $props ) {

	// Set Defaults
	$attr = array(
		'date'         => null,
		'tracks'       => 'all',
		'session_link' => 'permalink', // permalink|anchor|none
		'color_scheme' => 'light', // light/dark
		'align'        => '' // alignwide|alignfull
	);

	// Check if props exist. Fixes PHP errors when shortcode doesn't have any attributes.
	if($props):

		// Set Attribute values base on props
		if($props['date'])
			$attr['date'] = $props['date'];
		
		if($props['color_scheme'])
			$attr['color_scheme'] = $props['color_scheme'];
		
		if($props['session_link'])
			$attr['session_link'] = $props['session_link'];

		if($props['align'] == 'wide')
			$attr['align'] = 'alignwide';
		elseif($props['align'] == 'full')
			$attr['align'] = 'alignfull';
		
		if($props['tracks'])
			$attr['tracks'] = $props['tracks'];

		foreach ( array( 'tracks', 'session_link', 'color_scheme' ) as $key_for_case_sensitive_value ) {
			$attr[ $key_for_case_sensitive_value ] = strtolower( $attr[ $key_for_case_sensitive_value ] );
		}

		if ( ! in_array( $attr['session_link'], array( 'permalink', 'anchor', 'none' ), true ) ) {
			$attr['session_link'] = 'permalink';
		}

	endif;

	return $attr;
}

/**
 * Schedule Block and Shortcode Dynamic content Output.
 *
 * @param array $attr Array of attributes from shortcode.
 *
 * @return array Array of attributes, after preprocessing.
 */
function wpcs_scheduleOutput( $props ) {

	$attr                        = wpcs_preprocess_schedule_attributes( $props );
	$tracks                      = wpcs_get_schedule_tracks( $attr['tracks'] );
	$tracks_explicitly_specified = 'all' !== $attr['tracks'];
	$sessions                    = wpcs_get_schedule_sessions( $attr['date'], $tracks_explicitly_specified, $tracks );
	$columns                     = wpcs_get_schedule_columns( $tracks, $sessions, $tracks_explicitly_specified );

	$html = '<div class="wpcs-schedule-wrapper '.$attr['align'].'">';
	$html .= '<table class="wpcs-schedule wpcs-color-scheme-'.$attr['color_scheme'].'" border="0">';
	$html .= '<thead>';
	$html .= '<tr>';

	// Table headings.
	$html .= '<th class="wpcs-col-time">' . esc_html__( 'Time', 'wp-conference-schedule' ) . '</th>';
	foreach ( $columns as $term_id ) {
		$track = get_term( $term_id, 'wpcs_track' );
		$html .= sprintf(
			'<th class="wpcs-col-track"> <span class="wpcs-track-name">%s</span> <span class="wpcs-track-description">%s</span> </th>',
			isset( $track->term_id ) ? esc_html( $track->name ) : '',
			isset( $track->term_id ) ? esc_html( $track->description ) : ''
		);
	}

	$html .= '</tr>';
	$html .= '</thead>';

	$html .= '<tbody>';

	$time_format = get_option( 'time_format', 'g:i a' );

	foreach ( $sessions as $time => $entry ) {

		$skip_next = $colspan = 0;

		$columns_html = '';
		foreach ( $columns as $key => $term_id ) {

			// Allow the below to skip some items if needed.
			if ( $skip_next > 0 ) {
				$skip_next--;
				continue;
			}

			// For empty items print empty cells.
			if ( empty( $entry[ $term_id ] ) ) {
				$columns_html .= '<td class="wpcs-session-empty"></td>';
				continue;
			}

			// For custom labels print label and continue.
			if ( is_string( $entry[ $term_id ] ) ) {
				$columns_html .= sprintf( '<td colspan="%d" class="wpcs-session-custom">%s</td>', count( $columns ), esc_html( $entry[ $term_id ] ) );
				break;
			}

			// Gather relevant data about the session
			$colspan              = 1;
			$classes              = array();
			$session              = get_post( $entry[ $term_id ] );
			$session_title        = apply_filters( 'the_title', $session->post_title );
			$session_tracks       = get_the_terms( $session->ID, 'wpcs_track' );
			$session_track_titles = is_array( $session_tracks ) ? implode( ', ', wp_list_pluck( $session_tracks, 'name' ) ) : '';
			$session_type         = get_post_meta( $session->ID, '_wpcs_session_type', true );
			$speakers         		= get_post_meta( $session->ID, '_wpcs_session_speakers', true );


			if ( ! in_array( $session_type, array( 'session', 'custom', 'mainstage') ) ) {
				$session_type = 'session';
			}

			// Add CSS classes to help with custom styles
			if ( is_array( $session_tracks ) ) {
				foreach ( $session_tracks as $session_track ) {
					$classes[] = 'wpcs-track-' . $session_track->slug;
				}
			}

			$classes[] = 'wpcs-session-type-' . $session_type;
			$classes[] = 'wpcs-session-' . $session->post_name;

			$content = '';
			$content .= '<div class="wpcs-session-cell-content">';

			// Determine the session title
			if ( 'permalink' == $attr['session_link'] && ('session' == $session_type || 'mainstage' == $session_type) )
				$session_title_html = sprintf( '<h3><a class="wpcs-session-title" href="%s">%s</a></h3>', esc_url( get_permalink( $session->ID ) ), $session_title );
			elseif ( 'anchor' == $attr['session_link'] && ('session' == $session_type || 'mainstage' == $session_type) )
				$session_title_html = sprintf( '<h3><a class="wpcs-session-title" href="%s">%s</a></h3>', esc_url( get_permalink( $session->ID ).'#'.get_post_field( 'post_name', $session->ID ) ), $session_title );
			else
				$session_title_html = sprintf( '<h3><span class="wpcs-session-title">%s</span></h3>', $session_title );

			$content .= $session_title_html;

			// Add speakers names to the output string.
			if ($speakers) {
				$content .= sprintf( ' <span class="wpcs-session-speakers">%s</span>', esc_html($speakers));
			}

			// Session Content Footer Filter
			$wpcs_session_content_footer = apply_filters( 'wpcs_session_content_footer', $session->ID);
			$content .= ($wpcs_session_content_footer != $session->ID) ? $wpcs_session_content_footer : '';

			// End of cell-content.
			$content .= '</div>';

			$columns_clone = $columns;

			// If the next element in the table is the same as the current one, use colspan
			if ( $key != key( array_slice( $columns, -1, 1, true ) ) ) {
				while ( $pair = each( $columns_clone ) ) {
					if ( $pair['key'] == $key ) {
						continue;
					}

					if ( ! empty( $entry[ $pair['value'] ] ) && $entry[ $pair['value'] ] == $session->ID ) {
						$colspan++;
						$skip_next++;
					} else {
						break;
					}
				}
			}

			$columns_html .= sprintf( '<td colspan="%d" class="%s" data-track-title="%s" data-session-id="%s">%s</td>', $colspan, esc_attr( implode( ' ', $classes ) ), $session_track_titles, esc_attr( $session->ID ), $content );
		}

		$global_session      = $colspan == count( $columns ) ? ' wpcs-global-session'.' wpcs-global-session-'.esc_html($session_type) : '';
		$global_session_slug = $global_session ? ' ' . sanitize_html_class( sanitize_title_with_dashes( $session->post_title ) ) : '';

		$html .= sprintf( '<tr class="%s">', sanitize_html_class( 'wpcs-time-' . date( $time_format, $time ) ) . $global_session . $global_session_slug );
		$html .= sprintf( '<td class="wpcs-time">%s</td>', str_replace( ' ', '&nbsp;', esc_html( date( $time_format, $time ) ) ) );
		$html .= $columns_html;
		$html .= '</tr>';
	}

	$html .= '</tbody>';
	$html .= '</table>';
	$html .= '</div>';
	return $html;

}