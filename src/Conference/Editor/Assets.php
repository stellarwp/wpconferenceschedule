<?php
/**
 * Handles Conference Schedule Editor Assets.
 *
 * @since TBD
 *
 * @package TEC\Conference\Editor
 */

namespace TEC\Conference\Editor;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Assets\Asset;
use TEC\Conference\Vendor\StellarWP\Assets\Assets as Stellar_Assets;

/**
 * Class Assets
 *
 * @since TBD
 *
 * @package TEC\Conference\Editor
 */
class Assets {

	/**
	 * Registers the editor assets.
	 *
	 * @since TBD
	 */
	public function register_editor_assets() {
		Asset::add(
			'conference-schedule-pro-editor-css',
			'conference-schedule-views.css'
		)
		->set_dependencies( 'conference-schedule-pro-font-awesome', 'dashicons' )
		->add_to_group( 'conference-schedule-pro-editor' )
		->register();

		Asset::add(
			'conference-schedule-pro-font-awesome',
			'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta2/css/all.min.css'
		)
			->add_to_group( 'conference-schedule-pro-editor' )
		->register();

		Asset::add(
			'conference-schedule-schedule-block-js',
			'conference-schedule-block.js'
		)
		->set_dependencies( 'wp-blocks', 'wp-i18n', 'wp-editor' )
		->add_to_group( 'conference-schedule-pro-editor' )
		->register();
	}
}
