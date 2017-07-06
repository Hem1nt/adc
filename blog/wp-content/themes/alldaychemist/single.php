<?php
$full_path = getcwd();
$ar = explode("wp-", $full_path);
$dirPath = $ar[0];
$postId =  get_the_ID();
if(isset($_COOKIE['rvpo'])){
	$postIdsArr = json_decode($_COOKIE['rvpo']);
	if(!in_array($postId, $postIdsArr)){
		array_push($postIdsArr, $postId);
		$postIdsArrNew = json_encode($postIdsArr);
		setcookie("rvpo", $postIdsArrNew, time()+3600*24,'/');
	}
}else{
	$postIdsArrMega = array();
	array_push($postIdsArrMega, $postId);
	$postIdsArrNewMega = json_encode($postIdsArrMega);
	setcookie("rvpo", $postIdsArrNewMega, time()+3600*24,'/');
}
if(isset($_COOKIE['rvpo'])){
	$recentViewPost = json_decode($_COOKIE['rvpo']);
	if($recentViewPost[0] == 0){
		array_shift($recentViewPost);
	}
	if(($key = array_search($postId, $recentViewPost)) !== false) {
    	unset($recentViewPost[$key]);
	}
	array_values($recentViewPost);
}
$mainArr = array_reverse($recentViewPost);

$_SESSION['rvpo'] = $mainArr;

/**
 * The Template for displaying all single posts.
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */

get_header(); ?>

		<div id="primary" class="single_post_page">
			<div id="content" role="main">
				<?php while ( have_posts() ) : the_post(); ?>
				<div class="adc_header_part">
			<div class="div_child">
				<div class="entry-meta">
					<!-- <div class="al_rt"><?php allday_posted_on(); ?></div> -->
					<div class="title_main">
						<h1 class="entry-title"><?php the_title(); ?></h1>
						<div class="author_div"><?php allday_author(); ?></div>
						<div class="addthis_toolbox addthis_default_style ">
							<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
							<a class="addthis_button_tweet"></a>
							<a class="addthis_button_pinterest_pinit"></a>
							<a class="addthis_counter addthis_pill_style"></a>
							<div class="bloggoto" title="Go for Comment">
								<div class="image"></div>
								<div class="count"></div>
							</div>
						</div>
					</div>

				</div><!-- .entry-meta -->
				
				<!-- AddThis Button BEGIN -->
					

					<div class="comment_post_date">
						<div class="al_lt comment_box"><?php allday_comments_count(); ?></div>
						<div class="al_rt post_date"><?php allday_posted_on(); ?></div>
					</div>
					
					<!--script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=nsparekh"></script-->
			</div>
		</div>
					
					<?php get_template_part( 'content-single', get_post_format() ); ?>
					<?php
						$postId =  get_the_ID();

					?>
					<div class="addthis_toolbox addthis_default_style ">
						<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
						<a class="addthis_button_tweet"></a>
						<a class="addthis_button_pinterest_pinit"></a>
						<a class="addthis_counter addthis_pill_style"></a>
					</div>

					<?php comments_template( '', true ); ?><?php // uncomment this line for  ?>

				<?php endwhile; // end of the loop. ?>

			</div><!-- #content -->
		</div><!-- #primary -->
<?php get_sidebar(); ?>
<?php get_footer(); ?>
<script>
	var length = jQuery(".commentlist li").length;
	jQuery(".bloggoto .count").text(length);
	jQuery(".bloggoto").click(function() {
	  jQuery("html, body").animate({ scrollTop: jQuery(document).height() }, 1000);
	});

	jQuery(".alignright").each(function(){
		var closest_parent = jQuery(this).closest("p");
		jQuery(closest_parent).addClass("right_sty");
		var closest_parent1 = jQuery(this).closest("li");
		jQuery(closest_parent1).addClass("right_sty");
		var closest_parent2 = jQuery(this).closest("h1");
		jQuery(closest_parent2).addClass("right_sty");
	});

	jQuery(".alignleft,.aligncenter").each(function(){
		var closest_parent = jQuery(this).closest("p");
		jQuery(closest_parent).addClass("left_sty");
		var closest_parent1 = jQuery(this).closest("li");
		jQuery(closest_parent1).addClass("left_sty");
		var closest_parent2 = jQuery(this).closest("h1");
		jQuery(closest_parent2).addClass("left_sty");
	});

</script>