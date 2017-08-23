<?php
/**
 * The main template file.
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 * Learn more: http://codex.wordpress.org/Template_Hierarchy
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 */

get_header(); ?>
	
		<div id="primary">
			<div id="content" role="main">
				<?php //if (function_exists('rps_show')) echo rps_show(); ?>
				
			<?php if ( have_posts() ) { ?>

				<?php twentyeleven_content_nav( 'nav-above' ); ?>
				<?php
					if( function_exists('FA_display_slider') ){
					    FA_display_slider(1149);
					}
				?>
				<?php /* Start the Loop */ 
					$cats = array(16, 46, 137, 449, 450);
					foreach($cats as $cat) { 
					$my_query = new WP_Query('cat='.$cat.'&showposts=2');
					$query = json_decode(json_encode($my_query), true);
					if($query['post_count'])
					{
					//exit;
					echo "<a href='".get_category_link($cat)."'><h2 class='category_title'>".get_cat_name($cat)."</h2></a>";
				?>
				<?php while ( $my_query->have_posts() ) : $my_query->the_post(); ?>

					<?php get_template_part( 'content', get_post_format() ); ?>

				<?php endwhile; ?>

				<?php // twentyeleven_content_nav( 'nav-below' ); ?>
				<?php 	}  
					}
		 		} 

			 		else { ?>

				<article id="post-0" class="post no-results not-found">
					<header class="entry-header">
						<h1 class="entry-title"><?php _e( 'Nothing Found', 'twentyeleven' ); ?></h1>
					</header><!-- .entry-header -->

					<div class="entry-content">
						<p><?php _e( 'Apologies, but no results were found for the requested archive. Perhaps searching will help find a related post.', 'twentyeleven' ); ?></p>
						<?php get_search_form(); ?>
					</div><!-- .entry-content -->
				</article><!-- #post-0 -->

			<?php } ?>

			</div><!-- #content -->
		</div><!-- #primary -->

<?php get_sidebar(); ?>
<?php get_footer(); ?>