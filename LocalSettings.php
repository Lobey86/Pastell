<?php 

//Ce fichier contient les valeurs par défaut

if (file_exists(dirname( __FILE__) . "/../LocalSettings.php")){
	//Il est possible d'écraser les valeurs par défaut en 
	//créant un fichier LocalSettings.php dans le répertoire supérieur à ce fichier.
	require_once( dirname( __FILE__) . "/../LocalSettings.php");
}

if (! defined("PASTELL_PATH")){
	define("PASTELL_PATH",dirname(__FILE__) );
}

if ( ! defined("ZEN_PATH")){
	define("ZEN_PATH", PASTELL_PATH . "zenBase");
}


//Emplacement du répertoire pour sauvegarder les fichiers temporaire
if (!defined("WORKSPACE_PATH")){
	define("WORKSPACE_PATH" , dirname(__FILE__) . "/workspace");
}

require_once( ZEN_PATH . "/lib/ZLog.class.php");

//Emplecement du fichier de Log
if (!defined("LOG_FILE")){
	define("LOG_FILE",PASTELL_PATH."/log/pastell.log");
}

//Niveau de Log enregistré
if (!defined("LOG_LEVEL")){
	define("LOG_LEVEL",ZLog::DEBUG);
}

//Définition de la connexion à la base de données
if (!defined("BD_DSN")){
	define("BD_DSN","mysql:dbname=pastell;host=127.0.0.1");
	define("BD_USER","pastell");
	define("BD_PASS","pastell");
}

//Racine du site Pastell
//ex : http://pastell.sigmalis.com/
//ex : http://www.sigmalis.com/pastell/
//Toujours finir l'adresse par un /
if (!defined("SITE_BASE")){
	define("SITE_BASE","http://127.0.0.1/pastell/web/");
}