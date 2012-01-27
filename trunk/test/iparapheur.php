<?php 


require_once( dirname(__FILE__) . "/../web/init.php");


require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

$collectiviteProperties = $donneesFormulaireFactory->get(3,'collectivite-properties');

$iParapheur = new IParapheur($collectiviteProperties);

$result = $iParapheur->getSignature("1327659072"); //marche pas, ne renvoie rien

//$result = $iParapheur->getSignature("1327646102"); //fonctionne normalement

print_r($result);
