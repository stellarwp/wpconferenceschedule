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
			while ( have_posts() ) : the_post(); ?>

				<article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>

					<header class="entry-header">

						<?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>

						<div class="entry-meta">
							<?php 

							$time_format      = get_option( 'time_format', 'g:i a' );
							$post             = get_post();
							$session_time     = absint( get_post_meta( $post->ID, '_wpcs_session_time', true ) );
							$session_end_time = absint( get_post_meta( $post->ID, '_wpcs_session_end_time', true ) );
							$session_date     = ( $session_time ) ? date( 'F j, Y', $session_time ) : date( 'F j, Y' );
							$session_type     = get_post_meta( $post->ID, '_wpcs_session_type', true );
							$session_speakers = get_post_meta( $post->ID, '_wpcs_session_speakers',  true );

							// Check if end time is available. This is for pre version 1.0.1 as the end time wasn't available.
							if($session_date && !$session_end_time)
								echo '<h2> '.$session_date.' at '.date($time_format, $session_time).'</h2>';

							if($session_date && $session_end_time)
								echo '<h2> '.$session_date.' from '.date($time_format, $session_time).' to '.date($time_format, $session_end_time).'</h2>';

							//get_the_term_list( $post->ID, 'wpcs_track', '<strong>Track:</strong> ', ', ', '<br />');
							$tracks = get_the_term_list( $post->ID, 'wpcs_track', '', ', ', '');
							if($tracks){
								echo '<strong>Track: </strong>'.strip_tags($tracks).'<br />';
							}
							
							//get_the_term_list( $post->ID, 'wpcs_location', '<strong>Location:</strong> ', ', ', '<br />');
							$locations = get_the_term_list( $post->ID, 'wpcs_location', '<strong>Location:</strong> ', ', ', '<br />');
							if($locations){
								echo $locations;
							}

							if($session_speakers)
								echo '<strong>Speaker:</strong> '.$session_speakers.'<br />';
							?>
						</div><!-- .meta-info -->
						
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