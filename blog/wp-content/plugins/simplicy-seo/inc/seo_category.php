<?php
// Seo Category and Tags

if(isset($_POST['action']) && $_POST['action']=="editedtag" && $_POST['taxonomy']=="post_tag") {
    $tag_meta_setting['page_title']=$_POST['tag_title'];
    $tag_meta_setting['description']=$_POST['tag_desc'];
    $tag_meta_setting['metakey']=$_POST['tag_keywords'];
	$tag_meta_setting['robotskey']=$_POST['tag_robots'];
	if(!empty($tag_meta_setting['page_title'])) {
		 update_option('tag_meta_key_'.$_POST['tag_ID'],$tag_meta_setting);
	}	 
}

function get_current_tag_meta($cur_tag_id) {
	$csmt_options = get_option('csmt_options');
	if(get_option('tag_meta_key_'.$cur_tag_id) && $csmt_options['csmt_enabled']) {
	  $tag_meta_data = get_option('tag_meta_key_'.$cur_tag_id);
	  echo '<!-- Category SEO + -->'."\r\n";
	  echo '<title>'.$tag_meta_data['page_title'].'</title>'."\r\n";
	  echo '<meta name="description" content="'.$tag_meta_data['description'].'" />'."\r\n";
	  echo '<meta name="keywords" content="'.$tag_meta_data['metakey'].'" />'."\r\n";
	  echo '<meta name="robots" content="'.$tag_meta_data['robotskey'].'" />'."\r\n";
	  echo '<!-- /Category SEO Meta Tags 2.2 -->'."\r\n";
	}
}




?>
<?php // Catégories
if(isset($_POST['action']) && $_POST['action']=="editedtag" && $_POST['taxonomy']=="category") {
    $cat_meta_setting['page_title']=$_POST['cat_title'];
    $cat_meta_setting['description']=$_POST['cat_desc'];
    $cat_meta_setting['metakey']=$_POST['cat_keywords'];
	$cat_meta_setting['robotskey']=$_POST['cat_robots'];
	if(!empty($cat_meta_setting['page_title'])) {
		 update_option('cat_meta_key_'.$_POST['tag_ID'],$cat_meta_setting);
	}	 
}

function get_current_cat_meta($cur_cat_id) {
	$csmt_options = get_option('csmt_options');
	if(get_option('cat_meta_key_'.$cur_cat_id) && $csmt_options['csmt_enabled']) {
	  $cat_meta_data = get_option('cat_meta_key_'.$cur_cat_id);
	  echo '<!-- Category SEO + -->'."\r\n";
	  echo '<title>'.$cat_meta_data['page_title'].'</title>'."\r\n";
	  echo '<meta name="description" content="'.$cat_meta_data['description'].'" />'."\r\n";
	  echo '<meta name="keywords" content="'.$cat_meta_data['metakey'].'" />'."\r\n";
	  echo '<meta name="news_keywords" content="'.$cat_meta_data['metakey'].'" />'."\r\n";
	  echo '<meta name="robots" content="'.$cat_meta_data['robotskey'].'" />'."\r\n";
	  echo '<!-- /Category SEO + -->'."\r\n";
	}
}
function show_category_meta() {
	$cur_cat_id = get_cat_id( single_cat_title("",false) );
	if(is_category($cur_cat_id)) {
		get_current_cat_meta($cur_cat_id);
	}

	if(is_tag()) {
		$cur_tag_id = get_query_var('tag_id');
		get_current_tag_meta($cur_tag_id);
	}

}
?>
<?php
function category_meta_form() {

if(isset($_GET['action']) && $_GET['action']=="edit") {
?>
<div class="icon32" id="icon-edit"><br></div>
<h2><?php echo _e("Cat&eacute;gories : Param&egrave;tre des balises meta "); ?></h2>
<?php $cat_meta = get_option('cat_meta_key_'.$_GET['tag_ID']); //print_r( $cat_meta); ?>
<table class="form-table" >
<tbody>
  <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="cat_title"><?php echo _e("Titre de la cat&eacute;gorie"); ?>:</label></th>
    <td><input name="cat_title" type="text"  onKeyDown="showHTML()" onKeyPress="return charLimit(this)" onKeyUp="return characterCount(this)" style=" width:630px;" value="<?php echo $cat_meta['page_title']; ?>" />
    <br><p class="seo-infos"><span class="counter" id="charCount"></span> Caract&egrave;res restants. Id&eacute;al : <strong>60</strong> caract&egrave;res. Maximum : <strong>100</strong> caract&egrave;res.</p>
		<br>
    <p class="description"><?php echo _e("Entrer le titre de la cat&eacute;gorie ici."); ?><span style="color:#F00"><?php echo _e(" (* Important)", $csmt_domain); ?></span>
</p>
    </td>
  </tr>
  <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="cat_title"><?php echo _e("Description", $csmt_domain); ?>:</label></th>
    <td><textarea name="cat_desc" style=" width:630px; height:100px;" onKeyDown="showHTML()" onKeyPress="return charLimit2(this)" onKeyUp="return characterCount2(this)"><?php echo $cat_meta['description']; ?></textarea>
    <br><p class="seo-infos"><span class="counter" id="charCount2"></span> Caract&egrave;res restants. Id&eacute;al : <strong>156</strong> caract&egrave;res. Maximum : <strong>200</strong> caract&egrave;res.</p><br>
    <p class="description"><?php echo _e("Entrer la description de la cat&eacute;gorie ici."); ?><span style="color:#F00"><?php echo _e(" (Peut &ecirc;tre laiss&eacute; vide)"); ?></span></p>
    </td>
  </tr>
  <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="cat_title"><?php echo _e("mot cl&eacute;s"); ?>:</label></th>
    <td><input name="cat_keywords" type="text" style=" width:630px;" value="<?php echo $cat_meta['metakey']; ?>" />
    <p class="description"><?php echo _e("Entrer les mots cl&eacute;s de la cat&eacute;gorie ici.(meta news_keywords & keywords)"); ?><span style="color:#F00"><?php echo _e(" (Peut &ecirc;tre laiss&eacute; vide)"); ?></span></p></td>
  </tr>
  <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="cat_title"><?php echo _e("Meta robots"); ?>:</label></th>
    <td><input name="cat_robots" type="text" style=" width:630px;" value="<?php echo $cat_meta['robotskey']; ?>" />
    <p class="description"><?php echo _e("index,follow <em>(par défaut)"); ?><span style="color:#F00"><?php echo _e(" (Important)"); ?></span></p></td>
  </tr>
  <?php
function user_the_categories() {
    // get all categories for this post
    global $cats;
    $cats = get_the_category();
    // echo the first category
    echo $cats[0]->cat_name;
    // echo the remaining categories, appending separator
    for ($i = 1; $i < count($cats); $i++) {echo ', ' . $cats[$i]->cat_name ;}
}
?>
  <?php $site_url = network_site_url( '/' );?>
  <tr valign="top">
  
        <th>Aper&ccedil;u</th>
        <td>

        <div id="preview">
		<div id="title-seo" name="title-seo">
		<?php echo $cat_meta['page_title'];
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
        <?php echo $cat_meta['description'];
		if (empty($seo_desc[0]) )
		 {
    	echo $site_description ;
 		}
		 ?>
        </div>
        </div>
        </td>
        </tr>  
 </tbody> 
</table>
<?php
}
}

