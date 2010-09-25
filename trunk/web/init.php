<?php 
require_once(dirname(__FILE__)."/../DefaultSettings.php");


require_once( PASTELL_PATH . "/lib/base/Timer.class.php");
$timer = new Timer();

session_start();

$zLog = new ZLog(LOG_FILE);		
$zLog->setLogLevel(LOG_LEVEL);

require_once( PASTELL_PATH . "/lib/messageHTML/LastError.class.php");
$lastError = new LastError();

require_once( PASTELL_PATH . "/lib/messageHTML/LastMessage.class.php");
$lastMessage = new LastMessage();

require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");
$sqlQuery = new SQLQuery(BD_DSN,BD_USER,BD_PASS);
$sqlQuery->setLog($zLog);

require_once( PASTELL_PATH . "/lib/authentification/Authentification.class.php");
$authentification = new Authentification();

require_once( PASTELL_PATH . "/lib/util.php");

require_once( PASTELL_PATH . "/lib/droit/RoleUtilisateur.class.php");
$roleUtilisateur = new RoleUtilisateur($sqlQuery,new RoleDroit());