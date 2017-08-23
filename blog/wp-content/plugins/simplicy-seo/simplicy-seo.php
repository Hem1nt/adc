<?php
/*
Plugin Name: Simplicy SEO
Plugin URI: http://www.naxialis.com/
Description: Plugins SEO solution for WordPress, including on-page and post content analytics, Facebook open graph .
Version: 1.0.4
Author: Fred
Author URI: http://www.naxialis.com/
License: GPL v3

Simplicy SEO Plugin
Copyright (C) 2008-2014, Naxialis - fred@naxialis.com

This program is free software: you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation, either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/
define('SEO_BASENAME', plugin_basename(__FILE__));
define('SEO_DIR_URL', plugins_url('', SEO_BASENAME));
// create custom plugin settings menu
add_action('admin_menu', 'simplicy_seo_create_menu');
// seo plus script

function pw_load_scripts() {
	wp_enqueue_script("jquery");
	wp_enqueue_script( 'count_script', SEO_DIR_URL. '/js/count_script.js' , dirname(__FILE__) );
	wp_enqueue_script( 'reveal', SEO_DIR_URL. '/js/reveal.js' , dirname(__FILE__) );
	wp_enqueue_script( 'preview_seo', SEO_DIR_URL. '/js/preview_seo.js' , dirname(__FILE__) );
	wp_enqueue_style( 'sp_seo', SEO_DIR_URL. '/css/sp_seo.css' , dirname(__FILE__) );
}
add_action('admin_enqueue_scripts', 'pw_load_scripts');

// seo post fonction include

require_once(dirname(__FILE__).'/inc/post_options-seo.php');
require_once(dirname(__FILE__).'/inc/seo_category.php');

// head include seo
function simplicy_seo_head () {
require_once(dirname(__FILE__).'/inc/seo.php');
}
add_action('wp_head', 'simplicy_seo_head');
// début de la fonction
function simplicy_seo_create_menu() {
	add_menu_page('SEO Pro', 'Simplicy SEO', 'administrator', __FILE__, 'seo_settings_page', plugins_url('simplicy-seo/images/seo-icon.png'));
	add_submenu_page( __FILE__,'Submenu Page Title','Page accueil', 'administrator',__FILE__, 'seo_settings_page');
	add_submenu_page( __FILE__,'Avanc&eacute;','Avanc&eacute;', 'administrator', 'simplicy_seo', 'seo_advanced_page');
	//call register settings function
	add_action( 'admin_init', 'register_simplicy_seo_settings' );
}



function register_simplicy_seo_settings() {
	//Google 
	register_setting( 'seo-settings-group', 'seo_tracking_code' );
	// Titre
	register_setting( 'seo-settings-group', 'seo_title_code' );
	// Meta robots home
	register_setting( 'seo-settings-group', 'seo_robot_home_code' );
	// Mots clés
	register_setting( 'seo-settings-group', 'seo_key_code' );
	// Mots clés news
	register_setting( 'seo-settings-group', 'seo_key_news_keywords' );
	// Description
	register_setting( 'seo-settings-group', 'seo_desc_code' );
	// Meta Author
	register_setting( 'seo-settings-meta', 'seo_meta_author' );
	// Meta contact
	register_setting( 'seo-settings-meta', 'seo_meta_contact' );
	// Meta geo.region
	register_setting( 'seo-settings-meta', 'seo_geo_region' );
	// Meta geo.placename
	register_setting( 'seo-settings-meta', 'seo_geo_placename' );
	// Meta geo.position
	register_setting( 'seo-settings-meta', 'seo_geo_position' );
	// Meta ICBM
	register_setting( 'seo-settings-meta', 'seo_ICBM' );
	// Meta copyright
	register_setting( 'seo-settings-meta', 'seo_meta_copyright' );
	// Facebook app id
	register_setting( 'seo-settings-meta', 'seo_app_id' );
	// Facebook admins
	register_setting( 'seo-settings-meta', 'seo_admins' );
	// Facebook og url
	register_setting( 'seo-settings-meta', 'display_og_url' );
	// Facebook og site_name
	register_setting( 'seo-settings-meta', 'display_og_site_name' );
	// Facebook og og:description
	register_setting( 'seo-settings-meta', 'display_og_description' );
	// Facebook og og:website
	register_setting( 'seo-settings-meta', 'display_og_type' );
	// Facebook og og:image
	register_setting( 'seo-settings-meta', 'display_og_image' );
	// Facebook og url image
	register_setting( 'seo-settings-meta', 'upload_image' );
	// Facebook og decription post
	register_setting( 'seo-settings-meta', 'display_og_description_post' );
	// Facebook og url post
	register_setting( 'seo-settings-meta', 'display_og_url_post' );
	// Facebook og title 
	register_setting( 'seo-settings-meta', ' display_og_title' );
	// Facebook og type post
	register_setting( 'seo-settings-meta', 'display_og_type_post' );
	// Facebook og type post
	register_setting( 'seo-settings-meta', ' display_og_image_post' );
	
}
function wp_gear_manager_admin_scripts() {
wp_enqueue_script('media-upload');
wp_enqueue_script('thickbox');
wp_enqueue_script('jquery');
}

function wp_gear_manager_admin_styles() {
wp_enqueue_style('thickbox');
}

add_action('admin_print_scripts', 'wp_gear_manager_admin_scripts');
add_action('admin_print_styles', 'wp_gear_manager_admin_styles');

function seo_settings_page() {
// infos blog
$seo_title = get_option('seo_title_code');
$site_title = get_bloginfo( 'name' );
$site_url = network_site_url( '/' );
$site_description = get_bloginfo( 'description' );
$seo_desc = get_option('seo_desc_code');
?>
<div class="wrap">

<h2>Simplicy SEO </h2>

<form method="post" action="options.php">

    <?php settings_fields('seo-settings-group'); ?>
    <table class="form-table">
		<tr valign="top">
        <th></th>
        <td style=" font-size:18px;"> <?php echo '<img src="' .plugins_url( 'images/statistics.png' , __FILE__ ). '" > '; ?>Google Analytics</td>
        </tr>
<!-- Google Analytics -->
        <tr valign="top">
        <th>Code de suivi Google Analytics</th>
        <td><input type="text" name="seo_tracking_code" style=" width:630px;" value="<?php echo get_option('seo_tracking_code'); ?>" size="80"/></td>
        </tr> 
<!-- meta robot home -->
        <tr valign="top">
        <th>Balise Meta robots </th>
        
        <td><input type="text" name="seo_robot_home_code" style=" width:630px;" value="<?php echo get_option('seo_robot_home_code'); ?>" size="80"/></br><small>index,follow par d&eacute;faut</small></td>
        </tr>    
<!-- Mot clés page accueil -->
		<tr valign="top">
        <th></th>
        <td style=" font-size:18px;"><?php echo '<img src="' .plugins_url( 'images/collaboration.png' , __FILE__ ). '" > '; ?>Seo Page Accueil</td>
        </tr>
		<th scope="row">Mots Cl&eacute;s :</br><small> Balise Meta keywords</small></th>
        <td><input type="text" name="seo_key_code" style=" width:630px;" value="<?php echo get_option('seo_key_code'); ?>" size="80"/></td>
        </tr>
        <th scope="row">Mots Cl&eacute;s :</br><small> Balise Meta news_keywords</small></th>
        <td><input type="text" name="seo_key_news_keywords" style=" width:630px;" value="<?php echo get_option('seo_key_news_keywords'); ?>" size="80"/></br><small>Les mots cl&eacute;s sont limit&eacute;s &agrave; 10 et doivent &ecirc;tre s&eacute;par&eacute;s par des virgules.<a href="http://support.google.com/news/publisher/bin/answer.py?hl=fr&answer=68297" target="_blank">En savoir plus</a></small></td>
        </tr>
        <th scope="row">Titre de votre page d'accueil :</th>
        <td><input type="text" name="seo_title_code" id="seo_title_code" style=" width:630px;" onKeyDown="showHTML()" onKeyPress="return charLimit(this)" onKeyUp="return characterCount(this)" value="<?php echo get_option('seo_title_code'); ?>" size="80"/>
        <br><p class="seo-infos"><span class="counter" id="charCount"></span> Caract&egrave;res restants. Id&eacute;al : <strong>60</strong> caract&egrave;res. Maximum : <strong>100</strong> caract&egrave;res.</p>
		<br>
        </td>
        </tr>
<!-- Mot description  page accueil -->
		<tr valign="top">
        <th>Description</th>
        <td><textarea name="seo_desc_code" id="seo_desc_code" style=" width:630px; height:100px;" onKeyDown="showHTML()" onKeyPress="return charLimit2(this)" onKeyUp="return characterCount2(this)"><?php echo get_option('seo_desc_code'); ?></textarea>
        <br><p class="seo-infos"><span class="counter" id="charCount2"></span> Caract&egrave;res restants. Id&eacute;al : <strong>156</strong> caract&egrave;res. Maximum : <strong>200</strong> caract&egrave;res.</p><br>
        </td>
        </tr>
        <tr valign="top">
        <th>Aper&ccedil;u</th>
        <td>
        <div id="preview">
		<div id="title-seo" name="title-seo">
		<?php echo $seo_title ;
		if (empty($seo_title[0]) )
		 {
    	echo $site_title ;
 		}
		?>
        </div>
        <div id="permalink-seo-post">
        <?php echo $site_url; ?> 
        </div>
        <div id="desc-seo" name="desc-seo">
        <?php echo $seo_desc ;
		if (empty($seo_desc[0]) )
		 {
    	echo $site_description ;
 		}
		 ?>
        </div>
        </div>
        </td>
        </tr>  
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

</form>
</div>

<?php }
// Advanded

require_once(dirname(__FILE__).'/inc/seo_advanced.php'); 
// Fonction
add_action( 'init', 'seo_add_excerpts_to_pages' );
    function seo_add_excerpts_to_pages() {
     add_post_type_support( 'page', 'excerpt' );
    }

function custom_seo_excerpt($content){
$limit = 156;
$content = strip_tags($content);
if (strlen($content) > $limit)
$content = substr($content, 0, strpos($content," ",$limit)) . '...';
return $content;
}

?>