<?php
// seo plus
global $post;
$seo_url_post = get_post_meta( $post->ID, 'seo_url_post', true );
$seo_desc_post = get_post_meta($post->ID, 'seo_desc_post_code', true );
$seo_keys_post = get_post_meta($post->ID,'seo_keys_post', true);
$news_keywords = get_post_meta( $post->ID, 'seo_news_keywords', true );
$seo_title_post = get_post_meta($post->ID,'seo_title_sp', true);
$seo_post_robots = get_post_meta($post->ID,'seo_post_robots', true);
$seo_home_title = get_option('seo_title_code');
$home_description = get_option('seo_desc_code');
$seo_robot_home_code = get_option('seo_robot_home_code');
// Meta creation
$seo_meta_auhor = get_option('seo_meta_author');
$seo_meta_contact = get_option('seo_meta_contact');
$seo_meta_copyright = get_option('seo_meta_copyright');
// Meta geo
$seo_geo_region = get_option('seo_geo_region');
$seo_geo_placename = get_option('seo_geo_placename');
$seo_geo_position = get_option('seo_geo_position');
$seo_ICBM = get_option('seo_ICBM');
// facebook
$seo_app_id = get_option('seo_app_id');
$seo_admins = get_option('seo_admins');
$display_og_url = get_option('display_og_url');
$display_og_site_name = get_option('display_og_site_name');
$display_og_description = get_option('display_og_description');
$display_og_type = get_option('display_og_type');
$og_image = get_option('display_og_image');
$home_url_logo = get_option('upload_image');
// facebook post
$display_og_url_post = get_option('display_og_url_post');
$display_og_title = get_option('display_og_title');
$display_og_description_post = get_option('display_og_description_post');
$display_og_type_post = get_option('display_og_type_post');
$display_og_image_post = get_option('display_og_image_post');
?>
<!-- Seo + -->

<?php 

 
// post
if (is_single() ) { 

if (empty($seo_title_post[0]) )
 {
echo '<title>' .get_the_title($ID).'</title>';
 } 
else {
echo '<title>' . $seo_title_post .'</title>';	 
 }
}
// page
elseif (is_page() ) {

if (empty($seo_title_post[0]) )
 {
echo '<title>' .get_the_title($ID).'</title>';
 } 
else {
	
echo '<title>' . $seo_title_post .'</title>';	
}
}?>

<?php
if (is_category() || is_archive() || is_search()) {
show_category_meta();
} 
?>
<?php // Page Accueil
if (is_home () ) 
{ ?>

<!-- Facebook Opengraph other -->
<?php if (empty($seo_app_id)){
   
} else {
    echo '<meta property="fb:app_id" content="'.$seo_app_id.'" />'."\r\n";
}
?>
<?php if (empty($seo_admins)){
   
} else {
    echo '<meta property="fb:admins" content="'.$seo_admins.'" />'."\r\n";
}
?>
<?php if ($display_og_url){
	
	 echo '<meta property="og:url" content="'.get_site_url().'"/>'."\r\n";
   
} else {
   
}
?>
<?php if ($display_og_site_name){ if (empty($seo_home_title)) { ?>
<meta property="og:site_name" content="<?php bloginfo('name'); ?>"/>
<?php 
} else { echo '<meta property="og:site_name" content="' . $seo_home_title .'"/>'."\r\n";}
   
} else { }
?>
<?php if ($display_og_description ){ if (empty($seo_home_title)){ ?>
<meta property="og:description" content="<?php bloginfo('description'); ?>"/>
<?php } else { echo '<meta property="og:description" content="' .$home_description.'"/>'."\r\n";} 

} else { } 
?>
<?php if ($display_og_type ){
echo '<meta property="og:type" content="website"/>';	
} else { }
?> 
<?php if ($og_image){ 
echo '<meta property="og:image" content="' .$home_url_logo.'"/>'."\r\n"; 
} else { } 
?>    
<!-- Facebook Opengraph other -->
<?php 
if (empty($seo_home_title)){
	echo '<title>';
	echo  bloginfo('name');
	echo '</title>'; 
}
echo '<title>' . $seo_home_title .'</title>'."\r\n";{ ?>
<?php } 

    // Meta homepage
?>
<?php if (empty($seo_robot_home_code)){
   
} else {
    echo '<meta name="robots" content="'.$seo_robot_home_code.'" />'."\r\n";
	}
?>
<?php if (empty($seo_meta_auhor)){
   
} else {
    echo '<meta name="author" content="'.$seo_meta_auhor.'" />'."\r\n";
	}
?>
<?php if (empty($seo_meta_contact)){
   
} else {
    echo '<meta name="contact" content="'.$seo_meta_contact.'" />'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="copyright" content="'.$seo_meta_copyright.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="geo.region" content="'.$seo_geo_region.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="geo.placename" content="'.$seo_geo_placename.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="geo.position" content="'.$seo_geo_position.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="ICBM" content="'.$seo_ICBM.'"/>'."\r\n";
	}
?>
<?php 
if (empty($home_description)){ ?>
<meta name="description" content="<?php bloginfo('description')?>"/>
	<?php } else {
	echo '<meta name="description" content="'.$home_description.'"/>'."\r\n";
	}
?>
<meta name="keywords" content="<?php echo get_option('seo_key_code'); ?>"/>
<meta name="news_keywords" content="<?php echo get_option('seo_key_news_keywords'); ?>"/>
<?php } ?>



