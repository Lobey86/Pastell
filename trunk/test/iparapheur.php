<?php 


require_once( dirname(__FILE__) . "/../web/init.php");


require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

$collectiviteProperties = $donneesFormulaireFactory->get(3,'collectivite-properties');

$iParapheur = new IParapheur($collectiviteProperties);

//$result = $iParapheur->getSignature("20120221E"); 
//print_r($result);
//echo $iParapheur->getLastError();


$result = $iParapheur->effacerDossierRejete("20120221F");
print_r($result);
