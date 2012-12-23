<?php 
 
//Ce fichier contient les valeurs par défaut

if (file_exists( __DIR__ . "/LocalSettings.php")){
	//Il est possible d'écraser les valeurs par défaut en 
	//créant un fichier LocalSettings.php 
	require_once( __DIR__ . "/LocalSettings.php");
}

if (! defined("PASTELL_PATH")){
	define("PASTELL_PATH",__DIR__ ."/");
}

//Emplacement du répertoire pour sauvegarder les fichiers temporaires
//ATTENTION : CE RÉPERTOIRE DOIT ÊTRE ACCESSIBLE EN ECRITURE 
if (!defined("WORKSPACE_PATH")){
	define("WORKSPACE_PATH" , PASTELL_PATH . "/workspace");
}

require_once( PASTELL_PATH . "/lib/ZLog.class.php");

//Emplacement du fichier de Log
if (!defined("LOG_FILE")){
	define("LOG_FILE", __DIR__. "/log/pastell.log");
}

//Niveau de Log enregistré
if (!defined("LOG_LEVEL")){
	define("LOG_LEVEL",ZLog::DEBUG);
}

//Définition de la connexion à  la base de données
if (!defined("BD_DSN")){
	define("BD_DSN","mysql:dbname=pastell;host=127.0.0.1");
}
if (!defined("BD_USER")){
	define("BD_USER","pastell");
}
if (!defined("BD_PASS")){
	define("BD_PASS","pastell");
}

//Définition de la connexion au SignServer
if (! defined("SIGN_SERVER_URL")){
	define("SIGN_SERVER_URL", "http://127.0.0.1/adullact/pastell/web/demo/timestamp-server.php?workerId=1");	
}

//Certificat de signature des timestamps
if (! defined("SIGN_SERVER_CERTIFICATE")){
	define("SIGN_SERVER_CERTIFICATE", PASTELL_PATH . "/data-exemple/timestamp-cert.pem");
}

//Autorité de certification du certificat de timestamp
if (! defined("SIGN_SERVER_CA_CERTIFICATE")){
	define("SIGN_SERVER_CA_CERTIFICATE", PASTELL_PATH . "/data-exemple/autorite-cert.pem");
}

//Attention, il faut une version d'openSSL > 1.0.0a 
if (! defined("OPENSSL_PATH")){
	define("OPENSSL_PATH","/usr/bin/openssl");
}

//Racine du site Pastell
//ex : http://pastell.sigmalis.com/
//ex : http://www.sigmalis.com/pastell/
//Toujours finir l'adresse par un /
if (!defined("SITE_BASE")){
	define("SITE_BASE","http://192.168.1.5/adullact/pastell/web/");
}

if (!defined("AGENT_FILE_PATH")){
	define("AGENT_FILE_PATH","/tmp/agent");
}
if (! defined("PRODUCTION")){
	define("PRODUCTION",false);
}

if (!defined("PLATEFORME_MAIL")){
	define("PLATEFORME_MAIL","pastell@sigmalis.com");
}

if (!defined("UPSTART_TOUCH_FILE")){
	define("UPSTART_TOUCH_FILE",__DIR__."/log/upstart.mtime");
}

if (!defined("AIDE_URL")){
	define("AIDE_URL","aide/index.php");
}

