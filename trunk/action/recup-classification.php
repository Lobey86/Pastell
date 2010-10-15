<?php

require_once( PASTELL_PATH . "/lib/system/Tedetis.class.php");

$tedetis = new Tedetis($donneesFormulaire);

$result = $tedetis->getClassification();

if (! $result){
	$lastError->setLastError($tedetis->getLastError());
	header("Location: detail.php?id_e=$id_e&page=$page");
	exit;
}

$donneesFormulaire->addFileFromData("classification_file","classification.xml",$result);


$lastMessage->setLastMessage("La classification a été mise à jour");

header("Location: detail.php?id_e=$id_e&page=$page");