<?php // Meta Articles et Pages
if ( is_page() || is_singular( 'post' ) ) {
	?>
<link rel="canonical" href="<?php echo $seo_url_post;?>" />
<!-- Facebook Opengraph post -->
<?php if (empty($seo_app_id)){
   
} else {
    echo '<meta property="fb:app_id" content="'.$seo_app_id.'" />'."\r\n";
}
?>
<?php if (empty($seo_admins)){
   
} else {
    echo '<meta property="fb:admins" content="'.$seo_admins.'" />'."\r\n";
}
?>
<?php if ($display_og_title){ ?>
<meta property="og:title" content="<?php single_post_title('');?>"/>
<?php
} else {}
?>
<?php if ($display_og_url_post){ ?>
<meta property="og:url" content="<?php the_permalink();?>"/>
<?php
} else{}
?>
<?php if ($display_og_description_post){ 
echo '<meta property="og:description" content='.$seo_desc_post.'" />'."\r\n";
if (empty($seo_desc_post[0]) )
 {

	echo '<meta property="og:description" content='.strip_tags(get_the_excerpt($post->ID)).'" />'."\r\n";
  
 } 
} else {}
?>
<?php if ($display_og_type_post){
	echo '<meta property="og:type" content="article"/>'."\r\n";
} else {}
?>
<?php if ($display_og_image_post){ ?>
<meta property="og:image" content="<?php echo wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );?>"/>
<?php
} else{}
?>
<!-- Facebook Opengraph post -->
<meta name="description" content="<?php 
echo $seo_desc_post;
if (empty($seo_desc_post[0]) )
 {
   echo strip_tags(get_the_excerpt($post->ID));
 } 
?>" />
<meta name="keywords" content="<?php
echo $seo_keys_post;
if (empty($seo_keys_post[0]) )
 {
    echo get_option('seo_key_code');
 }   
 ?>" /> 
<meta name="news_keywords" content="<?php // Meta news_keywords 2012 google
echo $news_keywords;
if (empty($news_keywords[0]) )
 {
    echo get_option('seo_key_news_keywords');
 }   
 ?>" />
<meta name="robots" content="<?php echo $seo_post_robots;?>" />
<?php	
	}

