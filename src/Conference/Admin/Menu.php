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
	protected $menu_slug = 'edit.php?post_type=' . Plugin::SESSION_POSTTYPE;

	/**
	 * Get the menu slug for the Conference Schedule menu items.
	 *
	 * @since TBD
	 *
	 * @return string  The menu slug.
	 */
	public function get_menu_slug(): string {
		/**
		 * Filters the menu slug for the Conference Schedule menu items.
		 *
		 * @since TBD
		 *
		 * @param string $menu_slug The default menu slug.
		 *
		 * @return string $menu_slug The menu slug.
		 */
		return apply_filters( 'tec_conference_schedule_menu_slug', $this->menu_slug );
	}

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
			$this->get_menu_slug(),
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
		// Sessions.
		add_submenu_page(
			$this->get_menu_slug(),
			'Sessions',
			'Sessions',
			'read',
			'edit.php?post_type=' . Plugin::SESSION_POSTTYPE
		);
		add_submenu_page(
			$this->get_menu_slug(),
			'Add New Session',
			'Add New Session',
			'read',
			'post-new.php?post_type=' . Plugin::SESSION_POSTTYPE
		);
		// Submenu for Tracks Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			'Tracks',
			'Tracks',
			'read',
			'edit-tags.php?taxonomy=' . Plugin::TRACK_TAXONOMY . '&post_type=' . Plugin::SESSION_POSTTYPE
		);
		// Submenu for Locations Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			'Locations',
			'Locations',
			'read',
			'edit-tags.php?taxonomy=' . Plugin::LOCATION_TAXONOMY . '&post_type=' . Plugin::SESSION_POSTTYPE
		);
		// Submenu for Tags Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			'Tags',
			'Tags',
			'read',
			'edit-tags.php?taxonomy=' . Plugin::TAGS_TAXONOMY . '&post_type=' . Plugin::SESSION_POSTTYPE
		);

		// Speakers.
		add_submenu_page(
			$this->get_menu_slug(),
			'Speakers',
			'Speakers',
			'read',
			'edit.php?post_type=' . Plugin::SPEAKER_POSTTYPE
		);
		add_submenu_page(
			$this->get_menu_slug(),
			'Add New Speaker',
			'Add New Speaker',
			'read',
			'post-new.php?post_type=' . Plugin::SPEAKER_POSTTYPE
		);
		// Submenu for Groups Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			'Groups',
			'Groups',
			'read',
			'edit-tags.php?taxonomy=' . Plugin::GROUP_TAXONOMY . '&post_type=' . Plugin::SPEAKER_POSTTYPE
		);


		// Sponsors.
		add_submenu_page(
			$this->get_menu_slug(),
			'Sponsors',
			'Sponsors',
			'read',
			'edit.php?post_type=' . Plugin::SPONSOR_POSTTYPE
		);
		add_submenu_page(
			$this->get_menu_slug(),
			'Add New Sponsor',
			'Add New Sponsor',
			'read',
			'post-new.php?post_type=' . Plugin::SPONSOR_POSTTYPE
		);
		// Submenu for Sponsor Levels Taxonomy.
		add_submenu_page(
			$this->get_menu_slug(),
			'Sponsor Levels',
			'Sponsor Levels',
			'read',
			'edit-tags.php?taxonomy=' . Plugin::SPONSOR_LEVEL_TAXONOMY . '&post_type=' . Plugin::SPONSOR_POSTTYPE
		);
	}
}
