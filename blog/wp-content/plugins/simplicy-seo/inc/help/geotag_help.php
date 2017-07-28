<?php
// seo Help

$chaine = "
<h1>Balise Meta GEOTAG</h1>
Les <strong>meta geotags</strong> sont des balises d'indications géographiques, peu utilisées par google car pas toujours bien renseignées, les robots habituellement utilisent la recherche de la langue et de la région directement avec le contenu de la page ce qui permet d'obtenir une localisation plus juste du document. Mais cette balise est utilisée par le moteur de google Bing alors il ne faut pas la négliger, d'autant plus si votre positionnement géographique est stratégique dans votre activité (e-tourisme).
</br></br>
<strong>La balise geo.position</strong> renseigne sur la latitude et la longitude de l'emplacement du site Sa syntaxe est la suivante : La latitude est séparée de la longitude par un point-virgule. Pour obtenir ces données, allez sur Google Maps, renseignez le nom de votre ville, puis cliquez sur '<em>labos de google maps</em>' puis activez les repères de latitudes et de longitudes.
</br></br>
<strong>La balise geo.placename</strong> renseigne sur la localisation, son contenu est généralement le nom d'une ville ou/et de région.
</br></br>
<strong>La balise geo.region</strong> elle désigne le pays dans lequel se trouve le site. Le format est normalisé selon la norme ISO 3166. Vous pouvez trouver la liste des codes de pays ici : <a href='http://en.wikipedia.org/wiki/ISO_3166-2:FR' target='_blank'>ISO-3166 Country Name</a>.<em> Exemple FR-75(France avec le département 75)</em>
</br></br>
Belgique : <a href='http://en.wikipedia.org/wiki/ISO_3166-2:BE' target='_blank'>ISO-3166 Country Name</a>
" 
?>

<div id="geo-tags" class="reveal-modal">	
			<?php
			
			echo htmlspecialchars_decode(htmlentities($chaine, ENT_NOQUOTES, 'UTF-8'));
			echo'<br/><br/>';
			echo'<div class="logo-sp-seo"></div>';
			?>
<a class="close-reveal-modal">&#215;</a>
</div>