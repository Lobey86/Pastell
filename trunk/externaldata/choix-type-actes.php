<?php

require_once( PASTELL_PATH . "/externaldata/lib/TypeActes.class.php");


$donneesFormulaire = $donneesFormulaireFactory->get($id_e,"collectivite-properties");
$file = $donneesFormulaire->getFilePath('nomemclature_file');

if (! file_exists($file)){
	$lastError->setLastError("La nomemclature du CDG n'est pas disponible");
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

$typeActes = new TypeActes($file);

$page_title = "Choix du type d'Actes";


include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">

<h2>Types d'Actes</h2>
Veuillez sélectionner un type d'Actes :
<?php $typeActes->afficheClassification("document/external-data-controler.php?id_e=$id_e&id_d=$id_d&page=$page&field=$field")?>
</div>
<?php include( PASTELL_PATH ."/include/bas.php");