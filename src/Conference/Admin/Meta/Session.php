<?php
/**
 * Conference Schedule Session Meta.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin\Meta
 */

namespace TEC\Conference\Admin\Meta;

use TEC\Conference\Admin\WP_Post;

/**
 * Class Session
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin\Meta
 */
class Session {

	/**
	 * Saves post session details.
	 *
	 * @since TBD
	 *
	 * @param int     $post_id Post ID.
	 * @param WP_Post $post    Post object.
	 */
	public function save_post_session( $post_id, $post ) {
		if ( wp_is_post_revision( $post_id ) || $post->post_type != 'wpcs_session' ) {
			return;
		}

		if ( isset( $_POST['wpcs-meta-speakers-list-nonce'] ) && wp_verify_nonce( $_POST['wpcs-meta-speakers-list-nonce'], 'edit-speakers-list' ) && current_user_can( 'edit_post', $post_id ) ) {

			// Update the text box as is for backwards compatibility.
			$speakers = sanitize_text_field( $_POST['wpcs-speakers-list'] );
			update_post_meta( $post_id, '_conference_session_speakers', $speakers );
		}

		if ( isset( $_POST['wpcs-meta-session-info'] ) && wp_verify_nonce( $_POST['wpcs-meta-session-info'], 'edit-session-info' ) ) {

			// Update session time
			$session_time = strtotime( sprintf( '%s %d:%02d %s', sanitize_text_field( $_POST['wpcs-session-date'] ), absint( $_POST['wpcs-session-hour'] ), absint( $_POST['wpcs-session-minutes'] ), 'am' == $_POST['wpcs-session-meridiem'] ? 'am' : 'pm' ) );
			update_post_meta( $post_id, '_wpcs_session_time', $session_time );

			// Update session end time
			$session_end_time = strtotime( sprintf( '%s %d:%02d %s', sanitize_text_field( $_POST['wpcs-session-date'] ), absint( $_POST['wpcs-session-end-hour'] ), absint( $_POST['wpcs-session-end-minutes'] ), 'am' == $_POST['wpcs-session-end-meridiem'] ? 'am' : 'pm' ) );
			update_post_meta( $post_id, '_wpcs_session_end_time', $session_end_time );

			// Update session type
			$session_type = sanitize_text_field( $_POST['wpcs-session-type'] );
			if ( ! in_array( $session_type, array( 'session', 'custom', 'mainstage' ) ) ) {
				$session_type = 'session';
			}
			update_post_meta( $post_id, '_wpcs_session_type', $session_type );

			// Update session speakers
			$session_speakers = sanitize_text_field( $_POST['wpcs-session-speakers'] );
			update_post_meta( $post_id, '_wpcs_session_speakers', $session_speakers );
		}
	}

	/**
	 * Adds the session information meta box.
	 *
	 * @since TBD
	 */
	public function session_metabox() {
		$cmb = new_cmb2_box( [
			'id'           => 'wpcs_session_metabox',
			'title'        => _x( 'Session Information', 'Metabox title', 'wp-conference-schedule' ),
			'object_types' => [ 'wpcs_session' ], // Post type
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true, // Show field names on the left
		] );

		/**
		 * Filters the speaker meta field in the session information meta box.
		 *
		 * @since TBD
		 *
		 * @param object $cmb CMB2 box object.
		 */
		if ( has_filter( 'wpcs_filter_session_speaker_meta_field' ) ) {
			$cmb = apply_filters( 'wpcs_filter_session_speaker_meta_field', $cmb );
		} else {
			// Speaker Name(s)
			$cmb->add_field( [
				'name' => _x( 'Speaker Name(s)', 'Metabox field', 'wp-conference-schedule' ),
				'id'   => '_wpcs_session_speakers',
				'type' => 'text'
			] );
		}
	}

	/**
	 * Adds meta boxes for the session post type.
	 *
	 * @since TBD
	 */
	public function add_meta_boxes() {
		add_meta_box(
			'session-info',
			_x( 'Session Info', 'Session Metabox Name.','wp-conference-schedule' ),
			[ $this, 'metabox_session_info' ],
			'wpcs_session',
			'normal'
		);
	}

