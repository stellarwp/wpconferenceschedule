<?php
/**
 * Handles Conference Schedule Block,
 *
 * @since TBD
 *
 * @package TEC\Conference\Editor
 */

namespace TEC\Conference\Editor;

use TEC\Conference\Views\Shortcode\Schedule;

/**
 * Class Block
 *
 * @since TBD
 *
 * @package TEC\Conference\Editor
 */
class Block {

	/**
	 * Registers the conference schedule block.
	 *
	 * @since TBD
	 */
	public function register_block() {
		register_block_type( 'wpcs/schedule-block', [
			'editor_script'   => 'conference-schedule-schedule-block-js',
			'attributes'      => [
				'date'         => [ 'type' => 'string' ],
				'color_scheme' => [ 'type' => 'string' ],
				'layout'       => [ 'type' => 'string' ],
				'row_height'   => [ 'type' => 'string' ],
				'session_link' => [ 'type' => 'string' ],
				'tracks'       => [ 'type' => 'string' ],
				'align'        => [ 'type' => 'string' ],
			],
			'render_callback' => [ $this, 'schedule_block_output' ],
		] );

		register_block_style( 'wpcs/schedule-block', [
				'name'         => 'conference-schedule-pro-views',
				'label'        => __( 'Conference Schedule Pro Views', 'conference-schedule-pro' ),
				'style_handle' => 'conference-schedule-pro-editor-css',
			] );
	}

	/**
	 * Schedule Block Dynamic content Output.
	 *
	 * @since TBD
	 *
	 * @param array $props An array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function schedule_block_output($props) {
		$schedule = new Schedule();
		return $schedule->render_shortcode( $props );
	}
}
