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
	public function render_shortcode( $attr, $content ) {
		Assets::instance()->enqueue_group( 'conference-schedule-pro-views' );

		$attr = shortcode_atts( [ 'link' => 'none', 'title' => 'visible', 'content' => 'full', 'excerpt_length' => 55, 'heading_level' => 'h2' ], $attr );
		/*
		title = visible
		link = website, post
		content = full, excerpt
		heading_level = h2
		excerpt_length = 0
		*/

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
}
