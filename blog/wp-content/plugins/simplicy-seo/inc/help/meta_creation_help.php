<?php
// seo Help

$chaine = "
<h1>Balise Meta Création</h1>
Ces balises permettent de fournir le nom de l'auteur d'une page, sont adresse email et une information sur les droits d'auteurs (auteur, année, mention de licence ou mention de type <em><strong>«tous droits réservés»</strong></em>). 
</br>
</br>
Utile surtout si vous voulez rappeler aux curieux qui lisent le code source des pages que le site a un auteur (personne ou organisation), et que tout n'est pas permis.
</br>
</br>
Pas exploité par les principaux moteurs de recherche. Peut-être exploité par certains outils très spécifiques.
" 
?>

<div id="meta_creation" class="reveal-modal">	
			<?php
			
			echo htmlspecialchars_decode(htmlentities($chaine, ENT_NOQUOTES, 'UTF-8'));
			echo'<br/><br/>';
			echo'<div class="logo-sp-seo"></div>';
			?>
<a class="close-reveal-modal">&#215;</a>
</div>