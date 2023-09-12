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
