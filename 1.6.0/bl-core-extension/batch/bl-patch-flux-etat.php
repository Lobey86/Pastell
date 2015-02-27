<?php
// Outil permettant de modifier l'état d'un flux. Il permet notamment la reprise du workflow
// lorsque le flux est dans un état d'erreur alors que c'est la configuration qui est en cause.
// Il ne s'exécute qu'en mode shell (php CLI), les paramètres sont saisis.

require_once( __DIR__ . "/../../web/init.php");

$id_d = read('Document id');
$document_entite = $objectInstancier->DocumentEntite->getEntite($id_d);
if (!$document_entite) {
    error('Document inexistant');
}
$id_e = $document_entite[0]['id_e'];
$action = read('Nouvel état');

$allAdminUsers = $objectInstancier->RoleUtilisateur->getAllUtilisateur(0, 'admin');
$id_u = $allAdminUsers[0]['id_u'];

$message = 'app:shell,msg:patch état du flux';
$actionCreator = new ActionCreator($objectInstancier->SQLQuery, $objectInstancier->Journal, $id_d);
$actionCreator->addAction($id_e, $id_u, $action, $message);
outln('Etat modifié');

function read($prompt, $default = null) {
    out($prompt . ' : ');
    $ret = utf8_decode(trim(fgets(STDIN)));
    if (empty($ret)) {
        if (isset($default)) {
            return $default;
        }
        error('Abandon');
        exit(1);
    }
    return $ret;
}

function out($text) {
    echo utf8_encode($text);
}

function outln($text) {
    out($text . "\n");
}

function error($text) {
    outln($text);
    exit(1);
}
