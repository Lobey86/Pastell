<?php
$id_a = $recuperateur->get('id_a');

$entite = new Entite($sqlQuery,$id_e);
$siren =  $entite->getSiren();


$agentSQL = new AgentSQL($sqlQuery);
$info = $agentSQL->getInfo($id_a,$siren);


$da['matricule_de_lagent'] = $info['matricule'];
$da['prenom'] = $info['prenom'];
$da['nom_patronymique'] = $info['nom_patronymique'];
$status = array('titulaire' => 0,'stagiaire'=>1 , 
				'non-titulaire' => 2);
$da['statut']  = 0;
$da['grade'] = $info['emploi_grade_libelle'];


$data = new Recuperateur($da);


$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);

$donneesFormulaire->saveTab($data,new FileUploader($_FILES),$page);

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");