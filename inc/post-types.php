<?php

// Add page templates
add_filter( 'single_template', 'wpcs_set_single_session_template' );
function wpcs_set_single_session_template($single_template) {
	global $post;

	if ($post->post_type == 'wpcs_session' ) {
			$single_template = WPCS_DIR . '/templates/session-template.php';
	}
	return $single_template;
}