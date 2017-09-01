<?php
/**
 * The Header for our theme.
 *
 * Displays all of the <head> section and everything up till <div id="main">
 *
 * @package WordPress
 * @subpackage Twenty_Eleven
 * @since Twenty Eleven 1.0
 */
?><!DOCTYPE html>
<!--[if IE 6]>
<html id="ie6" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 7]>
<html id="ie7" <?php language_attributes(); ?>>
<![endif]-->
<!--[if IE 8]>
<html id="ie8" <?php language_attributes(); ?>>
<![endif]-->
<!--[if !(IE 6) | !(IE 7) | !(IE 8)  ]><!-->
<html <?php language_attributes(); ?>>
<!--<![endif]-->
<head>
<meta charset="<?php bloginfo( 'charset' ); ?>" />
<meta name="viewport" content="width=device-width" />
<link rel="icon" href="http://www.alldaychemist.com/skin/frontend/default/alldaychemist/favicon.ico" type="image/x-icon" />
<link rel="shortcut icon" href="http://www.alldaychemist.com/skin/frontend/default/alldaychemist/favicon.ico" type="image/x-icon" />
<title><?php
	/*
	 * Print the <title> tag based on what is being viewed.
	 */
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name.
	bloginfo( 'name' );

	// Add the blog description for the home/front page.
	$site_description = get_bloginfo( 'description', 'display' );
	if ( $site_description && ( is_home() || is_front_page() ) )
		echo " | $site_description";

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 )
		echo ' | ' . sprintf( __( 'Page %s', 'twentyeleven' ), max( $paged, $page ) );

	?></title>
<link rel="profile" href="http://gmpg.org/xfn/11" />
<link rel="stylesheet" type="text/css" media="all" href="<?php bloginfo( 'stylesheet_url' ); ?>" />
<link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>" />
<!--[if lt IE 9]>
<script src="<?php echo get_template_directory_uri(); ?>/js/html5.js" type="text/javascript"></script>
<![endif]-->
<?php
	/* We add some JavaScript to pages with the comment form
	 * to support sites with threaded comments (when in use).
	 */
	if ( is_singular() && get_option( 'thread_comments' ) )
		wp_enqueue_script( 'comment-reply' );

	/* Always have wp_head() just before the closing </head>
	 * tag of your theme, or you will break many plugins, which
	 * generally use this hook to add elements to <head> such
	 * as styles, scripts, and meta tags.
	 */
	wp_head();
?>
<link rel="stylesheet" type="text/css" media="all" href="<?php echo site_url(); ?>/wp-content/themes/alldaychemist/custom.css" />

</head>

