<?php
/**
 * Handles Setup of Post Types.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */

namespace TEC\Conference\Taxonomies;

use TEC\Conference\Plugin;
use WP_Taxonomy;
use WP_Post;

/**
 * Class Abstract_Post_Types
 *
 * @since   TBD
 *
 * @package TEC\Conference\Post_Types
 */
abstract class Abstract_Taxonomy {

	/**
	 * The registered post type object.
	 *
	 * @since TBD
	 *
	 * @var WP_Taxonomy
	 */
	 protected $taxonomy_object;

	 /**
	  * Abstract_Post_Types constructor.
	  *
	  * @since TBD
	  *
	  * @return WP_Taxonomy $post_type_object The custom post type object.
	  */
	 public function get_taxonomy_object() {
		 return $this->taxonomy_object;
	 }

	/**
	 * Registers the custom taxonomy.
	 *
	 * @since TBD
	 */
	abstract public function register_taxonomy();
}
