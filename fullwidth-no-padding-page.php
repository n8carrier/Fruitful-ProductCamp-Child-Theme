<?php
/**
 * Template Name: Full width Template No Padding
 *
 * This template is the same as the full width template,
 * but uses the the no-entry-content-padding class in the content div.
 * The class is given padding: 0 and margin: 0 in style.css.
 *
 * @package WordPress
 * @subpackage Fruitful theme
 * @since Fruitful child theme 0.1
 */


get_header(); ?>
	<div id="primary" class="content-area">
		<div id="content" class="site-content no-entry-content-padding" role="main">
			<?php while ( have_posts() ) : the_post(); ?>
				<?php get_template_part( 'content', 'page' ); ?>
				<?php if (fruitful_state_page_comment()) { comments_template( '', true ); } ?>
			<?php endwhile; // end of the loop. ?>
		</div><!-- #content .site-content -->
	</div><!-- #primary .content-area -->
<?php get_footer(); ?>