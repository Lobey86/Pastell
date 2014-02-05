<?php 
require_once(__DIR__."/../DefaultSettings.php");
set_include_path(__DIR__ . "/../pastell-core/" . PATH_SEPARATOR . __DIR__ . "/../lib/" . PATH_SEPARATOR . __DIR__ . "/../model" . PATH_SEPARATOR . __DIR__ . "/../controler");

function __autoload($class_name) {	
	$result = include($class_name . '.class.php');
	if ( ! $result ){
		throw new Exception("Impossible de trouver $class_name");
	}
}

if(php_sapi_name() != "cli") {
	session_start();
}

require_once( PASTELL_PATH . "/lib/util.php");


$timer = new Timer();


$objectInstancier = new ObjectInstancier();
$objectInstancier->versionFile = __DIR__."/../version.txt";
$objectInstancier->revisionFile = __DIR__."/../revision.txt";
$objectInstancier->workspacePath = WORKSPACE_PATH;
$objectInstancier->action_class_directory = __DIR__ ."/../action/"; 
$objectInstancier->module_path = __DIR__."/../module/";
$objectInstancier->connecteur_path = __DIR__."/../connecteur/";
$objectInstancier->template_path = __DIR__."/../template/";
$objectInstancier->api_definition_file_path = __DIR__ . "/../pastell-core/api-definition.yml";

$objectInstancier->opensslPath = OPENSSL_PATH;

$objectInstancier->bd_dsn = BD_DSN;
$objectInstancier->bd_user = BD_USER;
$objectInstancier->bd_password = BD_PASS;

$objectInstancier->upstart_touch_file = UPSTART_TOUCH_FILE;

$id_u_journal = 0;
if ($objectInstancier->Authentification->isConnected()) {
	$id_u_journal = $objectInstancier->Authentification->getId();
}
$objectInstancier->Journal->setId($id_u_journal);

$horodateur = $objectInstancier->ConnecteurFactory->getGlobalConnecteur('horodateur');
if ($horodateur){
	$objectInstancier->Journal->setHorodateur($horodateur);
}

$sqlQuery = $objectInstancier->SQLQuery;
$versionning = $objectInstancier->Versionning;
$lastError = $objectInstancier->LastError;
$lastMessage = $objectInstancier->LastMessage;
$authentification = $objectInstancier->Authentification;
$journal = $objectInstancier->Journal;
$documentTypeFactory = $objectInstancier->DocumentTypeFactory;
$donneesFormulaireFactory = $objectInstancier->DonneesFormulaireFactory;
$roleUtilisateur = $objectInstancier->RoleUtilisateur;

define("DATABASE_FILE", PASTELL_PATH."/installation/pastell.bin");




