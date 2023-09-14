<?php
/**
 * Handles Conference Schedule View Assets.
 *
 * @since TBD
 *
 * @package TEC\Conference\Views
 */

namespace TEC\Conference\Views;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Assets\Asset;
use TEC\Conference\Vendor\StellarWP\Assets\Assets as Stellar_Assets;

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

	/**
	 * Checks for specified custom post types on single post pages and enqueues assets if true.
	 *
	 * @since TBD
	 */
	public function enqueue_views_posttype_assets() {
		if ( ! is_single() ) {
			return;
		}

		$post_types = array(
			Plugin::SESSION_POSTTYPE,
			Plugin::SPEAKER_POSTTYPE,
			Plugin::SPONSOR_POSTTYPE,
		);

		if ( ! in_array( get_post_type(), $post_types ) ) {
			return;
		}

		Stellar_Assets::instance()->enqueue_group( 'conference-schedule-pro-views' );
	}
}
