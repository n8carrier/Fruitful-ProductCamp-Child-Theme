<?php
/**
 * Changes to original include:
 *   *Removal of Post Date
 *   *Entry-info table with session time, session room, and leader snippet
 *   *Truncated lengthy posts with a read more button (uses jQuery to show full post in the page)
 *   *Link to attachments at end of post (list of links)
 *
 * @package WordPress
 * @subpackage Fruitful theme
 * @since Fruitful child theme 0.1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class('blog_post'); ?>>
	<?php $day 		 = get_the_date('d'); 
		  $month_abr = get_the_date('M');
	?>
	<?php if (false) : /* Remove Post Date, but leave code in for reference */ ?>
		<?php if (get_the_title() == '') : ?>
			<a href="<?php the_permalink(); ?>" rel="bookmark">
		<?php endif; ?>	
		
		<div class="date_of_post">
			<span class="day_post"><?php print $day; ?></span>
			<span class="month_post"><?php print $month_abr; ?></span>
		</div>
		<?php if (get_the_title() == '') : ?>
			</a>
		<?php endif; ?>
	<?php endif; ?>
	
	<div class="post-content">	
	<header class="post-header">
		<?php if ( is_single() ) : ?>
				<h1 class="post-title"><?php the_title(); ?></h1>
		<?php else : ?>
			<?php if (get_the_title() != '') : ?>
			<h1 class="post-title">
				<a href="<?php the_permalink(); ?>" title="<?php echo esc_attr( sprintf( __( 'Permalink to %s', 'fruitful' ), the_title_attribute( 'echo=0' ) ) ); ?>" rel="bookmark"><?php the_title(); ?></a>
			</h1>
			<?php endif; ?>
		<?php endif; // is_single() ?>		
		
		
		<?php if ( !is_single() ) : ?>
			<?php if ( has_post_thumbnail() && ! post_password_required() ) : ?>
				<div class="entry-thumbnail">
					<?php the_post_thumbnail(); ?>
				</div>
			<?php endif; ?>
		<?php endif; // is_single() ?>
	</header><!-- .entry-header -->

	<?php
	// Get session time and room from categories
	// Uses child category of 'session-time' or session-room'
	// If more than one child category in place, will use last (not intended to have more than one)
	$categories = get_the_category();
	$session_time = 'TBD';
	$session_room = 'TBD';
	if($categories){
		foreach($categories as $category) {
			// Get parent
			$category_parent_id = $category->category_parent;
			if ( $category_parent_id != 0 ) {
				$category_parent = get_term( $category_parent_id, 'category' );
				if ($category_parent->slug == 'session-time') {
					$session_time = $category->name;
				} elseif ($category_parent->slug == 'session-room') {
					$session_room = $category->name;
				}
			}
		}
	}
	?>
	
	<?php
	// Create entry info table which displays session time, session room, and author bio snippet.
	?>
	<div class="entry-info">
		<table>
			<tbody>
				<tr>
					<td class="session-time">Time: <?php echo $session_time ?></td>
					<td class="session-avatar" rowspan="2">
						<?php echo get_avatar( get_the_author_meta( 'ID' ), 40 ); ?>
					</td>
					<td class="session-bio" rowspan="2">
						<?php if ( get_the_author_meta('description') ) : ?>
							<span class="bio-large">
								<?php echo substr(get_the_author_meta('description'),0,80)?>
							</span>
							<span class="bio-medium">
								<?php echo substr(get_the_author_meta('description'),0,55)?>
							</span>
							<span class="bio-small">
								<?php echo substr(get_the_author_meta('description'),0,30)?>
							</span>
							<?php /* Placing individual read more's in the above spans (or making them divs) messes things up really bad. So we have to do one, rather than have each separate to specify a different data-placement. */ ?>
							... <a href="#" class="full-bio" data-toggle="popover" data-placement="bottom" title="" data-content="<?php echo addcslashes(get_the_author_meta('description'), '"')?>" data-original-title="About <?php echo addcslashes(get_the_author_meta('display_name'), '"')?>">Read more &raquo;</a></td>
						<?php else : ?>
							<?php echo get_the_author_meta('display_name') . ' has not yet created a bio.';?>
						<?php endif; ?>
				</tr>
				<tr>
					<td class="session-room">Room: <?php echo $session_room ?></td>
				</tr>
				<tr>
					<td class="session-leader">Session Leader:&nbsp;
						<?php echo get_the_author_meta('display_name'); ?>
						<?php if ( get_the_author_meta('description') ) : ?>
							<a href="#" class="full-bio no-underline" data-toggle="popover" data-placement="bottom" title="" data-content="<?php echo addcslashes(get_the_author_meta('description'), '"')?>" data-original-title="About <?php echo addcslashes(get_the_author_meta('display_name'), '"')?>">
								<div class="dashicons dashicons-external"></div>
							</a>
						<?php endif; ?>
					</td>
				</td>
			</tbody>
		</table>
	</div><!-- .entry-info -->
	
	<?php if ( (is_search())) : // Only display Excerpts for Search ?>
	<div class="entry-summary">
		<?php the_excerpt(); ?>
	</div><!-- .entry-summary -->
	<?php else : ?>
		<div class="entry-content">
			<?php if ( strlen(get_the_content()) > 310) : // If entry is longer than 310 char, truncate to 300 and add read more button ?>
				<div id="entry-truncated-<?php the_ID(); ?>" class="entry-truncated">
					<?php echo substr(get_the_content(),0,300); ?>
					<span class="read-more">... <a href="#" id="read-more-<?php the_ID(); ?>" class="session-read-more">Read&nbsp;more&nbsp;&raquo;</a></span>
				</div>
				<div id="entry-full-<?php the_ID(); ?>" class="entry-full entry-hidden">
					<?php the_content(); ?>
					<span class="read-less"><a href="#" id="read-less-<?php the_ID(); ?>" class="session-read-less">&laquo;&nbsp;Read&nbsp;less</a></span>
				</div>
			<?php else: ?>
				<div id="entry-full-<?php the_ID(); ?>" class="entry-full">
					<?php the_content(); ?>
				</div>
			<?php endif; ?>
			<?php wp_link_pages( array( 'before' => '<div class="page-links">' . __( 'Pages:', 'fruitful' ), 'after' => '</div>' ) ); ?>
		</div><!-- .entry-content -->
		<div class="entry-attachments">
			<?php $attachments = new Attachments( 'attachments' ); // Load attachments as list of downloadable links ?>
			<?php if( $attachments->exist() ) : ?>
			  <ul class="attachments">
				<?php while( $attachments->get() ) : ?>
				  <li>
					<a href="<?php echo $attachments->url(); ?>" target="_blank">Download <?php echo $attachments->field( 'title' ); ?></a>
				  </li>
				<?php endwhile; ?>
			  </ul>
			<?php endif; ?>
		</div><!-- .entry-attachments -->
	<?php endif; ?>
	
	</div>
</article><!-- #post-<?php the_ID(); ?> -->
