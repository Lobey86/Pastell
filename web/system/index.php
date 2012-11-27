<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

if  (! $roleUtilisateur->hasDroit($authentification->getId(),'test:lecture',0)){
	header("Location: ".SITE_BASE . "/index.php");
	exit;
}

$page_title = "Environnement système";

include( PASTELL_PATH ."/include/haut.php");


include (PASTELL_PATH."/include/bloc_message.php"); ?>


<div class="box_contenu clearfix">


<h2>Cache</h2>

<a href='system/vide-cache.php'>Vider le cache des fichiers de conf YML</a>

<br/>
<br/>
<h2>Upstart</h2>
Dernier lancement du script action-automatique (par upstart ou crontab) : <?php echo $objectInstancier->LastUpstart->getLastMtime(); ?> 


<?php if ( ENABLE_VERIF_ENVIRONNEMENT ) :  ?>
<br/>
<br/>
<h2>Vérification de l'environnement</h2>
<a href='system/verif-environnement.php'>Vérifier l'environnement</a>
<br/><br/>
<?php endif; ?>

</div>



<?php 
include( PASTELL_PATH ."/include/bas.php");
