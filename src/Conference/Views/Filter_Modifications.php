<?php
/**
 * Handles modifications to outputs.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Views
 */

namespace TEC\Conference\Views;

use TEC\Conference\Plugin;

/**
 * Class Filter_Modifications
 *
 * @since   TBD
 *
 * @package TEC\Conference\Views
 */
class Filter_Modifications {

	/**
	 * Adds single sessions tags.
	 *
	 * @since TBD
	 *
	 * @return void
	 */
	public function single_session_tags(): void {
		$terms = get_the_terms( get_the_ID(), Plugin::TAGS_TAXONOMY );
		if ( ! is_wp_error( $terms ) && ! empty( $terms ) ) {
			$term_names = wp_list_pluck( $terms, 'name' );
			$terms      = implode( ", ", $term_names );
			if ( $terms !== '' && $terms !== '0' ) {
				echo '<li class="wpsc-single-session-taxonomies-taxonomy wpsc-single-session-location"><i class="fas fa-tag"></i>' . $terms . '</li>';
			}
		}
	}

	/**
	 * Filters session speakers output based on speaker display type.
	 *
	 * @since TBD
	 *
	 * @param string $speakers_typed Predefined speakers typed.
	 * @param int    $session_id     Session post ID.
	 *
	 * @return string HTML output of session speakers.
	 */
	public function filter_session_speakers( $speakers_typed, $session_id ): string {
		$speaker_display = get_post_meta( $session_id, 'wpcsp_session_speaker_display', true );

		if ( $speaker_display == 'typed' ) {
			return $speakers_typed;
		}

		$html         = '';
		$speakers_cpt = get_post_meta( $session_id, 'wpcsp_session_speakers', true );

		if ( $speakers_cpt ) {
			ob_start();
			foreach ( $speakers_cpt as $post_id ) {
				$first_name         = get_post_meta( $post_id, 'wpcsp_first_name', true );
				$last_name          = get_post_meta( $post_id, 'wpcsp_last_name', true );
				$full_name          = $first_name . ' ' . $last_name;
				$title_organization = [];
				$title              = ( get_post_meta( $post_id, 'wpcsp_title', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'wpcsp_title', true ) : null;
				$organization       = ( get_post_meta( $post_id, 'wpcsp_organization', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'wpcsp_organization', true ) : null;

				?>
				<div class="wpcsp-session-speaker">

					<?php if ( $full_name !== '' && $full_name !== '0' ) { ?>
						<div class="wpcsp-session-speaker-name">
							<?php echo $full_name; ?>
						</div>
					<?php } ?>

					<?php if ( $title_organization ) { ?>
						<div class="wpcsp-session-speaker-title-organization">
							<?php echo implode( ', ', $title_organization ); ?>
						</div>
					<?php } ?>

				</div>
				<?php
			}
			$html .= ob_get_clean();
		}

		return $html;
	}

	/**
	 * Generates session content header based on session tags.
	 *
	 * @since TBD
	 *
	 * @param int $session_id Session post ID.
	 *
	 * @return string HTML output of session content header.
	 */
	public function session_content_header( int $session_id ): string {
		$html         = '';
		$session_tags = get_the_terms( $session_id, Plugin::TAGS_TAXONOMY );
		if ( $session_tags ) {
			ob_start();
			?>
			<ul class="wpcsp-session-tags">
				<?php foreach ( $session_tags as $session_tag ) { ?>
					<li class="wpcsp-session-tags-tag">
						<a href="<?php echo get_term_link( $session_tag->term_id, 'wpcs_session_tag' ); ?>" class="wpcsp-session-tags-tag-link"><?php echo $session_tag->name; ?></a>
					</li>
				<?php } ?>
			</ul>
			<?php
			$html = ob_get_clean();
		}

		return $html;
	}


	/**
	 * Outputs session sponsors.
	 *
	 * @since TBD
	 *
	 * @param int $session_id The session ID.
	 *
	 * @return string The HTML of the session sponsors or empty string.
	 */
	public function session_sponsors( $session_id ): string {
		$session_sponsors = get_post_meta( $session_id, 'wpcsp_session_sponsors', true );
		if ( ! $session_sponsors ) {
			return '';
		}

		$sponsors = [];
		foreach ( $session_sponsors as $sponser_li ) {
			$sponsors[] = get_the_title( $sponser_li );
		}

		ob_start();

		if ( $sponsors !== [] ) {
			echo '<div class="wpcs-session-sponsor"><span class="wpcs-session-sponsor-label">Presented by: </span>' . implode( ', ', $sponsors ) . '</div>';
		}

		return ob_get_clean();
	}

	/**
	 * Filters single session speakers output based on speaker display type.
	 *
	 * @since TBD
	 *
	 * @param string $speakers_typed Predefined speakers typed.
	 * @param int    $session_id     Session post ID.
	 *
	 * @return string HTML output of single session speakers.
	 */
	public function filter_single_session_speakers( $speakers_typed, $session_id ): string {
		$speaker_display = get_post_meta( $session_id, 'wpcsp_session_speaker_display', true );

		if ( $speaker_display == 'typed' ) {
			return $speakers_typed;
		}

		$html         = '';
		$speakers_cpt = get_post_meta( $session_id, 'wpcsp_session_speakers', true );

		if ( $speakers_cpt ) {
			ob_start();
			?>
			<div class="wpcsp-single-session-speakers">
				<h2 class="wpcsp-single-session-speakers-title">Speakers</h2>
				<?php foreach ( $speakers_cpt as $post_id ) {
					$first_name         = get_post_meta( $post_id, 'wpcsp_first_name', true );
					$last_name          = get_post_meta( $post_id, 'wpcsp_last_name', true );
					$full_name          = $first_name . ' ' . $last_name;
					$title_organization = [];
					$title              = ( get_post_meta( $post_id, 'wpcsp_title', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'wpcsp_title', true ) : null;
					$organization       = ( get_post_meta( $post_id, 'wpcsp_organization', true ) ) ? $title_organization[] = get_post_meta( $post_id, 'wpcsp_organization', true ) : null;

					?>
					<div class="wpcsp-single-session-speakers-speaker">

						<?php if ( has_post_thumbnail( $post_id ) ) {
							echo get_the_post_thumbnail( $post_id, 'thumbnail', [ 'alt' => $full_name, 'class' => 'wpcsp-single-session-speakers-speaker-image' ] );
						} ?>

						<?php if ( $full_name !== '' && $full_name !== '0' ) { ?>
							<h3 class="wpcsp-single-session-speakers-speaker-name">
								<a href="<?php echo get_the_permalink( $post_id ); ?>"><?php echo $full_name; ?></a>
							</h3>
						<?php } ?>

						<?php if ( $title_organization ) { ?>
							<div class="wpcsp-single-session-speakers-speaker-title-organization">
								<?php echo implode( ', ', $title_organization ); ?>
							</div>
						<?php } ?>

					</div>
					<?php
				}
				?>
			</div>
			<?php
			$html .= ob_get_clean();
		}

		return $html;
	}
}
