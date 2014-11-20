<?php 
require_once(__DIR__."/DefaultSettings.php");
set_include_path(	__DIR__ . "/pastell-core/" . PATH_SEPARATOR .
				 	__DIR__ . "/lib/" . PATH_SEPARATOR . 
				 	__DIR__ . "/model" . PATH_SEPARATOR . 
				 	__DIR__ . "/controler" . PATH_SEPARATOR . 
				 	__DIR__ . "/connecteur-type" . PATH_SEPARATOR .
				 	get_include_path() 
				 	);

if ( ! function_exists('pastell_autoload')) {
	//PHPUnit est incompatible avec cette fonction d'autoload (warning + lancement d'exception)
	//Note : c'est un peu à la one-again, il faudrait sans doute refactorer cette fonction pour qu'elle
	//fonctionne dans tous les cas.
	function pastell_autoload($class_name) {	
		@ $result = include($class_name . '.class.php');
		if ( ! $result ){
			//throw new Exception("Impossible de trouver $class_name");
			return false;
		}
		return true;
	}
}

spl_autoload_register('pastell_autoload');

if(php_sapi_name() != "cli") {
	ini_set("session.cookie_httponly", 1);
	session_start();
}

if (! function_exists('apc_fetch')){
	function apc_fetch(){}
	function apc_store(){}
}


require_once( PASTELL_PATH . "/lib/util.php");
require_once("Connecteur.class.php");

$objectInstancier = new ObjectInstancier();
$objectInstancier->Timer = new Timer();

$objectInstancier->manifest_file_path = __DIR__."/manifest.yml";
$objectInstancier->temp_directory = sys_get_temp_dir();


$objectInstancier->workspacePath = WORKSPACE_PATH;
$objectInstancier->template_path = TEMPLATE_PATH;
$objectInstancier->api_definition_file_path = __DIR__ . "/pastell-core/api-definition.yml";

$objectInstancier->opensslPath = OPENSSL_PATH;

$objectInstancier->bd_dsn = BD_DSN;
$objectInstancier->bd_user = BD_USER;
$objectInstancier->bd_password = BD_PASS;

$objectInstancier->upstart_touch_file = UPSTART_TOUCH_FILE;
$objectInstancier->upstart_time_send_warning = UPSTART_TIME_SEND_WARNING;

$objectInstancier->open_id_url_callback = SITE_BASE."/connexion/openid-pastell.php";
 
$id_u_journal = 0;
if ($objectInstancier->Authentification->isConnected()) {
	$id_u_journal = $objectInstancier->Authentification->getId();
}
$objectInstancier->Journal->setId($id_u_journal);

try {
	$horodateur = $objectInstancier->ConnecteurFactory->getGlobalConnecteur('horodateur');
	if ($horodateur){
		$objectInstancier->Journal->setHorodateur($horodateur);
	}
} catch (Exception $e){}

$sqlQuery = $objectInstancier->SQLQuery;
$lastError = $objectInstancier->LastError;
$lastMessage = $objectInstancier->LastMessage;
$authentification = $objectInstancier->Authentification;
$journal = $objectInstancier->Journal;
$documentTypeFactory = $objectInstancier->DocumentTypeFactory;
$donneesFormulaireFactory = $objectInstancier->DonneesFormulaireFactory;
$roleUtilisateur = $objectInstancier->RoleUtilisateur;

define("DATABASE_FILE", PASTELL_PATH."/installation/pastell.bin");




