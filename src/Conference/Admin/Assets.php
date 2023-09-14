<?php
/**
 * Handles Conference Schedule Admin Assets.
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Assets\Asset;
use TEC\Conference\Vendor\StellarWP\Assets\Assets as Stellar_Assets;

/**
 * Class Assets
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */
class Assets {

	/**
	 * Registers the admin assets.
	 *
	 * @since TBD
	 */
	public function register_admin_assets() {
		Asset::add(
			'conference-schedule-pro-admin-css',
			'conference-schedule-admin.css'
		)
		->add_to_group( 'conference-schedule-pro-admin' )
		->register();

		Asset::add(
			'conference-schedule-pro-jquery-ui-css',
			'jquery-ui.css'
		)
		->add_to_group( 'conference-schedule-pro-admin' )
		->register();

		Asset::add(
			'conference-schedule-pro-admin-js',
			'conference-schedule-admin.js'
		)
		->set_dependencies( 'jquery', 'jquery-ui-datepicker', 'jquery-ui-sortable' )
		->add_to_group( 'conference-schedule-pro-admin' )
		->register();

		Asset::add(
			'conference-schedule-pro-js',
			'conference-schedule-pro.js'
		)
		->add_to_group( 'conference-schedule-pro-admin' )
		->register();
	}

	/**
	 * Enqueues the admin assets for custom post types.
	 *
	 * @since TBD
	 */
	public function enqueue_admin_posttype_assets() {
		$screen = get_current_screen();

		if ( empty( $screen->post_type ) ) {
			return;
		}

		$post_types = array(
			Plugin::SESSION_POSTTYPE,
			Plugin::SPEAKER_POSTTYPE,
			Plugin::SPONSOR_POSTTYPE,
		);

		if (
			'post' !== $screen->base
			|| ! in_array( $screen->post_type, $post_types )
		) {
			return;
		}

		Stellar_Assets::instance()->enqueue_group( 'conference-schedule-pro-admin' );
	}
}
