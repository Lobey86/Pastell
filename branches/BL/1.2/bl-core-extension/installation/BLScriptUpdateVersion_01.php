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
    
    function suppressionDroitLectureJournal($role) {        
        global $objectInstancier;
        $droit_journal_lecture = 'journal:lecture';
        
        $this->trace("Suppression des droits de lecture du journal au rôle $role : ");
        $sql_select = "SELECT * FROM role_droit WHERE role = ? AND droit = ?";
        if ($objectInstancier->sqlQuery->queryOne($sql_select, $role, $droit_journal_lecture) == NULL) {    
            $this->traceln('DEJA FAIT');
        } else {
            $sql_delete = "DELETE FROM role_droit WHERE role=? AND droit=?";
            $objectInstancier->sqlQuery->queryOne($sql_delete, $role, $droit_journal_lecture);
            $this->traceln('OK');
        }
    }
    
}

$blScript = new BLScriptUpdate();
$todoList = array();
$roleSQLClass = $objectInstancier->RoleSQL;

//////////////////////////////////////////////////
// Suppression des droits de lecture du journal //
//////////////////////////////////////////////////

$blScript->suppressionDroitLectureJournal('adminEntite');
$blScript->suppressionDroitLectureJournal('adminDocument');
$blScript->suppressionDroitLectureJournal('apiDocument');

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

///////////////////////////////////////////////////////////////
//          Ajout de l'extension BL fasttdtactesbl           //
///////////////////////////////////////////////////////////////

// Mise en place de l'extension BL : Connecteur fasttdtactesbl
$blScript->trace('Mise en place extension BL Connecteur fasttdtactesbl : ');
$requeteExtension = "SELECT id_e FROM extension WHERE path = ?";
$ext_fasttdtactesbl = "/var/www/pastell/extensionbl/fasttdtactesbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_fasttdtactesbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_fasttdtactesbl);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

///////////////////////////////////////////////////////////////
//          Affichage des paramétrages complémentaires
//          (à conserver en fin de script)
///////////////////////////////////////////////////////////////

if ($todoList) {
    $blScript->traceln();
    $blScript->traceln('====> Paramétrages complémentaires à effectuer :');
    foreach ($todoList as $todo) {
        $blScript->traceln('- ' . $todo);
    }
}

