<?php



$data['matricule_de_lagent'] = "";
$data['prenom'] = "";
$data['nom_patronymique'] = "";
$data['statut']  = "";
$data['grade'] = "";

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
$donneesFormulaire->setTabData($data);

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
exit;
