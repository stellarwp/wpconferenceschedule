<?php

/**
 * @link              https://wpconferenceschedule.com
 * @since             1.0.0
 * @package           wp_conference_schedule
 *
 * @wordpress-plugin
 * Plugin Name:       WP Conference Schedule
 * Plugin URI:        https://wpconferenceschedule.com
 * Description:       Creates sessions post types for conference websites. Includes shortcode and custom block for fully mobile-responsive conference schedule in table format.
 * Version:           1.1.1
 * Author:            Road Warrior Creative
 * Author URI:        https://roadwarriorcreative.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       wp-conference-schedule
 * Domain Path:       /languages
 */

// Freemius
if ( ! function_exists( 'wpcs_freemius' ) ) {
	// Create a helper function for easy SDK access.
	function wpcs_freemius() {
		global $wpcs_freemius;

		if ( ! isset( $wpcs_freemius ) ) {
			// Include Freemius SDK.
			require_once dirname(__FILE__) . '/freemius/start.php';

			$wpcs_freemius = fs_dynamic_init( array(
					'id'                  => '5738',
					'slug'                => 'wp-conference-schedule',
					'type'                => 'plugin',
					'public_key'          => 'pk_8f9bb5d32e35584c2425014d6d6bd',
					'is_premium'          => false,
					'has_addons'          => false,
					'has_paid_plans'      => false,
					'menu'                => array(
							'slug'           => 'wp-conference-schedule',
							'account'        => false,
							'contact'        => false,
							'support'        => false,
							'parent'         => array(
									'slug' => 'options-general.php',
					),
				),
			));
		}

		return $wpcs_freemius;
	}

	// Init Freemius.
	wpcs_freemius();
	// Signal that SDK was initiated.
	do_action( 'wpcs_freemius_loaded' );
}

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Plugin directory
define( 'WPCS_DIR' , plugin_dir_path( __FILE__ ) );

// Plugin File URL
define( 'PLUGIN_FILE_URL' , __FILE__);

// Pro Plugin Active
if ( ! defined( 'WPCSP_ACTIVE' ) ) {
	if(is_plugin_active('wp-conference-schedule-pro/wp-conference-schedule-pro.php')){
		define( 'WPCSP_ACTIVE', true );
	}else{
		define( 'WPCSP_ACTIVE', false );
	}
}

// Includes
require_once( WPCS_DIR . 'inc/post-types.php' );
require_once( WPCS_DIR . 'inc/taxonomies.php' );
require_once( WPCS_DIR . 'inc/schedule-output-functions.php' );
require_once( WPCS_DIR . 'inc/settings.php' );
require_once( WPCS_DIR . '/cmb2/init.php');

class WP_Conference_Schedule_Plugin {

