<?php
// seo Help

$chaine = "
<h1>Balise meta robots</h1>
 <p>Cette balise est prise en compte par Google (et les autres moteurs). Elle sert à définir des restrictions au robot qui vient crawler la page</p>
        <p><strong>noindex</strong> : indique au robot qu'il ne faut pas indexer la page.</p>
        <p><strong>nofollow </strong>: indique au robot qu'il ne faut pas suivre les liens dans la page.</p>
        <p><strong>index</strong> : indique au robot qu'il peut indexer la page</p>	
        <p><strong>follow </strong>: indique au robot qu'il peut suivre les liens dans la page.</p>
        <p><strong>all</strong> : cette valeur est l'équivalent de index,follow</p>	
        <p><strong>none</strong> : cette valeur est l'équivalent de noindex,nofollow</p>
        <p><strong>nosnippet</strong> : indique au robot qu'il ne faut pas afficher de descriptif (snippet) dans la page de résultats. </p>	
       <p> <strong>noarchive</strong> : indique au robot qu'il ne faut pas laisser l'accès à la version en cache. Le lien <strong>En cache</strong> dans la page de résultats ne sera donc pas affiché. Ceci peut servir à ceux qui passent leurs contenu d'une version publique accessible à une version archivée payante <em>(sites de journaux par exemple)</em>.</p>
       <p><strong>noodp</strong> : indique au robot qu'il ne faut pas utiliser les données associées au site par les éditeurs de l'annuaire DMOZ (Open Directory Project, ODP). Ceci est utile si la description ou le titre du site dans DMOZ ne correspondent pas assez bien à la réalité.</p>
       <p><strong>noydir</strong> : qui permet comme noodp d'indiquer au moteur qu'on ne souhaite pas que les données de l'annuaire Yahoo (Yahoo Directory) soient utilisées</p>
       <p><strong>unavailable_after:[date]</strong> : indique au robot que la page ne doit pas ressortir dans les résultats après la date indiquée.</p>																	
	</p>
	
	<p style='text-align:center;'><strong>Exemples de combinaisons :</strong> </br></br>
	
	index,follow <em>(par défaut)</em>
	</br>
	noindex,follow
	</br>
	index,nofollow
	</br>
	noindex,nofollow
	</p>
	" 
?>

<div id="meta_robots" class="reveal-modal">	
			<?php
			
			echo htmlspecialchars_decode(htmlentities($chaine, ENT_NOQUOTES, 'UTF-8'));
			echo'<br/><br/>';
			echo'<div class="logo-sp-seo"></div>';
			?>

</div>