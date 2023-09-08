<?php
/**
 * Handles Conference Schedule Front End Assets.
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference;

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
			'conference-schedule-pro-js',
			'conference-schedule-pro.js'
		)
		->add_to_group( 'conference-schedule-pro' )
		->register();
	}
}
