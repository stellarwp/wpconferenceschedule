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

define( 'CONFERENCE_SCHEDULE_FILE', __FILE__ );

// Load the required php min version functions.
require_once dirname( CONFERENCE_SCHEDULE_FILE ) . '/src/functions/php-min-version.php';

/**
 * Verifies if we need to warn the user about min PHP version and bail to avoid fatal errors.
 */
if ( tribe_is_not_min_php_version() ) {
	tribe_not_php_version_textdomain( 'conference-schedule', CONFERENCE_SCHEDULE_FILE );

	/**
	 * Include the plugin name into the correct place.
	 *
	 * @since  1.0.1
	 *
	 * @param  array $names current list of names.
	 *
	 * @return array List of names after adding Event Automator.
	 */
	function tec_conference_schedule_not_php_version_plugin_name( $names ) {
		$names['conference-schedule'] = esc_html__( 'Conference Schedule', 'conference-schedule' );
		return $names;
	}

	add_filter( 'tribe_not_php_version_names', 'tec_conference_schedule_not_php_version_plugin_name' );

	if ( ! has_filter( 'admin_notices', 'tribe_not_php_version_notice' ) ) {
		add_action( 'admin_notices', 'tribe_not_php_version_notice' );
	}

	return false;
}

// Include the file that defines the functions handling the plugin load operations.
require_once __DIR__ . '/src/functions/load.php';

add_action( 'plugins_loaded', 'conference_schedule_load', 0 );


//@TODO START OF ORIGINAL PLUGIN CODE - WILL MOVE AND REPLACE IN FUTURE PRS
// Plugin directory
define( 'WPCS_DIR' , plugin_dir_path( __FILE__ ) );

// Plugin File URL
define( 'PLUGIN_FILE_URL' , __FILE__);

// Pro Plugin Active
/*if ( ! defined( 'WPCSP_ACTIVE' ) ) {
	if(is_plugin_active('wp-conference-schedule-pro/wp-conference-schedule-pro.php')){
		define( 'WPCSP_ACTIVE', true );
	}else{
		define( 'WPCSP_ACTIVE', false );
	}
}*/

// Includes
require_once( WPCS_DIR . 'inc/post-types.php' );
require_once( WPCS_DIR . 'inc/schedule-output-functions.php' );

class WP_Conference_Schedule_Plugin {

	/**
	 * Fired when plugin file is loaded.
	 */
	function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'wpcs_enqueue_scripts' ) );
		add_action('enqueue_block_editor_assets', array( $this, 'wpcs_loadBlockFiles' ) );

		register_block_type('wpcs/schedule-block', [
			'editor_script' => 'schedule-block',
			'attributes' => [
				'date' => ['type' => 'string'],
				'color_scheme' => ['type' => 'string'],
				'layout' => ['type' => 'string'],
				'row_height' => ['type' => 'string'],
				'session_link' => ['type' => 'string'],
				'tracks' => ['type' => 'string'],
				'align' => ['type' => 'string'],
			],
			'render_callback' => [$this, 'wpcs_scheduleBlockOutput'],
		]);

		add_shortcode( 'wpcs_schedule', array( $this, 'wpcs_shortcode_schedule' ) );
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
	 * The [schedule] shortcode callback
	 */
	function wpcs_shortcode_schedule( $attr, $content ) {
		return wpcs_scheduleOutput( $attr );
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