<?php
// seo Help

$chaine = "
<h1>Facebook Open Graph</h1>
Open Graph est un protocole développé par Facebook permettant de transformer n'importe quel page web en un objet capable de s'intégrer au graph social de Facebook.
</br></br>
- <a href='https://developers.facebook.com/docs/concepts/opengraph/' target='_blank'>Documentation(en)</a>
</br>
- <a href='http://ogp.me/' target='_blank'>Protocole Open Graph</a>
</br></br>
-<strong> property app_id</strong>  est l'identifiant de votre application. Ce dernier est disponible dans la partie <em>Settings > Basic</em> de l'application.
</br></br>
-<strong> property admins</strong>  Est votre identifiant Facebook, vous pouvez trouver votre ID de profil en survolant votre photo de profil Facebook, et prenant note de la valeur après <em><strong>set = a</strong></em>. , Par exemple, <em><strong>?Set = a.223100916</strong></em>
" 
?>

<div id="fb-open-graph" class="reveal-modal">	
			<?php
			
			echo htmlspecialchars_decode(htmlentities($chaine, ENT_NOQUOTES, 'UTF-8'));
			echo'<br/><br/>';
			echo'<div class="logo-sp-seo"></div>';
			?>
<a class="close-reveal-modal">&#215;</a>
</div>