<?php
add_action( 'add_meta_boxes', 'seo_add_sp_meta' );
function seo_add_sp_meta()
{
	add_meta_box( 'seo-meta', __( 'Simplicy SEO post' ), 'seo_sp_meta_cb', 'post', 'normal', 'high' );
	add_meta_box( 'seo-meta', __( 'Simplicy SEO page' ), 'seo_sp_meta_cb', 'page', 'normal', 'high' );
}

function seo_sp_meta_cb( $post )
{
	$seo_desc_post = get_post_meta( $post->ID, 'seo_desc_post_code', true );
	$seo_tile_post = get_post_meta( $post->ID, 'seo_title_sp', true );
	$seo_keys = get_post_meta( $post->ID, 'seo_keys_post', true );
	$seo_url_post = get_post_meta( $post->ID, 'seo_url_post', true );
	$news_keywords = get_post_meta( $post->ID, 'seo_news_keywords', true );
	$seo_post_robots = get_post_meta( $post->ID, 'seo_post_robots', true );
	
	wp_nonce_field( 'save_seo_meta', 'seo_nonce' );
	?>
    
	<div>
    <p>
		<label for="title-seo">Titre Seo</label>
		<input type="text" class="widefat" id="seo_title_sp" name="seo_title_sp" onKeyDown="showHTML()" onKeyPress="return charLimit(this)" onKeyUp="return characterCount(this)" value="<?php echo $seo_tile_post; ?>" />
        <br><p class="seo-infos"><span class="counter" id="charCount"></span> Caract&egrave;res restants. Id&eacute;al : <strong>60</strong> caract&egrave;res. Maximum : <strong>100</strong> caract&egrave;res.</p>
		<br>
	</p>
	<p>
		<label for="desc-content">Description</label>
		<textarea class="widefat" id="seo_desc_post_code"  name="seo_desc_post_code" onKeyDown="showHTML()" onKeyPress="return charLimit2(this)" onKeyUp="return characterCount2(this)"><?php echo $seo_desc_post; ?></textarea>
        <br><p class="seo-infos"><span class="counter" id="charCount2"></span> Caract&egrave;res restants. Id&eacute;al : <strong>156</strong> caract&egrave;res. Maximum : <strong>200</strong> caract&egrave;res.</p>
		<br>
	</p>
	
	<p>
		<label for="keys-seo">Mots Cl&eacute;s<small><em>(balise meta keywords)</em></small></label>
		<input type="text" class="widefat" id="seo_keys_post" name="seo_keys_post" value="<?php echo $seo_keys; ?>" />
	</p>
    <p>
		<label for="keys-seo">Mots Cl&eacute;s <small><em>(balise meta news_keywords google)</em></small> </label>
		<input type="text" class="widefat" id="seo_news_keywords" name="seo_news_keywords" value="<?php echo $news_keywords; ?>" />
        </br><small>Les mots cl&eacute;s sont limit&eacute;s &agrave; 10 et doivent &ecirc;tre s&eacute;par&eacute;s par des virgules.<a href="http://support.google.com/news/publisher/bin/answer.py?hl=fr&answer=68297" target="_blank">En savoir plus</a></small>
	</p>
   <p>
		<label for="keys-seo">URL canonique <small>Indiquer le lien original de l'article</small></label>
		<input type="text" class="widefat" id="seo_url_post" name="seo_url_post" value="<?php echo $seo_url_post ; if (empty($seo_url_post[0]) ){echo the_permalink(); }  ?>" />																				
	</p>
    <p>
    <p id="preview-seo">Aper&ccedil;u</p>
    </p>
    <div id="preview">
<div id="title-seo" name="title">
<?php 
echo $seo_tile_post; 
if (empty($seo_tile_post[0]) )
 {
  echo  the_title();
 }
?>
</div>
<div id="permalink-seo-post">
<?php echo the_permalink(); ?> 
</div>
<div id="desc-seo" name="desc-seo">
<?php 
echo $seo_desc_post;
if (empty($seo_desc_post[0]) )
 {
    echo custom_seo_excerpt(get_the_excerpt());
 }
?>
</div>
</div>
</div> <!-- option 1 fin -->

	
    <p>
		<label for="keys-seo">Balise meta robots </label> </br>
		<input type="text" style=" width:45%" class="widefat" id="seo_post_robots" name="seo_post_robots" value="<?php echo $seo_post_robots ;?>" /></br>
        <small>(Indique aux robots, votre choix d'indexer, d'archiver ou non ce document) <a href="<?php echo plugins_url( 'inc/help/seo_help.php' , dirname(__FILE__) ); ?>?KeepThis=true&amp;TB_iframe=true&amp;height=400&amp;width=600" class="thickbox">En savoir plus</a></small>
      
</p>
<!-- <input type="button" onclick="showHTML()" value="Ok" /> --> 

	<?php
	require_once(dirname(__FILE__).'/help/seo_help.php');
}

add_action( 'save_post', 'seo_sp_meta_save' );
function seo_sp_meta_save( $id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	
	if( !isset( $_POST['seo_nonce'] ) || !wp_verify_nonce( $_POST['seo_nonce'], 'save_seo_meta' ) ) return;
	
	if( !current_user_can( 'edit_post' ) ) return;
	
	$allowed = array(
		'p'	=> array()
	);
	
	if( isset( $_POST['seo_desc_post_code'] ) )
		update_post_meta( $id, 'seo_desc_post_code', wp_kses( $_POST['seo_desc_post_code'], $allowed ) );
	
	if( isset( $_POST['seo_title_sp'] ) )
		update_post_meta( $id, 'seo_title_sp', esc_attr( strip_tags( $_POST['seo_title_sp'] ) ) );
		
	if( isset( $_POST['seo_keys_post'] ) )
		update_post_meta( $id, 'seo_keys_post', esc_attr( strip_tags( $_POST['seo_keys_post'] ) ) );
	
	if( isset( $_POST['seo_news_keywords'] ) )
		update_post_meta( $id, 'seo_news_keywords', esc_attr( strip_tags( $_POST['seo_news_keywords'] ) ) );
	
	if( isset( $_POST['seo_url_post'] ) )
		update_post_meta( $id, 'seo_url_post', esc_attr( strip_tags( $_POST['seo_url_post'] ) ) );
	
	if( isset( $_POST['seo_post_robots'] ) )
		update_post_meta( $id, 'seo_post_robots', esc_attr( strip_tags( $_POST['seo_post_robots'] ) ) );
	
}
?>