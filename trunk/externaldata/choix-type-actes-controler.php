<?php

require_once( PASTELL_PATH . "/externaldata/lib/TypeActes.class.php");
require_once( PASTELL_PATH . "/externaldata/lib/ClassificationActes.class.php");

$classif = $recuperateur->get('classif');

$typeActes = new TypeActes(PASTELL_PATH . "/data-exemple/nomenclature.csv");

$info = $typeActes->getInfo($classif);

$info_classification = "";
if ($info['transmission_actes']){
	$entite = new Entite($sqlQuery,$id_e);
	$id_e_col = $entite->getCollectiviteAncetre();
	$donneesFormulaire = $donneesFormulaireFactory->get($id_e_col,$type);	
	$file = $donneesFormulaire->getFilePath('classification_file');

	if (! file_exists($file)){
		$info_classification = "La classfication en matière et sous-matière n'est pas disponible";
	} else {
		$classificationActes= new ClassificationActes($file);
		$info_classification = $classificationActes->getInfo($info['code_actes']);
		if (! $info_classification){
			$info_classification = "Cette classification (".$info['code_actes'].") n'existe pas sur le Tétédis";
		}
	}
	
}
$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
$donneesFormulaire->setData('type',$classif." ".$info['nom']);
$donneesFormulaire->setData('classification',$info_classification);
$donneesFormulaire->setData('envoi_tdt',$info['transmission_actes']);
$donneesFormulaire->setData('envoi_cdg',$info['transmission_cdg']);
$donneesFormulaire->setData('archivage',$info['archivage']);

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
