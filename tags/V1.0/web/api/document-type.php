<?php
require_once("init-api.php");

$allDocType = $documentTypeFactory->getAllType();

$allDroit = $roleUtilisateur->getAllDroit($id_u);

foreach($allDocType as $type_flux => $les_flux){
	foreach($les_flux as $nom => $affichage) {
		if ($roleUtilisateur->hasOneDroit($id_u,$nom.":lecture")){
			$allType[$nom]  = array('type'=>$type_flux,'nom'=>$affichage);
		}
	}
}

$JSONoutput->display($allType);