?>
<!-- Seo + fin -->
<?php
// seo
global $post;
$seo_url_post = get_post_meta( $post->ID, 'seo_url_post', true );
$seo_desc_post = get_post_meta($post->ID, 'seo_desc_post_code', true );
$seo_keys_post = get_post_meta($post->ID,'seo_keys_post', true);
$news_keywords = get_post_meta( $post->ID, 'seo_news_keywords', true );
$seo_title_post = get_post_meta($post->ID,'seo_title_sp', true);
$seo_post_robots = get_post_meta($post->ID,'seo_post_robots', true);
$seo_home_title = get_option('seo_title_code');
$home_description = get_option('seo_desc_code');
$seo_robot_home_code = get_option('seo_robot_home_code');
// Meta creation
$seo_meta_auhor = get_option('seo_meta_author');
$seo_meta_contact = get_option('seo_meta_contact');
$seo_meta_copyright = get_option('seo_meta_copyright');
// Meta geo
$seo_geo_region = get_option('seo_geo_region');
$seo_geo_placename = get_option('seo_geo_placename');
$seo_geo_position = get_option('seo_geo_position');
$seo_ICBM = get_option('seo_ICBM');
// facebook
$seo_app_id = get_option('seo_app_id');
$seo_admins = get_option('seo_admins');
$display_og_url = get_option('display_og_url');
$display_og_site_name = get_option('display_og_site_name');
$display_og_description = get_option('display_og_description');
$display_og_type = get_option('display_og_type');
$og_image = get_option('display_og_image');
$home_url_logo = get_option('upload_image');
// facebook post
$display_og_url_post = get_option('display_og_url_post');
$display_og_title = get_option('display_og_title');
$display_og_description_post = get_option('display_og_description_post');
$display_og_type_post = get_option('display_og_type_post');
$display_og_image_post = get_option('display_og_image_post');
?>
<!-- Seo + -->

<?php 

 
// post
if (is_single() ) { 

if (empty($seo_title_post[0]) )
 {
echo '<title>' .get_the_title($ID).'</title>';
 } 
else {
echo '<title>' . $seo_title_post .'</title>';	 
 }
}
// page
elseif (is_page() ) {

if (empty($seo_title_post[0]) )
 {
echo '<title>' .get_the_title($ID).'</title>';
 } 
else {
	
echo '<title>' . $seo_title_post .'</title>';	
}
}?>

<?php
if (is_category() || is_archive() || is_search()) {
show_category_meta();
} 
?>
<?php // Page Accueil
if (is_home () ) 
{ ?>

<!-- Facebook Opengraph other -->
<?php if (empty($seo_app_id)){
   
} else {
    echo '<meta property="fb:app_id" content="'.$seo_app_id.'" />'."\r\n";
}
?>
<?php if (empty($seo_admins)){
   
} else {
    echo '<meta property="fb:admins" content="'.$seo_admins.'" />'."\r\n";
}
?>
<?php if ($display_og_url){
	
	 echo '<meta property="og:url" content="'.get_site_url().'"/>'."\r\n";
   
} else {
   
}
?>
<?php if ($display_og_site_name){ if (empty($seo_home_title)) { ?>
<meta property="og:site_name" content="<?php bloginfo('name'); ?>"/>
<?php 
} else { echo '<meta property="og:site_name" content="' . $seo_home_title .'"/>'."\r\n";}
   
} else { }
?>
<?php if ($display_og_description ){ if (empty($seo_home_title)){ ?>
<meta property="og:description" content="<?php bloginfo('description'); ?>"/>
<?php } else { echo '<meta property="og:description" content="' .$home_description.'"/>'."\r\n";} 

} else { } 
?>
<?php if ($display_og_type ){
echo '<meta property="og:type" content="website"/>';	
} else { }
?> 
<?php if ($og_image){ 
echo '<meta property="og:image" content="' .$home_url_logo.'"/>'."\r\n"; 
} else { } 
?>    
<!-- Facebook Opengraph other -->
<?php 
if (empty($seo_home_title)){
	echo '<title>';
	echo  bloginfo('name');
	echo '</title>'; 
}
echo '<title>' . $seo_home_title .'</title>'."\r\n";{ ?>
<?php } 

    // Meta homepage
?>
<?php if (empty($seo_robot_home_code)){
   
} else {
    echo '<meta name="robots" content="'.$seo_robot_home_code.'" />'."\r\n";
	}
?>
<?php if (empty($seo_meta_auhor)){
   
} else {
    echo '<meta name="author" content="'.$seo_meta_auhor.'" />'."\r\n";
	}
?>
<?php if (empty($seo_meta_contact)){
   
} else {
    echo '<meta name="contact" content="'.$seo_meta_contact.'" />'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="copyright" content="'.$seo_meta_copyright.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="geo.region" content="'.$seo_geo_region.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="geo.placename" content="'.$seo_geo_placename.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="geo.position" content="'.$seo_geo_position.'"/>'."\r\n";
	}
?>
<?php if (empty($seo_meta_copyright)){
   
} else {
    echo '<meta name="ICBM" content="'.$seo_ICBM.'"/>'."\r\n";
	}
?>
<?php 
if (empty($home_description)){ ?>
<meta name="description" content="<?php bloginfo('description')?>"/>
	<?php } else {
	echo '<meta name="description" content="'.$home_description.'"/>'."\r\n";
	}
?>
<meta name="keywords" content="<?php echo get_option('seo_key_code'); ?>"/>
<meta name="news_keywords" content="<?php echo get_option('seo_key_news_keywords'); ?>"/>
<?php } ?>



