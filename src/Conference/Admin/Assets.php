<?php
/**
 * Handles Conference Schedule Admin Assets.
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Vendor\StellarWP\Assets\Asset;

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
	}
}
