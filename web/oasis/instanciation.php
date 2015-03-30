<?php

require_once( __DIR__ . "/../init.php");


$oasisProvisionning = $objectInstancier->ConnecteurFactory->getGlobalConnecteur('oasis-provisionning');
if (!$oasisProvisionning){
	http_response_code(400);
	echo "Aucun connecteur Oasis Provisionning trouvé";
	exit;
}

if (empty($_SERVER['HTTP_X_HUB_SIGNATURE'])){
	http_response_code(400);
	echo "L'entete X-Hub-Signature n'a pas été trouvée";
	exit;
}

$x_hub_signature = $_SERVER['HTTP_X_HUB_SIGNATURE'];
$rawdata = file_get_contents('php://input');

try {
	$oasisProvisionning->addInstance($rawdata,$x_hub_signature);	

	$objectInstancier->Journal->add(Journal::CONNEXION,0,0,'instanciation', "Nouvelle demande de provisionning Oasis ajouté");
		
} catch (Exception $e){
	http_response_code(400);
	echo $e->getMessage();
	exit;
}
echo "ok";

