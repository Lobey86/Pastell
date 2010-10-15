<?php


require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");


$tedetis = new Tedetis($donneesFormulaire);



$result = $tedetis->testConnexion();

if ($result){
	$lastMessage->setLastMessage("La connexion est réussi");
} else {
	$lastError->setLastError("La connexion avec le Tedetis a échoué : " . $tedetis->getLastError());
}


header("Location: detail.php?id_e=$id_e&page=$page");