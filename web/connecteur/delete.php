<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$libelle = $recuperateur->get('libelle');



$droit_ecriture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e);

if ( ! $droit_ecriture ){
	header("Location: index.php");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
if ($id_e && ! $entite->exists()){
	header("Location: index.php");
	exit;
}
$info = $entite->getInfo();

$connecteur = $objectInstancier->ConnecteurFactory->getInfo($id_e,$libelle);


$page_title = "Supression du connecteur  « $libelle »";


include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>



<a href='entite/edition.php?id_e=<?php echo $id_e?>&libelle=<?php hecho($libelle)?>'>« Revenir à la définition du connecteur</a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Connecteur <?php hecho($libelle)?> (<?php hecho($connecteur['type']) ?>/<?php hecho($connecteur['name'])?>)
</h2>
<br/><br/>
<div class='box_alert'>
<p>Attention, la supression du connecteur est irréversible!</p>
</div>
<br/><br/>
<form action='connecteur/delete-controler.php' method='post' >
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='libelle' value='<?php echo $libelle ?>' />
	
	<input type='submit' value='Supprimer le connecteur'/>
</form>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
