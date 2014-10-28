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
$roleSQLClass = $objectInstancier->RoleSQL;

//////////////////////////////////////////////////////
// Ajout des droits sur le flux DSNBL au rôle admin //
//////////////////////////////////////////////////////

$blScript->trace('Ajout des droits de lecture sur le flux DSNBL au rôle admin : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'admin' AND droit = 'dsnbl:lecture'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('admin', 'dsnbl:lecture');
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->trace('Ajout des droits d\'édition sur le flux DSNBL au rôle admin : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'admin' AND droit = 'dsnbl:edition'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('admin', 'dsnbl:edition');
    $blScript->traceln('OK');
} else {
   $blScript->traceln('DEJA FAIT');
}
////////////////////////////////////////////////////////////
// Ajout des droits sur le flux DSNBL au rôle adminEntite //
////////////////////////////////////////////////////////////

$blScript->trace('Ajout des droits de lecture sur le flux DSNBL au rôle adminEntite : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'adminEntite' AND droit = 'dsnbl:lecture'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('adminEntite', 'dsnbl:lecture');
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->trace('Ajout des droits d\'édition sur le flux DSNBL au rôle adminEntite : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'adminEntite' AND droit = 'dsnbl:edition'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('adminEntite', 'dsnbl:edition');
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

/////////////////////////////////////////////////////////////
// Ajout des droits sur le flux DSNBL au rôle adminDocument//
/////////////////////////////////////////////////////////////

$blScript->trace('Ajout des droits de lecture sur le flux DSNBL au rôle adminDocument : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'adminDocument' AND droit = 'dsnbl:lecture'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('adminDocument', 'dsnbl:lecture');
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->trace('Ajout des droits d\'édition sur le flux DSNBL au rôle adminDocument : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'adminDocument' AND droit = 'dsnbl:edition'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('adminDocument', 'dsnbl:edition');
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

/////////////////////////////////////////////////////////////
// Ajout des droits sur le flux DSNBL au rôle apiDocument  //
/////////////////////////////////////////////////////////////

$blScript->trace('Ajout des droits lecture sur le flux DSNBL au rôle apiDocument : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'apiDocument' AND droit = 'dsnbl:lecture'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('apiDocument', 'dsnbl:lecture');
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->trace('Ajout des droits d\'édition sur le flux DSNBL au rôle apiDocument : ');
$sql_select = "SELECT * FROM role_droit WHERE role = 'apiDocument' AND droit = 'dsnbl:edition'";
if ($sqlQuery->queryOne($sql_select) == NULL) {
    $roleSQLClass->addDroit('apiDocument', 'dsnbl:edition');
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

///////////////////////////////////////////////////////////////
//             Ajout de l'extension BL netedsnbl             //
///////////////////////////////////////////////////////////////

// Mise en place de l'extension BL : Connecteur ganeshtdtactesbl
$blScript->trace('Mise en place extension BL Connecteur netedsnbl : ');
$requeteExtension = "SELECT id_e FROM extension WHERE path = ?";
$ext_netedsnbl = "/var/www/pastell/extensionbl/netedsnbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_netedsnbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_netedsnbl);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

////////////////////////////////////////////////////////////////
//           Ajout du connecteur global netedsnbl             //
////////////////////////////////////////////////////////////////
$name = 'netedsnbl';
$id_ce = $objectInstancier->ConnecteurEntiteSQL->getGlobal($name);
$blScript->trace("Création du connecteur global $name : ");
if (!$id_ce) {
    $id_ce = $blScript->createConnecteurGlobal($name,'dsn');
    $blScript->traceln('OK');
    $blScript->traceln('Paramétrage du compte concentrateur sur le connecteur global :');
    $data['siret'] = $blScript->read('Siret du concentrateur');
    $data['nom'] = $blScript->read('Nom du concentrateur');
    $data['prenom'] = $blScript->read('Prenom du concentrateur');
    $data['motdepasse']= $blScript->read('Mot de passe du concentrateur');
    $data['service'] = 98;
    $data['range_max'] = 259200;    
    $donneesFormulaire = $objectInstancier->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
    $donneesFormulaire->setTabDataVerif($data);
    $blScript->traceln('Paramétrage du compte concentrateur sur le connecteur global : TERMINE');
} else {
    $blScript->traceln('DEJA FAIT');
}
