<?php
// SEO Advanced

function seo_advanced_page() {
$options = get_option( 'seo_index_archives' );
?>
<script language="JavaScript">
jQuery(document).ready(function() {
jQuery('#upload_image_button').click(function() {
formfield = jQuery('#upload_image').attr('name');
tb_show('', 'media-upload.php?type=image&TB_iframe=true');
return false;
});

window.send_to_editor = function(html) {
imgurl = jQuery('img',html).attr('src');
jQuery('#upload_image').val(imgurl);
tb_remove();
}

});
</script>
<div class="wrap">

<h2>Param&egrave;tres Avanc&eacute;s</h2>
<h3>Balises Meta Suppl&eacute;mentaires</h3>
<p class="description">Pour ajouter les balises meta suppl&eacute;mentaires il vous suffit de compl&eacute;ter les champs et elles s&acute;afficheront automatiquement </p>
<form method="post" action="options.php">

    <?php settings_fields('seo-settings-meta'); ?>

<!-- Meta Création -->    

 <h2 style="float:left;"><?php echo '<img src="' .plugins_url( 'images/user.png' , __FILE__ ). '" > '; ?>Balise Meta cr&eacute;ation</h2><div class="td_help"><a href="#" class="big-link" data-reveal-id="meta_creation"><div class="seo_help"></div></a></div>
 
    <table class="form-table">
<!-- Meat Author -->
        <tr valign="top">
        <th>Utiliser la balise Meta author </th>
        <td><input type="text" name="seo_meta_author" style=" width:550px;" value="<?php echo get_option('seo_meta_author'); ?>" size="80"/></td>
        </tr>
<!-- Meat contact -->
        <tr valign="top">
        <th>Utiliser la balise Meta contact </th>
        <td><input type="text" name="seo_meta_contact" style=" width:550px;" value="<?php echo get_option('seo_meta_contact'); ?>" size="80"/></td>
        </tr>
<!-- Meat copyright -->
        <tr valign="top">
        <th>Utiliser la balise Meta copyright </th>
        <td><input type="text" name="seo_meta_copyright" style=" width:550px;" value="<?php echo get_option('seo_meta_copyright'); ?>" size="80"/></td>
        </tr>
     </table>
            
<!-- Meta geo positon -->

<h2 style="float:left;"><?php echo '<img src="' .plugins_url( 'images/globe.png' , __FILE__ ). '" > '; ?>Balise Meta GEOTAG</h2><div class="td_help"><a href="#" class="big-link" data-reveal-id="geo-tags"><div class="seo_help"></div></a></div>
	 <table class="form-table">
        <th>Utiliser la balise Meta geo.region </th>
        <td><input type="text" name="seo_geo_region" style=" width:550px;" value="<?php echo get_option('seo_geo_region'); ?>" size="80"/>
        </br><small>Entrez le code pays au format iso (exemple  FR-75)</small></td>
        </tr>
<!-- Meat contact -->
        <tr valign="top">
        <th>Utiliser la balise Meta geo.placename </th>
        <td><input type="text" name="seo_geo_placename" style=" width:550px;" value="<?php echo get_option('seo_geo_placename'); ?>" size="80"/>
        </br><small>Entrez la ville correspondante (exemple:  Paris)</small></td>
        </tr>
<!-- Meat copyright -->
        <tr valign="top">
        <th>Utiliser la balise Meta geo.position </th>
        <td><input type="text" name="seo_geo_position" style=" width:550px;" value="<?php echo get_option('seo_geo_position'); ?>" size="80"/>
        </br><small>latitude;longitude : La latitude est s&eacute;par&eacute;e de la longitude par un point-virgule (<strong>sans espace</strong>)</br> vous pouvez vous rendre sur Google Maps, renseignez le nom de votre ville, puis cliquez sur "obtenir l'URL de cette page"..</small></td>
        </tr> 
        <tr valign="top">
        <th>Utiliser la balise Meta ICBM </th>
        <td><input type="text" name="seo_ICBM" style=" width:550px;" value="<?php echo get_option('seo_ICBM'); ?>" size="80"/>
        </br><small>latitude, longitude : une syntaxe diff&eacute;rente (<strong>s&eacute;par&eacute;es par une virgule suivie d'un espace)</strong> et permet donc certains sites et outils de vous g&eacute;olocaliser</small></td>
        </tr>
        </table> 
        
        <!-- Facebook Open Graph  -->
        
        <h2 style="float:left;"><?php echo '<img src="' .plugins_url( 'images/fb.png' , __FILE__ ). '" > '; ?>Facebook Open Graph</h2><div class="td_help"><a href="#" class="big-link" data-reveal-id="fb-open-graph"><div class="seo_help"></div></a></div>
 
        <table class="form-table">
        <th><h4>Page accueil, pages, articles</h4></th>
        <tr>		
        <th>Utiliser la balise Meta property app_id </th>
        
        <td><input type="text" name="seo_app_id" style="width:550px;" value="<?php echo get_option('seo_app_id'); ?>" size="80"/></br><small>Identifiant de votre application</small>
        </td>
        
        </tr>
<!-- Meat admins-->
        <tr valign="top">
        <th>Utiliser la balise Meta property admins </th>
        <td><input type="text" name="seo_admins" style=" width:550px;" value="<?php echo get_option('seo_admins'); ?>" size="80"/></br><small>Identifiant de votre profil Facebook</small></td>
        </tr>
<!-- Meat url -->
<th><h4>Page accueil</h4></th>
        <tr valign="top">
        <th>Utiliser la balise Meta property og:url </th>
        
        <td>
        	<input name="display_og_url" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_url' ) ); ?> /> Cochez pour utiliser la balise meta property og:url 
        	</br><small>Cette balise indiquera l'url de votre page d'accueil</small></td>
        </tr>
        <tr valign="top">
        <th>Utiliser la balise Meta property og:site_name </th>
        
        <td>
        	<input name="display_og_site_name" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_site_name' ) ); ?> /> Cochez pour utiliser la balise meta property og:site_name 
        	</br><small>Cette balise indiquera le nom de votre site</small></td>
        </tr>
        <tr valign="top">
        <th>Utiliser la balise Meta property og:description </th>
        
        <td>
        	<input name="display_og_description" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_description' ) ); ?> /> Cochez pour utiliser la balise meta property og:description 
        	</br><small>Cette balise indiquera la description de votre site</small></td>
        </tr>    
         <tr valign="top">
        <th>Utiliser la balise Meta property og:type </th>
        
        <td>
        	<input name="display_og_type" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_type' ) ); ?> /> Cochez pour utiliser la balise meta property og:type 
        	</br><small>Cette balise indiquera le type de contenu : website</small></td>
        </tr>
        <tr valign="top">
        <th>Utiliser la balise Meta property og:image </th>
        
        <td>
        	<input name="display_og_image" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_image' ) ); ?> /> Cochez pour utiliser la balise meta property og:image 
        	</br><small>Cette indiquera le logo de votre site comme image par d&eacute;faut</small></br>
           
		
        <input id="upload_image" type="text" size="36" name="upload_image" value="<?php echo get_option('upload_image'); ?>" />
		<input id="upload_image_button" type="button" value="Upload Image" />
		<br /><small>Ici indiquez l&acute;url de votre logo </small>
            </td>
        </tr>
        <th><h4>Articles</h4></th>
            <tr valign="top">
        <th>Utiliser la balise Meta property og:url </th>
        
        <td>
        	<input name="display_og_url_post" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_url_post' ) ); ?> /> Cochez pour utiliser la balise meta property og:url 
        	</br><small>Cette indiquera l'url de l&acute;article</small></td>
        </tr>
        <tr valign="top">
        <th>Utiliser la balise Meta property og:title </th>
        
        <td>
        	<input name="display_og_title" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_title' ) ); ?> /> Cochez pour utiliser la balise meta property og:site_name 
        	</br><small>Cette indiquera le titre de l&acute;article</small></td>
        </tr>
        <tr valign="top">
        <th>Utiliser la balise Meta property og:description </th>
        
        <td>
        	<input name="display_og_description_post" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_description_post' ) ); ?> /> Cochez pour utiliser la balise meta property og:description 
        	</br><small>Cette indiquera la description de votre article</small></td>
        </tr>    
         <tr valign="top">
        <th>Utiliser la balise Meta property og:type </th>
        
        <td>
        	<input name="display_og_type_post" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_type_post' ) ); ?> /> Cochez pour utiliser la balise meta property og:type 
        	</br><small>Cette indiquera le type de contenu : article</small></td>
        </tr>
        <th>Utiliser la balise Meta property og:image </th>
        
        <td>
        	<input name="display_og_image_post" type="checkbox" value="1" <?php checked( '1', get_option( 'display_og_image_post' ) ); ?> /> Cochez pour utiliser la balise meta property og:image 
        	</br><small>Cette indiquera la vignette a afficher (utiliser l&acute;option image a la une)</small></td>
        </tr>
    </table>

    <p class="submit">
    <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
    </p>

<?php 
// help file simplicy seo 
require_once(dirname(__FILE__).'/help/seo_help.php');
require_once(dirname(__FILE__).'/help/geotag_help.php'); 
require_once(dirname(__FILE__).'/help/facebook_help.php');
require_once(dirname(__FILE__).'/help/meta_creation_help.php');


?>
</form>
</div>
<?php } ?>
