<?php
/**
 * Provides functions to handle the loading operations of the plugin.
 *
 * The functions are defined in the global namespace to allow easier loading in the main plugin file.
 *
 * @since TBD
 */

use TEC\Conference\Plugin;
use Tribe__Main as Common;

/**
 * Shows a message to indicate the plugin cannot be loaded due to missing requirements.
 *
 * @since TBD
 *
 * @param string $message The message to show.
 */
function conference_schedule_show_fail_message( $message ) {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	conference_schedule_load_text_domain();

	echo wp_kses_post( '<div class="error"><p>' . $message . '</p></div>' );
}

/**
 * Loads the plugin localization files.
 *
 * If the text domain loading functions provided by `common` (from The Events Calendar or Event Tickets) are not
 * available, then the function will use the `load_plugin_textdomain` function.
 *
 * @since TBD
 */
function conference_schedule_load_text_domain() {
	$domain          = 'conference-schedule';
	$plugin_base_dir = dirname( plugin_basename( CONFERENCE_SCHEDULE_FILE ) );
	$plugin_rel_path = $plugin_base_dir . DIRECTORY_SEPARATOR . 'lang';

	if ( ! class_exists( 'Common' ) ) {
		// If we don't have Common classes load the old fashioned way.
		load_plugin_textdomain( $domain, false, $plugin_rel_path );
	} else {
		// This will load `wp-content/languages/plugins` files first.
		Common::instance()->load_text_domain( $domain, $plugin_rel_path );
	}
}


/**
 * Register and load the service provider for loading the plugin.
 *
 * @since TBD
 */
function conference_schedule_load() {
	// Last file that needs to be loaded manually.
	require_once dirname( CONFERENCE_SCHEDULE_FILE ) . '/src/Conference/Plugin.php';

	// Load the plugin, autoloading happens here.
	$plugin = new Plugin();
	$plugin->boot();
}



/**
 * Handles the removal of PUE-related options when the plugin is uninstalled.
 *
 * @since TBD
 */
function conference_schedule_uninstall() {
	$slug = Plugin::SLUG;

	delete_option( 'pue_install_key_' . $slug );
	delete_option( 'pu_dismissed_upgrade_' . $slug );
}
