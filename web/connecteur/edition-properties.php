<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once( PASTELL_PATH . '/lib/formulaire/DataInjector.class.php');

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e',0);
$page = $recuperateur->getInt('page',0);

$document = $objectInstancier->Document;


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) {
	header("Location: detail.php?id_e=$id_e");
	exit;
}

$documentType = $documentTypeFactory->getEntiteConfig($id_e);
$formulaire = $documentType->getFormulaire();

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();
$page_title="Edition des propriétés « " .$formulaire->getTabName($page). " » de  " . $infoEntite['denomination'] ;

$donneesFormulaire = $donneesFormulaireFactory->getEntiteFormulaire($id_e);


$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->injectHiddenField("form_type",'collectivite-properties');
$afficheurFormulaire->injectHiddenField("id_e",$id_e);
$afficheurFormulaire->injectHiddenField("id_d",$id_e);


include( PASTELL_PATH ."/include/haut.php");
?>

<a href='connecteur/detail.php?id_e=<?php echo $id_e?>&page=<?php echo $page ?>'>« Revenir à <?php echo  $infoEntite['denomination'];  ?></a>
<br/><br/>

<?php 
		$afficheurFormulaire->afficheStaticTab($page);
?>

<div class="box_contenu clearfix">
<?php $afficheurFormulaire->affiche($page,"connecteur/edition-properties-controler.php","document/recuperation-fichier.php?id_d=$id_e&id_e=$id_e","entite/supprimer-fichier.php?id_e=$id_e&page=$page","connecteur/external-data.php"); ?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
