<?php

// Registers custom taxonomies to post types.
function wpcs_register_taxonomies() {

	// Labels for tracks.
	$tracklabels = array(
		'name'          => __( 'Tracks',         'wp-conference-schedule' ),
		'singular_name' => __( 'Track',          'wp-conference-schedule' ),
		'search_items'  => __( 'Search Tracks',  'wp-conference-schedule' ),
		'popular_items' => __( 'Popular Tracks', 'wp-conference-schedule' ),
		'all_items'     => __( 'All Tracks',     'wp-conference-schedule' ),
		'edit_item'     => __( 'Edit Track',     'wp-conference-schedule' ),
		'update_item'   => __( 'Update Track',   'wp-conference-schedule' ),
		'add_new_item'  => __( 'Add Track',      'wp-conference-schedule' ),
		'new_item_name' => __( 'New Track',      'wp-conference-schedule' ),
	);

	// Register the Tracks taxonomy.
	register_taxonomy(
		'wpcs_track',
		'wpcs_session',
		array(
			'labels'       => $tracklabels,
			'rewrite'      => array( 'slug' => 'track' ),
			'query_var'    => 'track',
			'hierarchical' => true,
			'public'       => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rest_base'    => 'session_track',
		)
	);
}

add_action( 'init', 'wpcs_register_taxonomies' );