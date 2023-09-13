<?php
/**
 * Handles the sponsors shortcode.
 *
 * @since   TBD
 *
 * @package TEC\Conference\Views\Shortcode
 */

namespace TEC\Conference\Views\Shortcode;

use TEC\Conference\Vendor\StellarWP\Assets\Assets;
use WP_Query;

/**
 * Class Sponsors
 *
 * @since   TBD
 *
 * @package TEC\Conference\Views\Shortcode
 */
class Sponsors {

	/**
	 * The [wpcs_sponsors] shortcode handler.
	 *
	 * @since TBD
	 *
	 * @param array<string|mixed> $attr Array of attributes from shortcode.
	 *
	 * @return string The HTML output the shortcode.
	 */
	public function render_shortcode( $attr ) {
		Assets::instance()->enqueue_group( 'conference-schedule-pro-views' );

		$attr = shortcode_atts( [
			'link' => 'none',
			'title' => 'visible',
			'content' => 'full',
			'excerpt_length' => 55,
			'heading_level' => 'h2'
		], $attr );

		$attr['link'] = strtolower( $attr['link'] );
		$terms        = $this->get_sponsor_levels( 'conference_sponsor_level_order', 'wpcsp_sponsor_level' );

		ob_start();
		?>

		<div class="wpcsp-sponsors">
		<?php foreach ( $terms as $term ) :
			$sponsors = new WP_Query( [ 'post_type' => 'wpcsp_sponsor', 'order' => 'ASC', 'orderby' => 'title', 'posts_per_page' => - 1, 'taxonomy' => $term->taxonomy, 'term' => $term->slug ] );

			if ( ! $sponsors->have_posts() ) {
				continue;
			}

			// temporarily hide elements
			$attr['title']   = 'hidden';
			$attr['content'] = 'hidden';
			?>

		<div class="wpcsp-sponsor-level wpcsp-sponsor-level-<?php echo sanitize_html_class( $term->slug ); ?>">
			<?php $heading_level = $attr['heading_level'] ?: 'h2'; ?>
			<<?php echo $heading_level; ?> class="wpcsp-sponsor-level-heading"><span><?php echo esc_html( $term->name ); ?></span></<?php echo $heading_level; ?>>

			<ul class="wpcsp-sponsor-list">
				<?php while ( $sponsors->have_posts() ) :
					$sponsors->the_post();
					$website     = get_post_meta( get_the_ID(), 'wpcsp_website_url', true );
					$logo_height = ( get_term_meta( $term->term_id, 'wpcsp_logo_height', true ) ) ? get_term_meta( $term->term_id, 'wpcsp_logo_height', true ) . 'px' : 'auto';
					$image       = ( has_post_thumbnail() ) ? '<img class="wpcsp-sponsor-image" src="' . get_the_post_thumbnail_url( get_the_ID(), 'full' ) . '" alt="' . get_the_title( get_the_ID() ) . '" style="width: auto; max-height: ' . $logo_height . ';"  />' : null;
					//
					?>

					<li id="wpcsp-sponsor-<?php the_ID(); ?>" class="wpcsp-sponsor">
						<?php if ( 'visible' === $attr['title'] ) : ?>
							<?php if ( 'website' === $attr['link'] && $website ) : ?>
								<h3>
									<a href="<?php echo esc_attr( esc_url( $website ) ); ?>">
										<?php the_title(); ?>
									</a>
								</h3>
							<?php elseif ( 'post' === $attr['link'] ) : ?>
								<h3>
									<a href="<?php echo esc_attr( esc_url( get_permalink() ) ); ?>">
										<?php the_title(); ?>
									</a>
								</h3>
							<?php else : ?>
								<h3>
									<?php the_title(); ?>
								</h3>
							<?php endif; ?>
						<?php endif; ?>

						<div class="wpcsp-sponsor-description">
							<?php if ( 'website' == $attr['link'] && $website ) : ?>
								<a href="<?php echo esc_attr( esc_url( $website ) ); ?>">
									<?php echo $image; ?>
								</a>
							<?php elseif ( 'post' == $attr['link'] ) : ?>
								<a href="<?php echo esc_attr( esc_url( get_permalink() ) ); ?>">
									<?php echo $image; ?>
								</a>
							<?php else : ?>
								<?php echo $image; ?>
							<?php endif; ?>

							<?php if ( 'full' === $attr['content'] ) : ?>
								<?php the_content(); ?>
							<?php elseif ( 'excerpt' === $attr['content'] ) : ?>
								<?php echo wpautop( wp_trim_words( get_the_content(), absint( $attr['excerpt_length'] ), apply_filters( 'excerpt_more', ' &hellip;' ) ) ); ?>
							<?php endif; ?>
						</div>
					</li>
				<?php endwhile; ?>
			</ul>
			</div>
		<?php endforeach; ?>
		</div>

		<?php

		wp_reset_postdata();
		$content = ob_get_contents();
		ob_end_clean();

		return $content;
	}

	/**
	 * Returns the sponsor level terms in set order.
	 *
	 * @since TBD
	 *
	 * @param string $option   The option key to fetch from the database.
	 * @param string $taxonomy The taxonomy to fetch terms for.
	 *
	 * @return array Array of term objects.
	 */
	public function get_sponsor_levels( string $option, string $taxonomy ): array {
		$option       = get_option( $option, [] );
		$term_objects = get_terms( $taxonomy, [ 'get' => 'all' ] );
		$terms        = [];

		foreach ( $term_objects as $term ) {
			$terms[ $term->term_id ] = $term;
		}

		return $this->order_terms_by_option( $terms, $option );
	}

	/**
	 * Orders the terms by a given option.
	 *
	 * @since TBD
	 *
	 * @param array $terms  The terms to be ordered.
	 * @param array $option The order option.
	 *
	 * @return array The ordered terms.
	 */
	private function order_terms_by_option( array $terms, array $option ): array {
		$ordered_terms = [];

		foreach ( $option as $term_id ) {
			if ( isset( $terms[ $term_id ] ) ) {
				$ordered_terms[] = $terms[ $term_id ];
				unset( $terms[ $term_id ] );
			}
		}

		return array_merge( $ordered_terms, array_values( $terms ) );
	}
}
