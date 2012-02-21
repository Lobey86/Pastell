<?php 


require_once( dirname(__FILE__) . "/../web/init.php");


require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

$collectiviteProperties = $donneesFormulaireFactory->get(3,'collectivite-properties');

$iParapheur = new IParapheur($collectiviteProperties);

$result = $iParapheur->getSignature("20120221A"); 

foreach($result as $key => $value){
	echo $key."\n";
}

//print_r($result);