	/**
	 * Displays the session information meta box.
	 *
	 * @since TBD
	 */
	public function metabox_session_info() {
		$post             = get_post();
		$session_time     = absint( get_post_meta( $post->ID, '_wpcs_session_time', true ) );
		$session_date     = ( $session_time ) ? date( 'Y-m-d', $session_time ) : date( 'Y-m-d' );
		$session_hours    = ( $session_time ) ? date( 'g', $session_time ) : date( 'g' );
		$session_minutes  = ( $session_time ) ? date( 'i', $session_time ) : '00';
		$session_meridiem = ( $session_time ) ? date( 'a', $session_time ) : 'am';
		$session_type     = get_post_meta( $post->ID, '_wpcs_session_type', true );

		$session_end_time     = absint( get_post_meta( $post->ID, '_wpcs_session_end_time', true ) );
		$session_end_hours    = ( $session_end_time ) ? date( 'g', $session_end_time ) : date( 'g' );
		$session_end_minutes  = ( $session_end_time ) ? date( 'i', $session_end_time ) : '00';
		$session_end_meridiem = ( $session_end_time ) ? date( 'a', $session_end_time ) : 'am';
		?>

		<?php wp_nonce_field( 'edit-session-info', 'wpcs-meta-session-info' ); ?>

		<p>
			<label for="wpcs-session-date"><?php _ex( 'Date:', 'Session date label', 'wp-conference-schedule' ); ?></label>
			<input type="text" id="wpcs-session-date" data-date="<?php echo esc_attr( $session_date ); ?>" name="wpcs-session-date" value="<?php echo esc_attr( $session_date ); ?>"/><br/>
			<label><?php _ex( 'Time:', 'Session time label', 'wp-conference-schedule' ); ?></label>

			<select name="wpcs-session-hour" aria-label="<?php _ex( 'Session Start Hour', 'Aria label for session start hour', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = 1; $i <= 12; $i ++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_hours ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select> :

			<select name="wpcs-session-minutes" aria-label="<?php _ex( 'Session Start Minutes', 'Aria label for session start minutes', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = '00'; (int) $i <= 55; $i = sprintf( '%02d', (int) $i + 5 ) ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_minutes ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>

			<select name="wpcs-session-meridiem" aria-label="<?php _ex( 'Session Meridiem', 'Aria label for session meridiem', 'wp-conference-schedule' ); ?>">
				<option value="am" <?php selected( 'am', $session_meridiem ); ?>>am</option>
				<option value="pm" <?php selected( 'pm', $session_meridiem ); ?>>pm</option>
			</select>
		</p>

		<p>
			<label><?php _ex( 'End Time:', 'Session end time label', 'wp-conference-schedule' ); ?></label>

			<select name="wpcs-session-end-hour" aria-label="<?php _ex( 'Session End Hour', 'Aria label for session end hour', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = 1; $i <= 12; $i ++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_end_hours ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select> :

			<select name="wpcs-session-end-minutes" aria-label="<?php _ex( 'Session End Minutes', 'Aria label for session end minutes', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = '00'; (int) $i <= 55; $i = sprintf( '%02d', (int) $i + 5 ) ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_end_minutes ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>

			<select name="wpcs-session-end-meridiem" aria-label="<?php _ex( 'Session End Meridiem', 'Aria label for session end meridiem', 'wp-conference-schedule' ); ?>">
				<option value="am" <?php selected( 'am', $session_end_meridiem ); ?>>am</option>
				<option value="pm" <?php selected( 'pm', $session_end_meridiem ); ?>>pm</option>
			</select>
		</p>

		<p>
			<label for="wpcs-session-type"><?php _ex( 'Type:', 'Session type label', 'wp-conference-schedule' ); ?></label>
			<select id="wpcs-session-type" name="wpcs-session-type">
				<option value="session" <?php selected( $session_type, 'session' ); ?>><?php _ex( 'Regular Session', 'Session type', 'wp-conference-schedule' ); ?></option>
				<option value="mainstage" <?php selected( $session_type, 'mainstage' ); ?>><?php _ex( 'Mainstage', 'Session type', 'wp-conference-schedule' ); ?></option>
				<option value="custom" <?php selected( $session_type, 'custom' ); ?>><?php _ex( 'Break, Lunch, etc.', 'Session type', 'wp-conference-schedule' ); ?></option>
			</select>
		</p>
		<?php
	}
}
