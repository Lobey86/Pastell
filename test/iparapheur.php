<?php 


require_once( dirname(__FILE__) . "/../web/init.php");


require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

$collectiviteProperties = $donneesFormulaireFactory->get(3,'collectivite-properties');

$iParapheur = new IParapheur($collectiviteProperties);

$result = $iParapheur->getDossier("20120304F Test F"); 

$info = $iParapheur->getBordereau($result);
print_r($info);

echo $iParapheur->getLastError();

