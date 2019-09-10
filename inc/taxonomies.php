<?php

// Registers custom taxonomies to post types.
function wpcs_register_taxonomies() {

	// Labels for tracks.
	$track_labels = array(
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
			'labels'       => $track_labels,
			'rewrite'      => array( 'slug' => 'track' ),
			'query_var'    => 'track',
			'hierarchical' => true,
			'public'       => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rest_base'    => 'session_track',
		)
	);

	// Labels for locations.
	$location_labels = array(
		'name'          => __( 'Locations',         'wp-conference-schedule' ),
		'singular_name' => __( 'Location',          'wp-conference-schedule' ),
		'search_items'  => __( 'Search Locations',  'wp-conference-schedule' ),
		'popular_items' => __( 'Popular Locations', 'wp-conference-schedule' ),
		'all_items'     => __( 'All Locations',     'wp-conference-schedule' ),
		'edit_item'     => __( 'Edit Location',     'wp-conference-schedule' ),
		'update_item'   => __( 'Update Location',   'wp-conference-schedule' ),
		'add_new_item'  => __( 'Add Location',      'wp-conference-schedule' ),
		'new_item_name' => __( 'New Location',      'wp-conference-schedule' ),
	);

	// Register the Locations taxonomy.
	register_taxonomy(
		'wpcs_location',
		'wpcs_session',
		array(
			'labels'       => $location_labels,
			'rewrite'      => array( 'slug' => 'location' ),
			'query_var'    => 'location',
			'hierarchical' => true,
			'public'       => true,
			'show_ui'      => true,
			'show_in_rest' => true,
			'rest_base'    => 'session_location',
		)
	);

}

add_action( 'init', 'wpcs_register_taxonomies' );