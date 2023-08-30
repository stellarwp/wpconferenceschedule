<?php
/**
 * Organizes Conference Schedule Post Types in Admin Nav.
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */

namespace TEC\Conference\Admin;

use TEC\Conference\Plugin;

/**
 * Class Conference_Schedule
 *
 * @since TBD
 *
 * @package TEC\Conference\Admin
 */
class Menu {

	/**
	 * The Conference Schedule menu slug.
	 *
	 * @since TBD
	 *
	 * @var string
	 */
	protected $menu_slug = Plugin::SLUG;

	/**
	 * Adds Conference Schedule menu item in the WordPress Admin Nav.
	 *
	 * @since TBD
	 */
	public function add_conference_schedule_menu() {
		add_menu_page(
			'Conference Schedule',
			'Conference Schedule',
			'read',
			$this->menu_slug,
			'',
			'dashicons-schedule',
			21
		);
	}

	/**
	 * Organizes the post types under the Conference Schedule menu item.
	 *
	 * @since TBD
	 */
	public function organize_post_types() {
		// Sessions
		add_submenu_page(
			$this->menu_slug,
			'Sessions',
			'Sessions',
			'read',
			'edit.php?post_type=' . Plugin::SESSION_POSTTYPE
		);
		add_submenu_page(
			$this->menu_slug,
			'Add New Session',
			'Add New Session',
			'read',
			'post-new.php?post_type=' . Plugin::SESSION_POSTTYPE
		);

		// Speakers
		add_submenu_page(
			$this->menu_slug,
			'Speakers',
			'Speakers',
			'read',
			'edit.php?post_type=' . Plugin::SPEAKER_POSTTYPE
		);
		add_submenu_page(
			$this->menu_slug,
			'Add New Speaker',
			'Add New Speaker',
			'read',
			'post-new.php?post_type=' . Plugin::SPEAKER_POSTTYPE
		);

		// Sponsors
		add_submenu_page(
			$this->menu_slug,
			'Sponsors',
			'Sponsors',
			'read',
			'edit.php?post_type=' . Plugin::SPONSOR_POSTTYPE
		);
		add_submenu_page(
			$this->menu_slug,
			'Add New Sponsor',
			'Add New Sponsor',
			'read',
			'post-new.php?post_type=' . Plugin::SPONSOR_POSTTYPE
		);
	}
}