	/**
	 * Fired when plugin file is loaded.
	 */
	function __construct() {

		add_action( 'admin_init', array( $this, 'wpcs_admin_init' ) );
		add_action( 'admin_print_styles', array( $this, 'wpcs_admin_css' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'wpcs_admin_enqueue_scripts' ) );
		add_action( 'admin_print_footer_scripts', array( $this, 'wpcs_admin_print_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'wpcs_enqueue_scripts' ) );
		add_action( 'save_post', array( $this, 'wpcs_save_post_session' ), 10, 2 );
		add_action( 'manage_posts_custom_column', array( $this, 'wpcs_manage_post_types_columns_output' ), 10, 2 );
		add_action( 'cmb2_admin_init', array($this, 'wpcs_session_metabox' ) );
		add_action( 'add_meta_boxes', array( $this, 'wpcs_add_meta_boxes' ) );
		add_action('enqueue_block_editor_assets', array( $this, 'wpcs_loadBlockFiles' ) );
		
		register_block_type('wpcs/schedule-block', array(
			'editor_script' => 'schedule-block',
			'attributes' => array(
				'date' => array('type' => 'string'),
				'color_scheme' => array('type' => 'string'),
				'layout' => array('type' => 'string'),
				'row_height' => array('type' => 'string'),
				'session_link' => array('type' => 'string'),
				'tracks' => array('type' => 'string'),
				'align' => array('type' => 'string'),
			),
			'render_callback' => array( $this, 'wpcs_scheduleBlockOutput')
		));

		add_filter( 'manage_wpcs_session_posts_columns', array( $this, 'wpcs_manage_post_types_columns' ) );
		add_filter( 'manage_edit-wpcs_session_sortable_columns', array( $this, 'wpcs_manage_sortable_columns' ) );
		add_filter( 'display_post_states', array( $this, 'wpcs_display_post_states' ) );

		add_shortcode( 'wpcs_schedule', array( $this, 'wpcs_shortcode_schedule' ) );
	}

	/**
	 * Runs during admin_init.
	 */
	function wpcs_admin_init() {
		add_action( 'pre_get_posts', array( $this, 'wpcs_admin_pre_get_posts' ) );
	}

	/**
	 * Runs during pre_get_posts in admin.
	 *
	 * @param WP_Query $query
	 */
	function wpcs_admin_pre_get_posts( $query ) {
		if ( ! is_admin() || ! $query->is_main_query() ) {
			return;
		}

		$current_screen = get_current_screen();

		// Order by session time
		if ( 'edit-wpcs_session' == $current_screen->id && $query->get( 'orderby' ) == '_wpcs_session_time' ) {
			$query->set( 'meta_key', '_wpcs_session_time' );
			$query->set( 'orderby', 'meta_value_num' );
		}
	}

	function wpcs_admin_enqueue_scripts() {
		global $post_type;

		// Enqueues scripts and styles for session admin page
		if ( 'wpcs_session' == $post_type ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
			wp_register_style( 'jquery-ui', plugins_url( '/assets/css/jquery-ui.css', __FILE__ ) );

			wp_enqueue_style( 'jquery-ui' ); 
		}

	}

	/*
	 * Print JavaScript
	 */
	function wpcs_admin_print_scripts() {
		global $post_type;

		// DatePicker for Session posts
		if ( 'wpcs_session' == $post_type ) :
			?>

			<script type="text/javascript">
				jQuery( document ).ready( function( $ ) {
					$( '#wpcs-session-date' ).datepicker( {
						dateFormat:  'yy-mm-dd',
						changeMonth: true,
						changeYear:  true
					} );
				} );
			</script>

			<?php
		endif;
	}

	function wpcs_enqueue_scripts() {
		wp_enqueue_style( 'wpcs_styles', plugins_url( '/assets/css/style.css', __FILE__ ), array(), 2 );

		wp_enqueue_style(
			'font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css',
			array(),
			'1.0.0'
		);
	}

	/**
	 * Runs during admin_print_styles, adds CSS for custom admin columns and block editor
	 *
	 * @uses wp_enqueue_style()
	 */
	function wpcs_admin_css() {
		wp_enqueue_style( 'wpcs-admin', plugins_url( '/assets/css/admin.css', __FILE__ ), array(), 1 );
	}

	/**
	 * The [schedule] shortcode callback
	 */
	function wpcs_shortcode_schedule( $attr, $content ) {
		return wpcs_scheduleOutput( $attr );
	}

	public function wpcs_update_session_date_meta(){
		$post_id = null;
		if(isset($_REQUEST['post']) || isset($_REQUEST['post_ID'])){
			$post_id = empty($_REQUEST['post_ID']) ? $_REQUEST['post'] : $_REQUEST['post_ID'];  
		}
		$session_date = get_post_meta($post_id, '_wpcs_session_date',true);
		$session_time = get_post_meta($post_id, '_wpcs_session_time',true);

		/* var_dump($session_date);
		var_dump($session_time);
		var_dump($post_id); */

		if($post_id && !$session_date && $session_time){
			update_post_meta($post_id, '_wpcs_session_date', $session_time);
		}
	}

	public function wpcs_session_metabox() {

		$cmb = new_cmb2_box( array(
			'id'            => 'wpcs_session_metabox',
			'title'         => __( 'Session Information', 'wpcsp' ),
			'object_types'  => array( 'wpcs_session', ), // Post type
			'context'       => 'normal',
			'priority'      => 'high',
			'show_names'    => true, // Show field names on the left
			// 'cmb_styles' => false, // false to disable the CMB stylesheet
			// 'closed'     => true, // Keep the metabox closed by default
		) );
		
		/* $this->wpcs_update_session_date_meta();

		$cmb->add_field( array(
			'name' => 'Date',
			'id'   => '_wpcs_session_date',
			'type' => 'text_date',
			// 'timezone_meta_key' => 'wiki_test_timezone',
			// 'date_format' => 'l jS \of F Y',
		) );

		$cmb->add_field( array(
			'name' => 'Start Time',
			'id' => '_wpcs_session_time',
			'type' => 'text_time'
			// Override default time-picker attributes:
			// 'attributes' => array(
			//     'data-timepicker' => json_encode( array(
			//         'timeOnlyTitle' => __( 'Choose your Time', 'cmb2' ),
			//         'timeFormat' => 'HH:mm',
			//         'stepMinute' => 1, // 1 minute increments instead of the default 5
			//     ) ),
			// ),
			// 'time_format' => 'h:i:s A',
		) );

		$cmb->add_field( array(
			'name' => 'End Time',
			'id' => '_wpcs_session_end_time',
			'type' => 'text_time'
			// Override default time-picker attributes:
			// 'attributes' => array(
			//     'data-timepicker' => json_encode( array(
			//         'timeOnlyTitle' => __( 'Choose your Time', 'cmb2' ),
			//         'timeFormat' => 'HH:mm',
			//         'stepMinute' => 1, // 1 minute increments instead of the default 5
			//     ) ),
			// ),
			// 'time_format' => 'h:i:s A',
		) );

		$cmb->add_field( array(
			'name'             => 'Type',
			//'desc'             => 'Select an option',
			'id'               => '_wpcs_session_type',
			'type'             => 'select',
			'show_option_none' => false,
			'default'          => 'session',
			'options'          => array(
				'session' => __( 'Regular Session', 'wpcs' ),
				'mainstage'   => __( 'Mainstage', 'wpcs' ),
				'custom'     => __( 'Break, Lunch, etc.', 'wpcs' ),
			),
		) ); */
	
		// filter speaker meta field
		if(has_filter('wpcs_filter_session_speaker_meta_field')) {
			$cmb = apply_filters('wpcs_filter_session_speaker_meta_field', $cmb);
		}else{
			// Speaker Name(s)
			$cmb->add_field( array(
				'name'       => __( 'Speaker Name(s)', 'wpcsp' ),
				//'desc'       => __( 'field description (optional)', 'wpcsp' ),
				'id'         => '_wpcs_session_speakers',
				'type'       => 'text',
			) );
		}
		
	}

	/**
	 * Fired during add_meta_boxes, adds extra meta boxes to our custom post types.
	 */
	function wpcs_add_meta_boxes() {
		add_meta_box( 'session-info',      __( 'Session Info',      'wp-conference-schedule'  ), array( $this, 'wpcs_metabox_session_info'      ), 'wpcs_session',   'normal' );
	}

	function wpcs_metabox_session_info() {
		$post             = get_post();
		$session_time     = absint( get_post_meta( $post->ID, '_wpcs_session_time', true ) );
		$session_date     = ( $session_time ) ? date( 'Y-m-d', $session_time ) : date( 'Y-m-d' );
		$session_hours    = ( $session_time ) ? date( 'g', $session_time )     : date( 'g' );
		$session_minutes  = ( $session_time ) ? date( 'i', $session_time )     : '00';
		$session_meridiem = ( $session_time ) ? date( 'a', $session_time )     : 'am';
		$session_type     = get_post_meta( $post->ID, '_wpcs_session_type', true );
		$session_speakers = get_post_meta( $post->ID, '_wpcs_session_speakers',  true );

		$session_end_time     = absint( get_post_meta( $post->ID, '_wpcs_session_end_time', true ) );
		$session_end_hours    = ( $session_end_time ) ? date( 'g', $session_end_time )     : date( 'g' );
		$session_end_minutes  = ( $session_end_time ) ? date( 'i', $session_end_time )     : '00';
		$session_end_meridiem = ( $session_end_time ) ? date( 'a', $session_end_time )     : 'am';
		?>

		<?php wp_nonce_field( 'edit-session-info', 'wpcs-meta-session-info' ); ?>

		<p>
			<label for="wpcs-session-date"><?php _e( 'Date:', 'wp-conference-schedule' ); ?></label>
			<input type="text" id="wpcs-session-date" data-date="<?php echo esc_attr( $session_date ); ?>" name="wpcs-session-date" value="<?php echo esc_attr( $session_date ); ?>" /><br />
			<label><?php _e( 'Time:', 'wp-conference-schedule' ); ?></label>

			<select name="wpcs-session-hour" aria-label="<?php _e( 'Session Start Hour', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_hours ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select> :

			<select name="wpcs-session-minutes" aria-label="<?php _e( 'Session Start Minutes', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = '00'; (int) $i <= 55; $i = sprintf( '%02d', (int) $i + 5 ) ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_minutes ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>

			<select name="wpcs-session-meridiem" aria-label="<?php _e( 'Session Meridiem', 'wp-conference-schedule' ); ?>">
				<option value="am" <?php selected( 'am', $session_meridiem ); ?>>am</option>
				<option value="pm" <?php selected( 'pm', $session_meridiem ); ?>>pm</option>
			</select>
		</p>

		<p>
			<label><?php _e( 'End Time:', 'wp-conference-schedule' ); ?></label>

			<select name="wpcs-session-end-hour" aria-label="<?php _e( 'Session Start Hour', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = 1; $i <= 12; $i++ ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_end_hours ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select> :

			<select name="wpcs-session-end-minutes" aria-label="<?php _e( 'Session Start Minutes', 'wp-conference-schedule' ); ?>">
				<?php for ( $i = '00'; (int) $i <= 55; $i = sprintf( '%02d', (int) $i + 5 ) ) : ?>
					<option value="<?php echo esc_attr( $i ); ?>" <?php selected( $i, $session_end_minutes ); ?>>
						<?php echo esc_html( $i ); ?>
					</option>
				<?php endfor; ?>
			</select>

			<select name="wpcs-session-end-meridiem" aria-label="<?php _e( 'Session Meridiem', 'wp-conference-schedule' ); ?>">
				<option value="am" <?php selected( 'am', $session_end_meridiem ); ?>>am</option>
				<option value="pm" <?php selected( 'pm', $session_end_meridiem ); ?>>pm</option>
			</select>
		</p>

		<p>
			<label for="wpcs-session-type"><?php _e( 'Type:', 'wp-conference-schedule' ); ?></label>
			<select id="wpcs-session-type" name="wpcs-session-type">
				<option value="session" <?php selected( $session_type, 'session' ); ?>><?php _e( 'Regular Session', 'wp-conference-schedule' ); ?></option>
				<option value="mainstage" <?php selected( $session_type, 'mainstage' ); ?>><?php _e( 'Mainstage', 'wp-conference-schedule' ); ?></option>
				<option value="custom" <?php selected( $session_type, 'custom' ); ?>><?php _e( 'Break, Lunch, etc.', 'wp-conference-schedule' ); ?></option>
			</select>
		</p>

		<!-- <p>
			<label for="wpcs-session-speakers"><?php _e( 'Speaker Name(s):', 'wp-conference-schedule' ); ?></label>
			<input type="text" class="widefat" id="wpcs-session-speakers" name="wpcs-session-speakers" value="<?php echo $session_speakers; ?>" />
		</p> -->

		<?php
	}
	
	/**
	 * Fired when a post is saved, updates additional sessions metadada.
	 */
	function wpcs_save_post_session( $post_id, $post ) {
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
			$session_time = strtotime( sprintf(
				'%s %d:%02d %s',
				sanitize_text_field( $_POST['wpcs-session-date'] ),
				absint( $_POST['wpcs-session-hour'] ),
				absint( $_POST['wpcs-session-minutes'] ),
				'am' == $_POST['wpcs-session-meridiem'] ? 'am' : 'pm'
			) );
			update_post_meta( $post_id, '_wpcs_session_time', $session_time );

			// Update session end time
			$session_end_time = strtotime( sprintf(
				'%s %d:%02d %s',
				sanitize_text_field( $_POST['wpcs-session-date'] ),
				absint( $_POST['wpcs-session-end-hour'] ),
				absint( $_POST['wpcs-session-end-minutes'] ),
				'am' == $_POST['wpcs-session-end-meridiem'] ? 'am' : 'pm'
			) );
			update_post_meta( $post_id, '_wpcs_session_end_time', $session_end_time );

			// Update session type
			$session_type = sanitize_text_field( $_POST['wpcs-session-type'] );
			if ( ! in_array( $session_type, array( 'session', 'custom', 'mainstage' ) ) ) {
				$session_type = 'session';
			}
			update_post_meta( $post_id, '_wpcs_session_type', $session_type );

			// Update session speakers
			$session_speakers = sanitize_text_field($_POST['wpcs-session-speakers']);
			update_post_meta( $post_id, '_wpcs_session_speakers', $session_speakers);

		}

	}
	
	/**
	 * Filters our custom post types columns.
	 *
	 * @uses current_filter()
	 * @see __construct()
	 */
	function wpcs_manage_post_types_columns( $columns ) {
		$current_filter = current_filter();

		switch ( $current_filter ) {
			case 'manage_wpcs_session_posts_columns':
				$columns = array_slice( $columns, 0, 1, true ) + array( 'conference_session_time'     => __( 'Time',     'wp-conference-schedule' ) ) + array_slice( $columns, 1, null, true );
				break;
			default:
		}

		return $columns;
	}

	/**
	 * Custom columns output
	 *
	 * This generates the output to the extra columns added to the posts lists in the admin.
	 *
	 * @see wpcs_manage_post_types_columns()
	 */
	function wpcs_manage_post_types_columns_output( $column, $post_id ) {
		switch ( $column ) {

			case 'conference_session_time':
				$session_time = absint( get_post_meta( get_the_ID(), '_wpcs_session_time', true ) );
				$session_time = ( $session_time ) ? date( get_option( 'time_format' ), $session_time ) : '&mdash;';
				echo esc_html( $session_time );
				break;

			default:
		}
	}

	/**
	 * Additional sortable columns for WP_Posts_List_Table
	 */
	function wpcs_manage_sortable_columns( $sortable ) {
		$current_filter = current_filter();

		if ( 'manage_edit-wpcs_session_sortable_columns' == $current_filter ) {
			$sortable['conference_session_time'] = '_wpcs_session_time';
		}

		return $sortable;
	}

	/**
	 * Display an additional post label if needed.
	 */
	function wpcs_display_post_states( $states ) {
		$post = get_post();

		if ( 'wpcs_session' != $post->post_type ) {
			return $states;
		}

		$session_type = get_post_meta( $post->ID, '_wpcs_session_type', true );
		if ( ! in_array( $session_type, array( 'session', 'custom', 'mainstage' ) ) ) {
			$session_type = 'session';
		}

		if ( 'session' == $session_type ) {
			$states['wpcs-session-type'] = __( 'Session', 'wp-conference-schedule' );
		} elseif ( 'custom' == $session_type ) {
			$states['wpcs-session-type'] = __( 'Custom', 'wp-conference-schedule' );
		} elseif ( 'mainstage' == $session_type ) {
			$states['wpcs-session-type'] = __( 'Mainstage', 'wp-conference-schedule' );
		}

		return $states;
	}

	/**
	 * Enqueue blocks
	 */
	function wpcs_loadBlockFiles() {
		wp_enqueue_script(
			'schedule-block',
			plugin_dir_url(__FILE__) . 'assets/js/schedule-block.js',
			array('wp-blocks', 'wp-i18n', 'wp-editor'),
			true
		);
	}

	/**
	 * Schedule Block Dynamic content Output.
	 */
	function wpcs_scheduleBlockOutput($props) {
		return wpcs_scheduleOutput( $props );
	}

}

// Load the plugin class.
$GLOBALS['wpcs_plugin'] = new WP_Conference_Schedule_Plugin();