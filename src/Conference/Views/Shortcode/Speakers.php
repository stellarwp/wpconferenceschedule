<?php
/**
 * Handles the sponsors shortcode.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Views\Shortcode
 */

namespace TEC\Conference\Views\Shortcode;

use TEC\Conference\Plugin;
use TEC\Conference\Vendor\StellarWP\Assets\Assets;
use WP_Query;

/**
 * Class Sponsors
 *
 * @since   TBD
 *
 * @package TEC\Conference\Views\Shortcode
 */
class Speakers {

	/**
	 * The [wpcs_speakers] shortcode handler.
	 *
	 * @since TBD
	 *
	 * @param array<string|mixed> $attr Array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function render_shortcode( $attr ) {
		Assets::instance()->enqueue_group( 'conference-schedule-pro-views' );
		global $post;

		// Prepare the shortcodes arguments
		$attr = shortcode_atts( [
			'show_image'     => true,
			'image_size'     => 150,
			'show_content'   => true,
			'posts_per_page' => - 1,
			'orderby'        => 'date',
			'order'          => 'desc',
			'speaker_link'   => '',
			'track'          => '',
			'groups'         => '',
			'columns'        => 1,
			'gap'            => 30,
			'align'          => 'left'
		], $attr );

		foreach ( [ 'orderby', 'order', 'speaker_link' ] as $key_for_case_sensitive_value ) {
			$attr[ $key_for_case_sensitive_value ] = strtolower( $attr[ $key_for_case_sensitive_value ] );
		}

		$attr['show_image']   = $this->str_to_bool( $attr['show_image'] );
		$attr['show_content'] = $this->str_to_bool( $attr['show_content'] );
		$attr['orderby']      = in_array( $attr['orderby'], [ 'date', 'title', 'rand' ] ) ? $attr['orderby'] : 'date';
		$attr['order']        = in_array( $attr['order'], [ 'asc', 'desc' ] ) ? $attr['order'] : 'desc';
		$attr['speaker_link'] = $attr['speaker_link'] == 'permalink' ? $attr['speaker_link'] : '';
		$attr['track']        = array_filter( explode( ',', $attr['track'] ) );
		$attr['groups']       = array_filter( explode( ',', $attr['groups'] ) );

		// Fetch all the relevant sessions
		$session_args = [ 'post_type' => Plugin::SESSION_POSTTYPE, 'posts_per_page' => -1 ];

		if ( isset( $attr['track'] ) && $attr['track'] !== [] ) {
			$session_args['tax_query'] = [ [ 'taxonomy' => Plugin::TRACK_TAXONOMY, 'field' => 'slug', 'terms' => $attr['track'] ] ];
		}

		$sessions = get_posts( $session_args );

		// Parse the sessions
		$speaker_ids = $speakers_tracks = [];
		foreach ( $sessions as $session ) {
			// Get the speaker IDs for all the sessions in the requested tracks
			$session_speaker_ids = get_post_meta( $session->ID, '_rwc_cs_speaker_id' );
			$speaker_ids         = array_merge( $speaker_ids, $session_speaker_ids );

			// Map speaker IDs to their corresponding tracks
			$session_terms = wp_get_object_terms( $session->ID, 'RWC_track' );
			foreach ( $session_speaker_ids as $speaker_id ) {
				if ( isset( $speakers_tracks[ $speaker_id ] ) ) {
					$speakers_tracks[ $speaker_id ] = array_merge( $speakers_tracks[ $speaker_id ], wp_list_pluck( $session_terms, 'slug' ) );
				} else {
					$speakers_tracks[ $speaker_id ] = wp_list_pluck( $session_terms, 'slug' );
				}
			}
		}

		// Remove duplicate entries
		$speaker_ids = array_unique( $speaker_ids );
		foreach ( $speakers_tracks as $speaker_id => $tracks ) {
			$speakers_tracks[ $speaker_id ] = array_unique( $tracks );
		}

		// Fetch all specified speakers
		$speaker_args = [ 'post_type' => 'wpcsp_speaker', 'posts_per_page' => (int) $attr['posts_per_page'], 'orderby' => $attr['orderby'], 'order' => $attr['order'] ];

		if ( isset( $attr['track'] ) && $attr['track'] !== [] ) {
			$speaker_args['post__in'] = $speaker_ids;
		}

		if ( isset( $attr['groups'] ) && $attr['groups'] !== [] ) {
			$speaker_args['tax_query'] = [ [ 'taxonomy' => 'wpcsp_speaker_level', 'field' => 'slug', 'terms' => $attr['groups'] ] ];
		}

		$speakers = new WP_Query( $speaker_args );

		if ( ! $speakers->have_posts() ) {
			return '';
		}

		// Render the HTML for the shortcode
		ob_start();
		?>

		<div class="wpcsp-speakers" style="text-align: <?php echo $attr['align']; ?>; display: grid; grid-template-columns: repeat(<?php echo $attr['columns']; ?>, 1fr); grid-gap: <?php echo $attr['gap']; ?>px;">

			<?php while ( $speakers->have_posts() ) :
				$speakers->the_post();

				$post_id            = get_the_ID();
				$first_name         = get_post_meta( $post_id, 'wpcsp_first_name', true );
				$last_name          = get_post_meta( $post_id, 'wpcsp_last_name', true );
				$full_name          = $first_name . ' ' . $last_name;
				$title_organization = [];
				$title              = ( get_post_meta( $post_id, 'wpcsp_title', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'wpcsp_title', true ) : null;
				$organization       = ( get_post_meta( $post_id, 'wpcsp_organization', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'wpcsp_organization', true ) : null;

				$speaker_classes = [ 'wpcsp-speaker', 'wpcsp-speaker-' . sanitize_html_class( $post->post_name ) ];

				if ( isset( $speakers_tracks[ get_the_ID() ] ) ) {
					foreach ( $speakers_tracks[ get_the_ID() ] as $track ) {
						$speaker_classes[] = sanitize_html_class( 'wpcsp-track-' . $track );
					}
				}

				?>

				<!-- Organizers note: The id attribute is deprecated and only remains for backwards compatibility, please use the corresponding class to target individual speakers -->
				<div class="wpcsp-speaker" id="wpcsp-speaker-<?php echo sanitize_html_class( $post->post_name ); ?>" class="<?php echo implode( ' ', $speaker_classes ); ?>">

					<?php if ( has_post_thumbnail( $post_id ) && $attr['show_image'] == true ) {
						echo get_the_post_thumbnail( $post_id, [ $attr['image_size'], $attr['image_size'] ], [ 'alt' => $full_name, 'class' => 'wpcsp-speaker-image' ] );
					} ?>

					<h2 class="wpcsp-speaker-name">
						<?php if ( 'permalink' === $attr['speaker_link'] ) : ?>

							<a href="<?php the_permalink(); ?>">
								<?php echo $full_name; ?>
							</a>

						<?php else : ?>

							<?php echo $full_name; ?>

						<?php endif; ?>
					</h2>

					<?php if ( $title_organization ) { ?>
						<p class="wpcsp-speaker-title-organization">
							<?php echo implode( ', ', $title_organization ); ?>
						</p>
					<?php } ?>

					<div class="wpcsp-speaker-description">
						<?php if ( $attr['show_content'] == true ) {
							the_content();
						} ?>
					</div>
				</div>

			<?php endwhile; ?>

		</div>

		<?php

		wp_reset_postdata();

		return ob_get_clean();
	}

	/**
	 * Convert a string representation of a boolean to an actual boolean
	 *
	 * @param string|bool
	 *
	 * @return bool
	 */
	public function str_to_bool( $value ): bool {
		if ( true === $value ) {
			return true;
		}

		return in_array( strtolower( trim( $value ) ), [ 'yes', 'true', '1' ] );
	}
}
