<?php
/**
 * Conference Schedule Speaker Meta.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin\Meta
 */

namespace TEC\Conference\Admin\Meta;

use TEC\Conference\Plugin;

/**
 * Class Speaker
 *
 * @since   TBD
 *
 * @package TEC\Conference\Admin\Meta
 */
class Speaker {

	/**
	 * Adds the session information meta box.
	 *
	 * @since TBD
	 */
	public function speaker_metabox() {
		$cmb = new_cmb2_box( [
			'id'           => 'wpcsp_speaker_metabox',
			'title'        => _x( 'Speaker Information', 'speaker meta box title', 'wpcsp' ),
			'object_types' => [ Plugin::SPEAKER_POSTTYPE ],
			'context'      => 'normal',
			'priority'     => 'high',
			'show_names'   => true,
		] );

		// First Name
		$cmb->add_field( [
			'name' => _x( 'First Name', 'speaker meta box field', 'wpcsp' ),
			'id'   => 'wpcsp_first_name',
			'type' => 'text'
		] );

		// Last Name
		$cmb->add_field( [
			'name' => _x( 'Last Name', 'speaker meta box field', 'wpcsp' ),
			'id'   => 'wpcsp_last_name',
			'type' => 'text'
		] );

		// Title
		$cmb->add_field( [
			'name' => _x( 'Title', 'speaker meta box field', 'wpcsp' ),
			'id'   => 'wpcsp_title',
			'type' => 'text'
		] );

		// Organization
		$cmb->add_field( [
			'name' => _x( 'Organization', 'speaker meta box field', 'wpcsp' ),
			'id'   => 'wpcsp_organization',
			'type' => 'text'
		] );

		// Facebook URL
		$cmb->add_field( [
			'name'      => _x( 'Facebook URL', 'speaker meta box field', 'wpcsp' ),
			'id'        => 'wpcsp_facebook_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// Twitter URL
		$cmb->add_field( [
			'name'      => _x( 'Twitter URL', 'speaker meta box field', 'wpcsp' ),
			'id'        => 'wpcsp_twitter_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// Instagram URL
		$cmb->add_field( [
			'name'      => _x( 'Instagram URL', 'speaker meta box field', 'wpcsp' ),
			'id'        => 'wpcsp_instagram_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// LinkedIn URL
		$cmb->add_field( [
			'name'      => _x( 'LinkedIn URL', 'speaker meta box field', 'wpcsp' ),
			'id'        => 'wpcsp_linkedin_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// YouTube URL
		$cmb->add_field( [
			'name'      => _x( 'YouTube URL', 'speaker meta box field', 'wpcsp' ),
			'id'        => 'wpcsp_youtube_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );

		// Website URL
		$cmb->add_field( [
			'name'      => _x( 'Website URL', 'speaker meta box field', 'wpcsp' ),
			'id'        => 'wpcsp_website_url',
			'type'      => 'text_url',
			'protocols' => [ 'http', 'https' ]
		] );
	}

	/**
	 * Filters session speaker meta field.
	 *
	 * @since TBD
	 *
	 * @param array $cmb The current CMB2 box object.
	 */
	public function filter_session_speaker_meta_field( $cmb ) {
		// Speaker Display Type
		$cmb->add_field( [
			'name'             => _x( 'Speaker Display', 'session meta field', 'wpcsp' ),
			'id'               => 'wpcsp_session_speaker_display',
			'type'             => 'radio',
			'show_option_none' => false,
			'options'          => [
				'typed' => _x( 'Speaker Names (Typed)', 'session meta field option', 'wpcsp' ),
				'cpt'   => _x( 'Speaker Select (from Speakers CPT)', 'session meta field option', 'wpcsp' )
			],
			'default'          => 'typed'
		] );

		// Fetch speakers
		$args     = [
			'numberposts' => - 1,
			'post_type'   => 'wpcsp_speaker',
		];
		$speakers = get_posts( $args );
		$speakers = wp_list_pluck( $speakers, 'post_title', 'ID' );

		// Speaker Select Field
		$cmb->add_field( [
			'name'       => _x( 'Speakers', 'session meta field', 'wpcsp' ),
			'id'         => 'wpcsp_session_speakers',
			'desc'       => _x( 'Select speakers. Drag to reorder.', 'session meta field description', 'wpcsp' ),
			'type'       => 'pw_multiselect',
			'options'    => $speakers,
			'attributes' => [
				'data-conditional-id'    => 'wpcsp_session_speaker_display',
				'data-conditional-value' => 'cpt'
			]
		] );

		// Speaker Names Field
		$cmb->add_field( [
			'name'       => _x( 'Speaker Name(s)', 'session meta field', 'wpcsp' ),
			'id'         => '_wpcs_session_speakers',
			'type'       => 'text',
			'attributes' => [
				'data-conditional-id'    => 'wpcsp_session_speaker_display',
				'data-conditional-value' => 'typed'
			]
		] );

		// Fetch sponsors
		$args     = [
			'numberposts' => - 1,
			'post_type'   => 'wpcsp_sponsor',
		];
		$sponsors = get_posts( $args );
		$sponsors = wp_list_pluck( $sponsors, 'post_title', 'ID' );

		// Sponsor Select Field
		$cmb->add_field( [
			'name'    => _x( 'Sponsors', 'session meta field', 'wpcsp' ),
			'id'      => 'wpcsp_session_sponsors',
			'desc'    => _x( 'Select sponsor. Drag to reorder.', 'session meta field description', 'wpcsp' ),
			'type'    => 'pw_multiselect',
			'options' => $sponsors
		] );
	}
}
