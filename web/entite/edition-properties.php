<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once( PASTELL_PATH . '/lib/formulaire/DataInjector.class.php');

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e',0);
$page = $recuperateur->getInt('page',0);

$document = new Document($sqlQuery);


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	header("Location: detail.php?id_e=$id_e");
	exit;
}

if ($id_e) {
	$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
} else {
	$documentType = $documentTypeFactory->getDocumentType('entite0-properties');
}
$formulaire = $documentType->getFormulaire();



$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();
$page_title="Edition des propriétés « " .$formulaire->getTabName($page). " » de  " . $infoEntite['denomination'] ;

$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');


$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->injectHiddenField("form_type",'collectivite-properties');
$afficheurFormulaire->injectHiddenField("id_e",$id_e);
$afficheurFormulaire->injectHiddenField("id_d",$id_e);


include( PASTELL_PATH ."/include/haut.php");
?>

<a href='entite/detail.php?id_e=<?php echo $id_e?>&page=<?php echo $page ?>'>« Revenir à <?php echo  $infoEntite['denomination'];  ?></a>
<br/><br/>

<?php 
		$afficheurFormulaire->afficheStaticTab($page);
?>

<div class="box_contenu clearfix">
<?php $afficheurFormulaire->affiche($page,"entite/edition-properties-controler.php","document/recuperation-fichier.php?id_d=$id_e&id_e=$id_e","entite/supprimer-fichier.php?id_e=$id_e&page=$page","entite/external-data.php"); ?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
