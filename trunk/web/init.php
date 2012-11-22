<?php 
require_once(__DIR__."/../DefaultSettings.php");

set_include_path( __DIR__ . "/../lib/" . PATH_SEPARATOR . __DIR__ . "/../model" . PATH_SEPARATOR . __DIR__ . "/../controler");

function __autoload($class_name) {	
	$result = include($class_name . '.class.php');
	if ( ! $result ){
		throw new Exception("Impossible de trouver $class_name");
	}
}


$timer = new Timer();

session_start();

$zLog = new ZLog(LOG_FILE);		
$zLog->setLogLevel(LOG_LEVEL);

$lastError = new LastError();
$lastMessage = new LastMessage();

$sqlQuery = new SQLQuery(BD_DSN,BD_USER,BD_PASS);
$sqlQuery->setLog($zLog);

$authentification = new Authentification();

$roleUtilisateur = new RoleUtilisateur($sqlQuery);
$roleUtilisateur->setRoleDroit(new RoleDroit());

require_once( PASTELL_PATH. "/lib/document/DocumentTypeFactory.class.php");
$documentTypeFactory = new DocumentTypeFactory();

require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaireFactory.class.php");
$donneesFormulaireFactory = new DonneesFormulaireFactory($documentTypeFactory,WORKSPACE_PATH);

$opensslTSWrapper = new OpensslTSWrapper(OPENSSL_PATH,$zLog);
$signServer = new SignServer(SIGN_SERVER_URL,$opensslTSWrapper);

$id_u_journal = 0;
if ($authentification->isConnected()) {
	$id_u_journal = $authentification->getId();
}
$journal = new Journal($signServer,$sqlQuery,$id_u_journal);

require_once( PASTELL_PATH . "/lib/util.php");

define("DATABASE_FILE", PASTELL_PATH."/installation/pastell.bin");

$objectInstancier = new ObjectInstancier();
$objectInstancier->versionFile = __DIR__."/../version.txt";
$objectInstancier->revisionFile = __DIR__."/../revision.txt";
$objectInstancier->SQLQuery = $sqlQuery;
$objectInstancier->workspacePath = WORKSPACE_PATH;

$versionning = $objectInstancier->Versionning;


