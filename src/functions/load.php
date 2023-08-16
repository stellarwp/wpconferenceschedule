<?php
/**
 * Provides functions to handle the loading operations of the plugin.
 *
 * The functions are defined in the global namespace to allow easier loading in the main plugin file.
 *
 * @since TBD
 */

use Conference\Schedule\Plugin;

/**
 * Shows a message to indicate the plugin cannot be loaded due to missing requirements.
 *
 * @since TBD
 * @since TBD Include message as a param.
 */
function conference_schedule_show_fail_message( $message = null ) {
	if ( ! current_user_can( 'activate_plugins' ) ) {
		return;
	}

	conference_schedule_load_text_domain();

	$url_tec = 'plugin-install.php?tab=plugin-information&plugin=the-events-calendar&TB_iframe=true';
	$url_et = 'plugin-install.php?tab=plugin-information&plugin=event-tickets&TB_iframe=true';

	if ( null === $message ) {
		$message = sprintf(
			'%1s <a href="%2s" class="thickbox" title="%3s">%3$s</a> or <a href="%4s" class="thickbox" title="%5s">%5$s</a>.',
			esc_html_x(
				'To begin using Event Automator, please install the latest version of',
				'Instructions displayed when missing a required plugin for Event Automator to work.',
				'conference-schedule'
			),
			esc_url( $url_tec ),
			esc_html_x( 'The Events Calendar', 'Name of one of the required plugins for Event Automator to work.','conference-schedule' ),
			esc_url( $url_et ),
			esc_html_x( 'Event Tickets', 'Name of one of the required plugins for Event Automator to work.','conference-schedule' )
		);
	}

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

	if ( ! class_exists( 'Tribe__Main' ) ) {
		// If we don't have Common classes load the old fashioned way.
		load_plugin_textdomain( $domain, false, $plugin_rel_path );
	} else {
		// This will load `wp-content/languages/plugins` files first.
		Tribe__Main::instance()->load_text_domain( $domain, $plugin_rel_path );
	}
}

/**
 * Register and load the service provider for loading the plugin.
 *
 * @since TBD
 */
function conference_schedule_load() {
	$plugin_register = tribe( \Conference\Schedule\Plugin_Register::class );

	// Determine if the main class exists, it really shouldn't, but we double-check.
	if ( class_exists( $plugin_register->get_plugin_class(), false ) ) {
		$notice_about_plugin_already_exists = static function() {
			$message = esc_html__(
				'Conference Schedule plugin is already loaded. Please check your site for conflicting plugins.',
				'events-automator'
			);

			conference_schedule_show_fail_message( $message );
		};

		// Loaded in single site or not network-activated in a multisite installation.
		add_action( 'admin_notices', $notice_about_plugin_already_exists );

		// Network-activated in a multisite installation.
		add_action( 'network_admin_notices', $notice_about_plugin_already_exists );
	}

	// Last file that needs to be loaded manually.
	require_once dirname( $plugin_register->get_base_dir() ) . '/src/Conference/Plugin.php';

	// Load the plugin, autoloading happens here.
	\Conference\Schedule\Plugin::boot();
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
