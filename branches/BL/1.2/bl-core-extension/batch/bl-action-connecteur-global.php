<?php
/*
 * Exécute une action d'un connecteur global.
 * Ce script peut s'appeler par soit par http (tests), soit par CLI PHP (pour un cron par exemple).
 * @param String id_connecteur id du connecteur global
 * @param String action nom de l'action exécutable sur le connecteur global
 * 
 * Exemple (http) : https://www.pastell.fr/batch/bl-action-connecteur-global.php?id_connecteur=signaturebl&action=update-cache-circuits
 * Exemple (CLI)  : /usr/bin/php /var/www/pastell/batch/bl-action-connecteur-global.php id_connecteur=signaturebl action=update-cache-circuits
 */
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/BLBatch.class.php");
    
$blscript = new BLBatch();
$prmIdConnecteur = $blscript->getArg('id_connecteur');
$prmAction = $blscript->getArg('action');

$id_ce = $objectInstancier->ConnecteurEntiteSQL->getGlobal($prmIdConnecteur);
if (!$id_ce) {
    throw new Exception("Connecteur global d'id '$prmIdConnecteur' inconnu.");
}

$connecteur = $objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
$blscript->traceln("Traitement sur connecteur ({$id_ce}, {$connecteur['libelle']}, $prmAction) :");
$result = $objectInstancier->ActionExecutorFactory->executeOnConnecteur($id_ce, 0, $prmAction, false, array());
if (!$result) {
    $blscript->traceln($objectInstancier->ActionExecutorFactory->getLastMessage(), false);
} else {
    $blscript->traceln("ok");
}
