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
} catch (Exception $e){
	http_response_code(400);
	echo $e->getMessage();
	exit;
}
echo "ok";




function parseRequestHeaders() {
	$headers = array();
	foreach($_SERVER as $key => $value) {
		if (substr($key, 0, 5) <> 'HTTP_') {
			continue;
		}
		$header = str_replace(' ', '-', ucwords(str_replace('_', ' ', strtolower(substr($key, 5)))));
		$headers[$header] = $value;
	}
	return $headers;
}

/*
 * {"instance_id":"9436729b-a875-438a-8767-a3481b3ce0d5","client_id":"9436729b-a875-438a-8767-a3481b3ce0d5","client_secret":"hBJO+XIRhSFazT+BEv4z3mCYSIwXde99tLgJK4dMflk","user_id":"447e1478-461b-4802-b3a5-81fb7ae912c2","user":{"id":"447e1478-461b-4802-b3a5-81fb7ae912c2","name":"igu
illaumedenis@yahoo.fr","email_address":"iguillaumedenis@yahoo.fr"},"organization_id":"4725bf47-8846-4fdb-adcf-542d373e0676","organization_name":"Sigmalis","organization":{"id":"4725bf47-8846-4fdb-adcf-542d373e0676","name":"Sigmalis","type":"PUBLIC_BODY"},"instance_registration_u
ri":"https://kernel.ozwillo-preprod.eu/apps/pending-instance/9436729b-a875-438a-8767-a3481b3ce0d5"}
X-Hub-Signature : sha1=B27BE0C30467E7465661F6162461592027DB296C
Accept-Encoding : gzip, deflate
Host : dev.sigmalis.com
Connection : Keep-Alive

 */