<?php
/**
 * Abstract class to handle setup of custom taxonomies
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
 */

namespace TEC\Conference\Taxonomies;

use WP_Taxonomy;

/**
 * Class Abstract_Taxonomy
 *
 * @since   TBD
 *
 * @package TEC\Conference\Taxonomies
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
