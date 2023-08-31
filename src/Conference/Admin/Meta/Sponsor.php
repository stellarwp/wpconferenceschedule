<?php
/**
 * Conference Schedule Sponsor Meta.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin\Meta
 */

namespace TEC\Conference\Admin\Meta;

use TEC\Conference\Plugin;

/**
 * Class Sponsor
 *
 * Handles the sponsor meta boxes.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin\Meta
 */
class Sponsor {

	/**
	 * Adds the sponsor information meta box.
	 *
	 * @since TBD
	 */
	public function sponsor_metabox(): void {
		$cmb = new_cmb2_box([
			'id'           => 'wpcsp_sponsor_metabox',
			'title'        => _x( 'Sponsor Information', 'sponsor meta box title', 'wpcsp' ),
			'object_types' => [ Plugin::SPONSOR_POSTTYPE ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		]);

		// Website URL
		$cmb->add_field([
			'name'      => _x( 'Website URL', 'sponsor meta box field', 'wpcsp' ),
			'id'        => 'wpcsp_website_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ],
		]);
	}

	/**
	 * Adds the sponsor level meta box.
	 *
	 * @since TBD
	 */
	public function sponsor_level_metabox(): void {
		$cmb = new_cmb2_box([
			'id'           => 'wpcsp_sponsor_level_metabox',
			'title'        => _x( 'Category Metabox', 'sponsor level meta box title', 'cmb2' ),
			'object_types' => [ 'term' ],
			'taxonomies'   => [ Plugin::SPONSOR_LEVEL_TAXONOMY ],
		]);

		// Logo Height
		$cmb->add_field([
			'name'       => _x( 'Logo Height', 'sponsor level meta box field', 'wpcsp' ),
			'desc'       => _x( 'Pixels', 'sponsor level meta box field description', 'wpcsp' ),
			'id'         => 'wpcsp_logo_height',
			'type'       => 'text_small',
			'attributes' => [
				'type'    => 'number',
				'pattern' => '\d*',
			],
		]);
	}
}
