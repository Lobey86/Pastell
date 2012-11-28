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


</h2>
<?php 

$documentType = $objectInstancier->ConnecteurFactory->getDocumentType($id_e,$libelle);
$formulaire = $documentType->getFormulaire();

$donneesFormulaire = $objectInstancier->ConnecteurFactory->getDataFormulaire($id_e,$libelle);
	
$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->injectHiddenField("id_e",$id_e);
$afficheurFormulaire->injectHiddenField("id_d",$id_e);
$afficheurFormulaire->injectHiddenField("old_libelle",$libelle);

$afficheurFormulaire->affiche(0,"connecteur/edition-modif-controler.php","document/recuperation-fichier.php?id_d=$id_e&id_e=$id_e","document/recuperation-fichier.php?id_d=$id_e&id_e=$id_e","entite/supprimer-fichier.php?id_e=$id_e","connecteur/external-data.php"); 

?></div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
