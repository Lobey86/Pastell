<?php
/*
 * Exécute une action d'un connecteur global.
 * Ce script peut s'appeler par soit par http (tests), soit par CLI PHP (pour un cron par exemple).
 * @param String id_connecteur id du connecteur global
 * @param String action nom de l'action exécutable sur le connecteur global
 * 
 * Exemple (http) : https://www.pastell.fr/batch/bl-action-connecteur-global.php?id_connecteur=iparapheurbl&action=update-cache-circuits
 * Exemple (CLI)  : /usr/bin/php /var/www/pastell/batch/bl-action-connecteur-global.php id_connecteur=iparapheurbl action=update-cache-circuits
 */
require_once( __DIR__ . "/../web/init.php");
    
if (PHP_SAPI === 'cli') {
    for ($iarg = 1; $iarg < count($argv); $iarg++) {
        $arg = explode('=', $argv[$iarg]);
        $argName = $arg[0];
        if (count($arg) < 2) {
            throw new Exception("Paramètre '$argName' non valorisé");
        }
        if ($argName == 'id_connecteur') {
            $prmIdConnecteur = $arg[1];
        } elseif ($argName == 'action') {
            $prmAction = $arg[1];
        } else {
            throw new Exception("Paramètre '$argName' non pris en charge");
        }
    }
} else {
    $recuperateur = new Recuperateur($_GET);
    $prmIdConnecteur = $recuperateur->get('id_connecteur');
    $prmAction = $recuperateur->get('action');
}

if (!$prmIdConnecteur) {
    throw new Exception("Paramètre \'id_connecteur\' attendu.");
}
if (!$prmAction) {
    throw new Exception("Paramètre \'action\' attendu.");
}

$connecteur = $objectInstancier->ConnecteurEntiteSQL->getGlobal($prmIdConnecteur);
if (!$connecteur) {
    throw new Exception("Connecteur d'id '$prmIdConnecteur' inconnu.");
}

echo "Traitement sur connecteur ({$connecteur['id_ce']}, {$connecteur['libelle']}, $prmAction) : \n";
$result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($connecteur['id_ce'], 0, $prmAction, true, array());
if (!$result) {
    echo $objectInstancier->ActionExecutorFactory->getLastMessage();
    echo "\n";
} else {
    echo "ok\n";
}
?>