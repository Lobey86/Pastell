<?php
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/../batch/BLBatch.class.php");

class BLScriptUpdate extends BLBatch {

    function createConnecteurGlobal($name, $type) {
        global $objectInstancier;
        $id_ce = $objectInstancier->ConnecteurControler->nouveau(0, $name, $name . ' global');
        if (!$objectInstancier->FluxEntiteSQL->isUsed($id_ce)) {
            $id_fe = $objectInstancier->FluxControler->editionModif(0, null, $type, $id_ce);
        }
        return $id_ce;
    }
}

$blScript = new BLScriptUpdate();
$todoList = array();
$roleSQLClass = $objectInstancier->RoleSQL;

///////////////////////////////////////////////////////////////
//             Ajout de l'extension BL insaebl             //
///////////////////////////////////////////////////////////////
// Mise en place de l'extension BL : Connecteur ganeshtdtactesbl
$blScript->trace('Mise en place extension BL Connecteur insaebl : ');
$requeteExtension = "SELECT id_e FROM extension WHERE path = ?";
$ext_insaebl = "/var/www/pastell/extensionbl/insaebl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_insaebl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_insaebl);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

////////////////////////////////////////////////////////////////
//           Ajout du connecteur global insaebl               //
////////////////////////////////////////////////////////////////
$name = 'insaebl';
$id_ce = $objectInstancier->ConnecteurEntiteSQL->getGlobal($name);
if (!$id_ce) {
    $blScript->trace("Création du connecteur global $name : ");
    $id_ce = $blScript->createConnecteurGlobal($name, 'sae');
    $blScript->traceln('OK');
    $todoList[] = 'Compléter le paramétrage du connecteur global ' . $name . ', par IHM';
} else {
    $blScript->trace("Création du connecteur global $name : ");
    $blScript->traceln('DEJA FAIT');
}

////////////////////////////////////////////////////////////////
//           Ajout du connecteur global saeversantbl          //
////////////////////////////////////////////////////////////////
$name = 'saeversantbl';
$id_ce = $objectInstancier->ConnecteurEntiteSQL->getGlobal($name);
if (!$id_ce) {
    $blScript->trace("Création du connecteur global $name : ");
    $id_ce = $blScript->createConnecteurGlobal($name, 'saeversant');
    $blScript->traceln('OK');
    $todoList[] = 'Compléter le paramétrage du connecteur global ' . $name . ', par IHM';
} else {
    $blScript->trace("Création du connecteur global $name : ");
    $blScript->traceln('DEJA FAIT');
}

if ($todoList) {
    $blScript->traceln();
    $blScript->traceln('====> Paramétrages complémentaires à effectuer :');
    foreach ($todoList as $todo) {
        $blScript->traceln('- ' . $todo);
    }
}
