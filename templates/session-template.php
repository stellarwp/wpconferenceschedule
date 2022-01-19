<?php
/**
 * The template for displaying the single session posts
 *
 * @package wp_conference_schedule
 * @since 1.0.0
 */

get_header(); ?>

	<section id="primary" class="content-area">
		<main id="main" class="site-main">

			<?php
			/* Start the Loop */
			while ( have_posts() ) : the_post(); 
				$time_format      = get_option( 'time_format', 'g:i a' );
				$post             = get_post();
				$session_time     = absint( get_post_meta( $post->ID, '_wpcs_session_time', true ) );
				$session_end_time = absint( get_post_meta( $post->ID, '_wpcs_session_end_time', true ) );
				$session_date     = ( $session_time ) ? date( 'F j, Y', $session_time ) : date( 'F j, Y' );
				$session_type     = get_post_meta( $post->ID, '_wpcs_session_type', true );
				$session_speakers_text = get_post_meta( $post->ID, '_wpcs_session_speakers',  true );
				$session_speakers_html = ($session_speakers_text ) ? '<div class="wpsc-single-session-speakers"><strong>Speaker:</strong> '.$session_speakers_text .'</div>' : null;
				$session_speakers = apply_filters( 'wpcs_filter_single_session_speakers', $session_speakers_html, $post->ID);
				?>

				<article id="post-<?php the_ID(); ?>" <?php post_class('wpsc-single-session'); ?>>

					<header class="entry-header">

						<?php the_title( '<h1 class="entry-title wpsc-single-session-title">', '</h1>' ); ?>

						<?php
						// Check if end time is available. This is for pre version 1.0.1 as the end time wasn't available.
						if($session_date && !$session_end_time)
							echo '<h2 class="wpsc-single-session-time"> '.$session_date.' at '.date($time_format, $session_time).'</h2>';

						if($session_date && $session_end_time)
							echo '<h2 class="wpsc-single-session-time"> '.$session_date.' from '.date($time_format, $session_time).' to '.date($time_format, $session_end_time).'</h2>';
						?>
						
						<div class="entry-meta wpsc-single-session-meta">
							<ul class="wpsc-single-session-taxonomies">
								<?php 
								$terms = get_the_terms(get_the_ID(), 'wpcs_track');
								if ( !is_wp_error($terms)){
									$term_names = wp_list_pluck($terms, 'name'); 
									$terms = implode(", ", $term_names);
									if($terms)
										echo '<li class="wpsc-single-session-taxonomies-taxonomy wpsc-single-session-tracks"><i class="fas fa-columns"></i>'.$terms.'</li>';
								}

								$terms = get_the_terms(get_the_ID(), 'wpcs_location');
								if ( !is_wp_error($terms)){
									$term_names = wp_list_pluck($terms, 'name'); 
									$terms = implode(", ", $term_names);
									if($terms)
										echo '<li class="wpsc-single-session-taxonomies-taxonomy  wpsc-single-session-location"><i class="fas fa-map-marker-alt"></i>'.$terms.'</li>';
								}

								do_action('wpsc_single_taxonomies');
								
								?>
							</ul>

						</div><!-- .meta-info -->

						<?php echo $session_speakers; ?>

					</header>
			
					<div class="entry-content">
						<?php the_content();?>
					</div><!-- .entry-content -->
					
					<?php if(get_option('wpcs_field_schedule_page_url')){ ?>
						<footer class="entry-footer">	
							<p><a href="<?php echo get_option('wpcs_field_schedule_page_url'); ?>">Return to Schedule</a></p>
						</footer>
					<?php } ?>

				</article><!-- #post-${ID} -->

				<?php

			endwhile; // End of the loop.
			?>

		</main><!-- #main -->
	</section><!-- #primary -->

<?php get_footer();