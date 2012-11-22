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

require_once( PASTELL_PATH . "/lib/messageHTML/LastError.class.php");
$lastError = new LastError();

require_once( PASTELL_PATH . "/lib/messageHTML/LastMessage.class.php");
$lastMessage = new LastMessage();

$sqlQuery = new SQLQuery(BD_DSN,BD_USER,BD_PASS);
$sqlQuery->setLog($zLog);

require_once( PASTELL_PATH . "/lib/authentification/Authentification.class.php");
$authentification = new Authentification();


require_once( PASTELL_PATH . "/lib/droit/RoleUtilisateur.class.php");
$roleUtilisateur = new RoleUtilisateur($sqlQuery);
$roleUtilisateur->setRoleDroit(new RoleDroit());

require_once( PASTELL_PATH. "/lib/document/DocumentTypeFactory.class.php");
$documentTypeFactory = new DocumentTypeFactory();

require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaireFactory.class.php");
$donneesFormulaireFactory = new DonneesFormulaireFactory($documentTypeFactory,WORKSPACE_PATH);

require_once( PASTELL_PATH ."/lib/timestamp/OpensslTSWrapper.class.php");
$opensslTSWrapper = new OpensslTSWrapper(OPENSSL_PATH,$zLog);

require_once( PASTELL_PATH ."/lib/timestamp/SignServer.class.php");
$signServer = new SignServer(SIGN_SERVER_URL,$opensslTSWrapper);

require_once( PASTELL_PATH . "/lib/journal/Journal.class.php");
$id_u_journal = 0;
if ($authentification->isConnected()) {
	$id_u_journal = $authentification->getId();
}
$journal = new Journal($signServer,$sqlQuery,$id_u_journal);

require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/util.php");

define("DATABASE_FILE", PASTELL_PATH."/installation/pastell.bin");


$objectInstancier = new ObjectInstancier();
$objectInstancier->versionFile = __DIR__."/../version.txt";
$objectInstancier->revisionFile = __DIR__."/../revision.txt";
$objectInstancier->SQLQuery = $sqlQuery;
$objectInstancier->workspacePath = WORKSPACE_PATH;

$versionning = $objectInstancier->Versionning;


