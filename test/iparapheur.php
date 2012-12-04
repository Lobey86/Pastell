<?php 


$soap = new SoapClient("https://iparapheur.demonstrations.adullact.org/ws-iparapheur-no-mtom?wsdl",
	
	array(
	     			'local_cert' => '/home/eric/adullact/pastell-workspace/connecteur_8.yml_iparapheur_user_key_pem_0',
	     			'passphrase' => 'adullact',
					'login' => 'wspastell@pastell',
					'password' => 'wspastell123',
					'trace' => 1,
					'exceptions' => 1,
	    		)

);
/*

require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

$collectiviteProperties = $donneesFormulaireFactory->getEntiteFormulaire(3);

$iParapheur = new IParapheur($collectiviteProperties);

$result = $iParapheur->getDossier("Test C pesA_xml"); 

foreach($result as $k => $v){
	echo $k."\n";
}

print_r($result->FichierPES);
//print_r(array_keys($result));
//$info = $iParapheur->getBordereau($result);
//print_r($info);

echo $iParapheur->getLastError();

