<?php

/*
 *   Ces fonctions permettent la détection et la réaction à des demandes d'opération de maintenance, comme
 *   les sauvegardes, les installations de nouvelles versions, etc, qui impliquent notamment de ne pas
 *   accéder à la base de données ou à l'espace de stockage pendant l'opération.
 *  
 *   Le principe retenu est le suivant :
 *   - le traitement de maintenance signale son démarrage à l'application en créant un fichier flag (/tmp/<nom>.lock)
 *   - un fichier de lock par traitement de maintenance; ceci permettra des traitements de maintenance simultanés (peu probable)
 *   - après le dépôt du fichier flag, le traitement de maintenance doit attendre qq minutes (5mn paraissent raisonnables) avant de déclencher l'action. Ceci permet aux traitements applicatifs qui seraient en cours de se terminer.
 *   - l'application doit prendre en compte les signaux de maintenance dans les différents contextes; le message d'erreur
 *    doit notamment être émis au format attendu par l'appelant (html pour client navigateur, json pour client d'api, texte pour cron/upstart)
 */

function isAppLocked() {
    $files = glob('/tmp/*.lock');
    return $files;
}

function appLockedMessage() {
    $texte = 'Application en cours de maintenance.';
    return $texte;
}

function displayAppLocked() {
    $texte = appLockedMessage();
    if (PHP_SAPI === 'cli') {
        echo utf8_encode($texte) . "\n";
    } elseif (preg_match('/^\/web\/api\//', $_SERVER['REQUEST_URI'])) {
        header("Content-type: text/plain");
        $array = array('status' => 'error', 'error-message' => utf8_encode($texte));
        $message = json_encode($array);
        echo $message;
    } else {
        $message = '<H2>' . htmlentities($texte) . '</H2><p/>' . htmlentities('Le service sera rétabli dès que l\'opération sera terminée.');
        echo $message;
    }
}

function displayAppLockedAndDie() {
    displayAppLocked();
    die(1);
}

function throwAppLocked() {
    $message = appLockedMessage();
    throw new Exception($message);
}

if (isAppLocked()) {
    displayAppLockedAndDie();
}