<body <?php body_class(); ?>>
<div id="page" class="hfeed">
	<header role="banner">
		<div id="custom_head">
			<hgroup>
				<div class="hg_top">
					<div id="logo"><a href="/"></a></div>
					<div class="social_media_links_in_the_header" style="float:left;">
						<span style="font-size: 12px;margin-left: 3px;">STAY CONNECTED WITH US: </span>
						<a class="target_blank" href="http://www.facebook.com/alldaychemist2"><img width='25px' height='25px' src="http://adc.iksulabeta.com/skin/frontend/default/alldaychemist/images/social_images/facebook.png" alt="Facebook"></a>
						<a class="target_blank" href="http://www.twitter.com/alldaychemist"><img width='25px' height='25px' class="imgsize" src="http://adc.iksulabeta.com/skin/frontend/default/alldaychemist/images/social_images/twitter.png" alt="Twitter"></a>
						<a class="target_blank" href="http://pinterest.com/alldaychemist"><img width='25px' height='25px' class="imgsize" src="http://adc.iksulabeta.com/skin/frontend/default/alldaychemist/images/social_images/pinterest.png" alt="Pinterest"></a>
						<a class="target_blank" href="http://www.youtube.com/user/alldaychemist03"><img width='25px' height='25px' class="imgsize" src="http://adc.iksulabeta.com/skin/frontend/default/alldaychemist/images/social_images/youtube.png" alt="YouTube"></a>
						<a class="target_blank" href="http://adc.iksulabeta.com/sitemap.xml"><img width='25px' height='25px' class="imgsize" src="http://adc.iksulabeta.com/skin/frontend/default/alldaychemist/images/social_images/rss.png" alt="RSS Feed"></a>
					</div>
				</div>
				<div class="hg_bot">
					<div>
						<p class="ptag">Toll Free Tel +1(855)840-0584 &amp; Internationl Tel +1(213)2912588 &amp; Fax +1(760)284-5903</p>
						<p class="ptag">Calling Hours 9:00 AM to 5 AM (PST)</p>
					</div>
					<div id="top_links">
						<?php $siteUrl = site_url()."/.."; ?>
						<ul class="links">
							<li class="first">
								<a href="<?php echo $siteUrl; ?>/customer/account/" title="My Account">
								<span>My Account</span>
								</a>
							</li>
							<li>
								<a href="<?php echo $siteUrl; ?>/Article/" title="Article" class="top-link-news">
								<span>Article</span>
								</a>
							</li>
							<li>
								<a href="<?php echo $siteUrl; ?>/wishlist/" title="My Wishlist"><span>My Wishlist</span></a>
							</li>
							<li>
								<a href="<?php echo $siteUrl; ?>/checkout/cart/" title="My Cart" class="top-link-cart">
								<span>My Cart</span>
								</a>
							</li>
							<li>
								<a href="<?php echo $siteUrl; ?>/checkout/" title="Checkout" class="top-link-checkout">
								<span>Checkout</span>
								</a>
							</li>
							<li>
								<a href="<?php echo $siteUrl; ?>/testimonial" title="Testimonial">
								<span>Testimonial</span>
								</a>
							</li>
							<li class=" last">
								<a href="<?php echo $siteUrl; ?>/customer/account/login/" title="Log In">
								<span>Log In</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</hgroup>

			<?php
				// Check to see if the header image has been removed
				$header_image = get_header_image();
				if ( $header_image ) :
					// Compatibility with versions of WordPress prior to 3.4.
					if ( function_exists( 'get_custom_header' ) ) {
						// We need to figure out what the minimum width should be for our featured image.
						// This result would be the suggested width if the theme were to implement flexible widths.
						$header_image_width = get_theme_support( 'custom-header', 'width' );
					} else {
						$header_image_width = HEADER_IMAGE_WIDTH;
					}
					?>
			<a href="<?php echo esc_url( home_url( '/' ) ); ?>">
				<?php
					// The header image
					// Check if this is a post or page, if it has a thumbnail, and if it's a big one
					if ( is_singular() && has_post_thumbnail( $post->ID ) &&
							( /* $src, $width, $height */ $image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), array( $header_image_width, $header_image_width ) ) ) &&
							$image[1] >= $header_image_width ) :
						// Houston, we have a new header image!
						echo get_the_post_thumbnail( $post->ID, 'post-thumbnail' );
					else :
						// Compatibility with versions of WordPress prior to 3.4.
						if ( function_exists( 'get_custom_header' ) ) {
							$header_image_width  = get_custom_header()->width;
							$header_image_height = get_custom_header()->height;
						} else {
							$header_image_width  = HEADER_IMAGE_WIDTH;
							$header_image_height = HEADER_IMAGE_HEIGHT;
						}
						?>
					<img src="<?php header_image(); ?>" width="<?php echo $header_image_width; ?>" height="<?php echo $header_image_height; ?>" alt="" />
				<?php endif; // end check for featured image or standard header ?>
			</a>
			<?php endif; // end check for removed header image ?>

			<?php
				// Has the text been hidden?
				if ( 'blank' == get_header_textcolor() ) :
			?>
				<div class="only-search<?php if ( $header_image ) : ?> with-image<?php endif; ?>">
				<?php get_search_form(); ?>
				</div>
			<?php
				else :
			?>
				<?php get_search_form(); ?>
			<?php endif; ?>
		</div>
			<div id="menu_container">
				<nav id="access" role="navigation">
					<!--h3 class="assistive-text"><?php _e( 'Main menu', 'twentyeleven' ); ?></h3-->
					<?php /* Allow screen readers / text browsers to skip the navigation menu and get right to the good stuff. */ ?>
					<div class="skip-link"><a class="assistive-text" href="#content" title="<?php esc_attr_e( 'Skip to primary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to primary content', 'twentyeleven' ); ?></a></div>
					<div class="skip-link"><a class="assistive-text" href="#secondary" title="<?php esc_attr_e( 'Skip to secondary content', 'twentyeleven' ); ?>"><?php _e( 'Skip to secondary content', 'twentyeleven' ); ?></a></div>
					<?php /* Our navigation menu. If one isn't filled out, wp_nav_menu falls back to wp_page_menu. The menu assigned to the primary location is the one used. If one isn't assigned, the menu with the lowest ID is used. */ ?>
					<?php wp_nav_menu( array( 'theme_location' => 'primary' ) ); ?>
				</nav><!-- #access -->
			</div>
	</header><!-- #branding -->
	
	
	<div class="adc_header_part">
			<div class="div_child">
				<h1 class="entry-title new-entry-title"><?php echo get_bloginfo('name'); ?></h1>
			</div>
		</div>
	<div id="main">

