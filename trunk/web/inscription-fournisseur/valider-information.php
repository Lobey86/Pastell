<?php

require_once("init-information.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

$recuperateur = new Recuperateur($_POST);
$page = $recuperateur->get('page');

$fileUploader = new FileUploader($_FILES);

$formulaire->setTabNumber($page);

$donneesFormulaire->save($recuperateur,$fileUploader);

$lastMessage->setLastMessage("Information enregistré");

header("Location: index.php?page=$page");