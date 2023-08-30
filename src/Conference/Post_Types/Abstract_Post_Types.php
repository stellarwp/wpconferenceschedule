<?php
/**
 * Handles Setup of Post Types.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Post_Types;

use TEC\Conference\Plugin;
use WP_Post_Type;
use WP_Post;

/**
 * Class Abstract_Post_Types
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */
abstract class Abstract_Post_Types {

	/**
	 * The registered post type object.
	 *
	 * @since TBD
	 *
	 * @var WP_Post_Type
	 */
	 protected $post_type_object;

	 /**
	  * Abstract_Post_Types constructor.
	  *
	  * @since TBD
	  *
	  * @return WP_Post_Type $post_type_object The custom post type object.
	  */
	 public function get_post_type_object() {
		 return $this->post_type_object;
	 }

	/**
	 * Registers the custom post type.
	 *
	 * @since TBD
	 */
	abstract public function register_post_type();

	/**
	 * Changes the title placeholder text for the custom post type.
	 *
	 * @since TBD
	 *
	 * @param string  $title The current placeholder text.
	 * @param WP_Post $post  The current post object.
	 *
	 * @return string The modified placeholder text.
	 */
	public function change_title_text( $title, $post ) {
		if ( $post->post_type === Plugin::SESSION_POSTTYPE ) {
			$title = $this->get_title_text();
		}

		return $title;
	}

	/**
	 * Returns the title text for the custom post type.
	 *
	 * @since TBD
	 *
	 * @return string The title text.
	 */
	abstract public function get_title_text(): string;
}
