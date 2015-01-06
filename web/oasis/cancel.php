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
	$instance_id = $oasisProvisionning->getInstanceIdFromDeleteInstanceMessage($rawdata,$x_hub_signature);
	
	foreach($objectInstancier->ConnecteurEntiteSQL->getAllById("openid-authentication") as $connecteur_info){
		$connecteur_config = $objectInstancier->ConnecteruFactory->getConnecteurConfig($connecteur_info['id_ce']);
		if ($connecteur_config->get("instance_id") == $instance_id){
			$selected_id_e = $connecteur_info['id_e'];
			break;
		}
	}
	if (! $selected_id_e){
		throw new Exception("Impossible de trouvé une entité correspondante à l'instance_id $instance_id");
	}
	
	$objectInstancier->EntiteSQL->setActive($selected_id_e,0);
	
} catch (Exception $e){
	http_response_code(400);
	echo $e->getMessage();
	exit;
}
echo "ok";

