<?php
/**
 * Handles Conference Schedule View Assets.
 *
 * @since TBD
 *
 * @package TEC\Conference\Views
 */

namespace TEC\Conference\Views;

use TEC\Conference\Vendor\StellarWP\Assets\Asset;

/**
 * Class Assets
 *
 * @since TBD
 *
 * @package TEC\Conference\Views
 */
class Assets {

	/**
	 * Registers the view assets.
	 *
	 * @since TBD
	 */
	public function register_views_assets() {
		Asset::add(
			'conference-schedule-pro-views-css',
			'conference-schedule-views.css'
		)
		->set_dependencies( 'conference-schedule-pro-font-awesome', 'dashicons' )
		->add_to_group( 'conference-schedule-pro-views' )
		->register();

		Asset::add(
			'conference-schedule-pro-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css'
		)
			->add_to_group( 'conference-schedule-pro-views' )
		->register();

		Asset::add(
			'conference-schedule-pro-js',
			'conference-schedule-pro.js'
		)
		->set_dependencies( 'jquery' )
		->add_to_group( 'conference-schedule-pro-views' )
		->register();
	}
}