<?php // Meta Articles et Pages
if ( is_page() || is_singular( 'post' ) ) {
	?>
<link rel="canonical" href="<?php echo $seo_url_post;?>" />
<!-- Facebook Opengraph post -->
<?php if (empty($seo_app_id)){
   
} else {
    echo '<meta property="fb:app_id" content="'.$seo_app_id.'" />'."\r\n";
}
?>
<?php if (empty($seo_admins)){
   
} else {
    echo '<meta property="fb:admins" content="'.$seo_admins.'" />'."\r\n";
}
?>
<?php if ($display_og_title){ ?>
<meta property="og:title" content="<?php single_post_title('');?>"/>
<?php
} else {}
?>
<?php if ($display_og_url_post){ ?>
<meta property="og:url" content="<?php the_permalink();?>"/>
<?php
} else{}
?>
<?php if ($display_og_description_post){ ?>
<meta property="og:description" content="<?php echo $seo_desc_post;?>"/>
<?php
if (empty($seo_desc_post[0]) )
 {
   echo '<meta property="og:description" content="'.strip_tags(get_the_excerpt($post->ID)).'">';
 } 
} else {}
?>
<?php if ($display_og_type_post){
	echo '<meta property="og:type" content="article"/>'."\r\n";
} else {}
?>
<?php if ($display_og_image_post){ ?>
<meta property="og:image" content="<?php echo wp_get_attachment_thumb_url( get_post_thumbnail_id( $post->ID ) );?>"/>
<?php
} else{}
?>
<!-- Facebook Opengraph post -->
<meta name="description" content="<?php 
echo $seo_desc_post;
if (empty($seo_desc_post[0]) )
 {
   echo strip_tags(get_the_excerpt($post->ID));
 } 
?>" />
<meta name="keywords" content="<?php
echo $seo_keys_post;
if (empty($seo_keys_post[0]) )
 {
    echo get_option('seo_key_code');
 }   
 ?>" /> 
<meta name="news_keywords" content="<?php // Meta news_keywords 2012 google
echo $news_keywords;
if (empty($news_keywords[0]) )
 {
    echo get_option('seo_key_news_keywords');
 }   
 ?>" />
<meta name="robots" content="<?php echo $seo_post_robots;?>" />
<?php	
	}
// Google Analytics
add_action('wp_footer', 'add_googleanalytics');
function add_googleanalytics() { ?>
<!-- google analystics -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
try{
var pageTracker = _gat._getTracker("<?php echo get_option('seo_tracking_code'); ?>");
pageTracker._trackPageview();
} catch(err) {}
</script> 
<!-- google analystics Fin -->
<?php } ?>
<!-- Seo + fin -->