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


$page_title = "Configuration des connecteurs pour « {$info['denomination']} »";


include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>


<a href='entite/detail.php?id_e=<?php echo $id_e?>&page=2'>« Revenir à <?php echo $info['denomination']?></a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Connecteur <?php hecho($libelle)?> (<?php hecho($connecteur['type']) ?>/<?php hecho($connecteur['name'])?>)
<a href="connecteur/edition-modif.php?id_e=<?php echo $id_e?>&libelle=<?php echo $libelle ?>" class='btn_maj'>
			Modifier
		</a>

</h2>
<?php 

$documentType = $objectInstancier->ConnecteurFactory->getDocumentType($id_e,$libelle);
$formulaire = $documentType->getFormulaire();

$donneesFormulaire = $objectInstancier->ConnecteurFactory->getDataFormulaire($id_e,$libelle);
	
$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->injectHiddenField("id_e",$id_e);

$afficheurFormulaire->afficheStatic(0,"document/recuperation-fichier.php?id_d=$id_e&id_e=$id_e"); 
 

foreach($documentType->getAction()->getAll() as $action_name) : 

?>
<form action='connecteur/action2.php' method='post' >
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='libelle' value='<?php echo $libelle ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' value='<?php hecho($documentType->getAction()->getActionName($action_name)) ?>'/>
</form>
<?php endforeach;?>

</div>

<div class="box_contenu clearfix">
<h2>Supression</h2>
<a href="connecteur/delete.php?id_e=<?php echo $id_e?>&libelle=<?php echo $libelle ?>" class='btn_maj'>
			Supprimer ce connecteur 
</a>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
