<?php 


require_once( dirname(__FILE__) . "/../web/init.php");


require_once( PASTELL_PATH . "/lib/system/IParapheur.class.php");

$collectiviteProperties = $donneesFormulaireFactory->get(3,'collectivite-properties');

$iParapheur = new IParapheur($collectiviteProperties);

$result = $iParapheur->archiver("20120228C Test C"); 
print_r($result);

echo $iParapheur->getLastError();

/*foreach(get_object_vars($result) as $name => $value){
	echo $name."\n";
}*/



//$result = $iParapheur->effacerDossierRejete("20120221F");
//print_r($result);
