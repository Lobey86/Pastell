<?php

require_once(PASTELL_PATH."/externaldata/lib/ClassificationActes.class.php");

$entite = new Entite($sqlQuery,$id_e);
$ancetre = $entite->getCollectiviteAncetre();


$donneesFormulaire = $donneesFormulaireFactory->get($ancetre,'collectivite-properties');


$file = $donneesFormulaire->getFilePath('classification_file');


if (! file_exists($file)){
	$lastError->setLastError("La classification en matière et sous-matière n'est pas disponible ($file)");
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}
$classificationActes = new ClassificationActes($donneesFormulaire->getFilePath('classification_file'));

  
$page_title = "Choix de la classification en matière et sous matière";
include( PASTELL_PATH ."/include/haut.php");

?>

<div class="box_contenu clearfix">
<h2>Classification</h2>
Veuillez sélectionner une classification : 
<?php $classificationActes->affiche("document/external-data-controler.php?id_e=$id_e&id_d=$id_d&page=$page&field=$field");?>
</div>
<?php include( PASTELL_PATH ."/include/bas.php");
