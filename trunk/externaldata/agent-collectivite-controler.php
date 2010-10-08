<?php


$agent = $recuperateur->get('agent');
$matricule = $agent[0];


$fileName = PASTELL_PATH . "/base-agent/agent.csv";
if (! file_exists($fileName)){
	exit;
}

$dataFile = explode("\n",file_get_contents($fileName));
$agent = array();
foreach($dataFile as $ligne){
	if (! $ligne){
		continue;
	}
	$l = explode(",",$ligne);	
	$agent[$l[0]] = $l;	
}

$data_agent = $agent[$matricule];
$da['matricule_de_lagent'] = $data_agent[0];
$da['prenom'] = $data_agent[1];
$da['nom_patronymique'] = $data_agent[2];
$status = array('titulaire' => 0,'stagiaire'=>1 , 
				'non-titulaire' => 2);
$da['statut']  = $status[$data_agent[3]];
$da['grade'] = $data_agent[4];


$data = new Recuperateur($da);

$donneesFormulaire = new DonneesFormulaire( WORKSPACE_PATH  . "/$id_d.yml");
$donneesFormulaire->setFormulaire($formulaire);

	
$donneesFormulaire->save($data,new FileUploader($_FILES));

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");