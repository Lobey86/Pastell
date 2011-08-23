<?php 

require_once(PASTELL_PATH . "/lib/system/WebGFC.class.php");
$webGFC = new WebGFC();
$message_type = $recuperateur->get('messagetype');
$message_sous_type = $recuperateur->get('messagesoustype');

if (! $message_sous_type){
	header("Location: external-data.php?id_d=$id_d&id_e=$id_e&page=$page&field=$field&messagetype=$message_type");
	exit;
}

$message_type = $webGFC->getInfo($message_type);
$message_sous_type =  $webGFC->getInfo($message_sous_type);

$info = $message_type[1].":".$message_sous_type[1];


$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$type);
$donneesFormulaire->setData('messagetype',$info);
$donneesFormulaire->setData('messageSousTypeId',$message_sous_type[0]);

header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
