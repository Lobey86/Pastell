<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$recuperateur = new Recuperateur($_GET);
$id_ce = $recuperateur->getInt('id_ce');

$objectInstancier->ConnecteurControler->hasDroitOnConnecteur($id_ce);

$connecteur_entite_info = $objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);

$page_title = "Supression du connecteur  « {$connecteur_entite_info['libelle']} »";

include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>



<a href='connecteur/edition.php?id_ce=<?php echo $connecteur_entite_info['id_ce'] ?>'>« Revenir à la définition du connecteur</a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Connecteur <?php hecho($connecteur_entite_info['type']) ?> - <?php hecho($connecteur_entite_info['id_connecteur'])?> : <?php hecho($connecteur_entite_info['libelle']) ?> 
</h2>
<br/><br/>
<div class='box_alert'>
<p>Attention, la supression du connecteur est irréversible!</p>
</div>
<br/><br/>
<form action='connecteur/delete-controler.php' method='post' >
	<input type='hidden' name='id_ce' value='<?php echo $connecteur_entite_info['id_ce'] ?>' />
	<input type='submit' value='Supprimer le connecteur'/>
</form>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
