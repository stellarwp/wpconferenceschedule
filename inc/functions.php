<?php

// add fields to add track taxonomy
function wpcs_add_track_fields() { ?>

	<div class="form-field">
		<label for="track_background_color">Track Background Color</label>
		<input type="text" name="track_background_color" id="track_background_color" class="track-color-picker" data-alpha-enabled="true">
	</div>
	<div class="form-field">
		<label for="track_text_color">Track Text Color</label>
		<input type="text" name="track_text_color" id="track_text_color" class="track-color-picker" data-alpha-enabled="true">
	</div>
	
	<?php } 
	
	add_action( 'wpcs_track_add_form_fields' , 'wpcs_add_track_fields' );

// add fields to edit track taxonomy
function wpcs_edit_track_fields( $term, $taxonomy ) {
	$backgroundValue = get_term_meta($term->term_id, 'track_background_color', true);
	$textValue = get_term_meta($term->term_id, 'track_text_color', true);
	 ?>

	<tr class="form-field">
		<th scope="row"><label for="track_background_color">Track Background Color</label></th>
			<td><input type="text" name="track_background_color" id="track_background_color" class="track-color-picker" size="40" value="<?php echo esc_attr($backgroundValue); ?>" data-alpha-enabled="true"></td>
	</tr>
	<tr class="form-field">
		<th scope="row"><label for="track_text_color">Track Text Color</label></th>
			<td><input type="text" name="track_text_color" id="track_text_color" class="track-color-picker" size="40" value="<?php echo esc_attr($textValue); ?>" data-alpha-enabled="true"></td>
	</tr>
	
	<?php } 
	
	add_action( 'wpcs_track_edit_form_fields' , 'wpcs_edit_track_fields', 10, 2 );


// save to database
	function wpcs_created_track_fields( $term_id ) {
		update_term_meta( $term_id, 'track_background_color', sanitize_text_field($_POST['track_background_color'] ) );
		update_term_meta( $term_id, 'track_text_color', sanitize_text_field($_POST['track_text_color'] ) );
	}
	add_action( 'created_wpcs_track' , 'wpcs_created_track_fields' );
	add_action( 'edited_wpcs_track' , 'wpcs_created_track_fields' );
