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
$roleUtilisateur = new RoleUtilisateur($sqlQuery);
$roleUtilisateur->setRoleDroit(new RoleDroit());

require_once( PASTELL_PATH. "/lib/document/DocumentTypeFactory.class.php");
$documentTypeFactory = new DocumentTypeFactory();

require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaireFactory.class.php");
$donneesFormulaireFactory = new DonneesFormulaireFactory($documentTypeFactory,WORKSPACE_PATH);

require_once( PASTELL_PATH . "/lib/utilisateur/Utilisateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");

$id_u_journal = 0;

if ($authentification->isConnected()) {
	$utilisateur = new Utilisateur($sqlQuery,$authentification->getId());
	$infoUtilisateur = $utilisateur->getInfo();
	if (! $infoUtilisateur['mail_verifie']) {
		header("Location: " . SITE_BASE . "inscription/fournisseur/inscription-mail-en-cours.php");
		exit;
	}
	$id_u_journal = $authentification->getId();
}

require_once( PASTELL_PATH ."/lib/timestamp/OpensslTSWrapper.class.php");
$opensslTSWrapper = new OpensslTSWrapper(OPENSSL_PATH,$zLog);

require_once( PASTELL_PATH ."/lib/timestamp/SignServer.class.php");
$signServer = new SignServer(SIGN_SERVER_URL,$opensslTSWrapper);

require_once( PASTELL_PATH . "/lib/journal/Journal.class.php");
$journal = new Journal($signServer,$sqlQuery,$id_u_journal);

