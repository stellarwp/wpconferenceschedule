<?php

// add fields to add track taxonomy
function wpcs_add_track_fields() { ?>

	<div class="form-field">
		<label for="track_color">Track Color</label>
		<input type="text" name="track_color" id="track_color" class="track-color-picker">
		
	</div>
	
	<?php } 
	
	add_action( 'wpcs_track_add_form_fields' , 'wpcs_add_track_fields' );

// add fields to edit track taxonomy
function wpcs_edit_track_fields( $term, $taxonomy ) {
	$value = get_term_meta($term->term_id, 'track_color', true);
	 ?>

	<tr class="form-field">
		<th scope="row"><label for="track_color">Track Color</label></th>
			<td><input type="text" name="track_color" id="track_color" class="track-color-picker" size="40" value="<?php echo esc_attr($value); ?>"></td>
	</tr>
	
	<?php } 
	
	add_action( 'wpcs_track_edit_form_fields' , 'wpcs_edit_track_fields', 10, 2 );


// save to database
	function wpcs_created_track_fields( $term_id ) {
		update_term_meta( $term_id, 'track_color', sanitize_text_field($_POST['track_color'] ) );
	}
	add_action( 'created_wpcs_track' , 'wpcs_created_track_fields' );
	add_action( 'edited_wpcs_track' , 'wpcs_created_track_fields' );