function tag_meta_form() {
global $csmt_domain;
if(isset($_GET['action']) && $_GET['action']=="edit") {
?>
<div class="icon32" id="icon-edit"><br></div>
<h2><?php echo _e("Mots cl&eacute;s : Param&egrave;tre des balises meta ", $csmt_domain); ?></h2>
<?php $cat_meta = get_option('tag_meta_key_'.$_GET['tag_ID']); //print_r( $cat_meta); ?>
<table class="form-table" >
<tbody>
  <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="tag_title"><?php echo _e("Titre pour le mot cl&eacute; :"); ?></label></th>
    <td><input name="tag_title" type="text"  onKeyDown="showHTML()" onKeyPress="return charLimit(this)" onKeyUp="return characterCount(this)" style=" width:630px;"  value="<?php echo $cat_meta['page_title']; ?>"  />
    <br><p class="seo-infos"><span class="counter" id="charCount"></span> Caract&egrave;res restants. Id&eacute;al : <strong>60</strong> caract&egrave;res. Maximum : <strong>100</strong> caract&egrave;res.</p>
		<br>
    <p class="description"><?php echo _e("Entrer le titre du mot cl&eacute; ici."); ?><span style="color:#F00"><?php echo _e(" (* Important)"); ?></span></p>
    </td>
  </tr>
  <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="tag_desc"><?php echo _e("Description:"); ?></label></th>
    <td><textarea name="tag_desc" style=" width:630px; height:100px;" onKeyDown="showHTML()" onKeyPress="return charLimit2(this)" onKeyUp="return characterCount2(this)"><?php echo $cat_meta['description']; ?></textarea>
    <br><p class="seo-infos"><span class="counter" id="charCount2"></span> Caract&egrave;res restants. Id&eacute;al : <strong>156</strong> caract&egrave;res. Maximum : <strong>200</strong> caract&egrave;res.</p><br>
    <p class="description"><?php echo _e("Entrer la description du mot cl&eacute; ici."); ?><span style="color:#F00"><?php echo _e(" (Peut &ecirc;tre laiss&eacute; vide)"); ?></span></p>
    </td>
  </tr>
  <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="tag_keywords"><?php echo _e("mot cl&eacute;s"); ?></label></th>
    <td><input name="tag_keywords" type="text" style=" width:630px;" value="<?php echo $cat_meta['metakey']; ?>" />
    <p class="description"><?php echo _e("Entrer les mots cl&eacute;s seo ici.(meta news_keywords & keywords)"); ?><span style="color:#F00"><?php echo _e(" (Peut &ecirc;tre laiss&eacute; vide)"); ?></span></p></td>
  </tr>
   <tr class="form-field form-required">
  <th valign="top" scope="row"><label for="tag_robots"><?php echo _e("Meta robots"); ?>:</label></th>
    <td><input name="tag_robots" type="text" style=" width:630px;" value="<?php echo $cat_meta['robotskey']; ?>" />
    <p class="description"><?php echo _e("index,follow <em>(par défaut)"); ?><span style="color:#F00"><?php echo _e(" (Important)"); ?></span></p></td>
  </tr>
   <?php $site_url = network_site_url( '/' );?>
  <tr valign="top">
        <th>Aper&ccedil;u</th>
        <td>
        <div id="preview">
		<div id="title-seo" name="title-seo">
		<?php echo $cat_meta['page_title'];
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
        <?php echo $cat_meta['description'];
		if (empty($seo_desc[0]) )
		 {
    	echo $site_description ;
 		}
		 ?>
        </div>
        </div>
        </td>
        </tr>  
 </tbody> 
</table>

<?php
}
}
add_action ('edit_category_form', 'category_meta_form' );
add_action ('edit_tag_form', 'tag_meta_form' );