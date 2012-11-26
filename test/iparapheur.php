<?php 


require_once( dirname(__FILE__) . "/../web/init.php");


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

