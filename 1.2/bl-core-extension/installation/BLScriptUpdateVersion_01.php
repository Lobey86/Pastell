<?php
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/../batch/BLBatch.class.php");

class BLScriptSpecifique extends BLBatch {
    
    function createConnecteur($idConnecteur, $libelle, $id_e) {
        global $objectInstancier;
        $this->trace('  - Création du connecteur ' . $idConnecteur . ' : ');
        $liste_connecteur = $objectInstancier->ConnecteurEntiteSQL->getAllbyId($idConnecteur);
        $id_ce=0;
        foreach($liste_connecteur as $connecteur) {
            if ($connecteur['id_e']==$id_e) {
                $id_ce = $connecteur['id_ce'];
                break;
            }
        }
        if (!$id_ce) {                       
            $id_ce = $objectInstancier->ConnecteurControler->nouveau($id_e, $idConnecteur, $libelle);
            $this->traceln('OK ');            
        } else {
            $this->traceln('DEJA FAIT');
        }
    }
}

$blScript = new BLScriptSpecifique();

$blScript->traceln('Nettoyage des enregistrements faisant référence à des documents inexistants : ');
// Table document_action_entite
$sql = 'DELETE document_action_entite.* FROM document_action_entite';
$sql .= ' INNER JOIN document_action ON document_action_entite.id_a=document_action.id_a';
$sql .= ' LEFT JOIN document ON document_action.id_d = document.id_d';
$sql .= ' WHERE document.id_d is null';
$rowCount = $sqlQuery->getPdo()->exec($sql);
$blScript->traceln('    document_action_entite : ' . $rowCount . ' suppressions.');

// Table document_action
$sql = 'DELETE document_action.* FROM document_action';
$sql .= ' LEFT JOIN document ON document_action.id_d = document.id_d';
$sql .= ' WHERE document.id_d is null';
$rowCount = $sqlQuery->getPdo()->exec($sql);
$blScript->traceln('    document_action : ' . $rowCount . ' suppressions.');

// Mise en place de l'extension BL : Connecteur ganeshtdtactesbl
$blScript->trace('Mise en place extension BL Connecteur ganeshtdtactesbl : ');
$requeteExtension = "SELECT id_e FROM extension WHERE path = ?";
$ext_ganeshtdtactesbl = "/var/www/pastell/extensionbl/ganeshtdtactesbl/";
if (!$sqlQuery->queryOne($requeteExtension, $ext_ganeshtdtactesbl)) {
    $sqlQuery->queryOne("INSERT INTO extension (path) VALUES(?)", $ext_ganeshtdtactesbl);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}


/////////////////////////////////////////////////
// Connecteurs globaux sur entités entreprises //
/////////////////////////////////////////////////
$blScript->trace('Modification du type sur le connecteur global signaturebl -> adm_signature : ');
$sql_select = "SELECT id_ce FROM connecteur_entite WHERE id_e=0 AND type='signature' AND id_connecteur='signaturebl'";
if ($sqlQuery->queryOne($sql_select)) {
    $sql = "UPDATE connecteur_entite SET type = 'adm_signature' WHERE id_e=0 AND id_connecteur='signaturebl'";
    $sqlQuery->queryOne($sql);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->trace('Modification du type sur le connecteur global tdtbl -> adm_tdt : ');
$sql_select = "SELECT id_ce FROM connecteur_entite WHERE id_e=0 AND type='tdt' AND id_connecteur='tdtbl'";
if ($sqlQuery->queryOne($sql_select)) {
    $sql = "UPDATE connecteur_entite SET type = 'adm_tdt' WHERE id_e=0 AND id_connecteur='tdtbl'";
    $sqlQuery->queryOne($sql);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->trace('Modification du type sur le connecteur global fluxbl -> adm_flux : ');
$sql_select = "SELECT id_ce FROM connecteur_entite WHERE id_e=0 AND type='flux' AND id_connecteur='fluxbl'";
if ($sqlQuery->queryOne($sql_select)) {
    $sql = "UPDATE connecteur_entite SET type = 'adm_flux' WHERE id_e=0 AND id_connecteur='fluxbl'";
    $sqlQuery->queryOne($sql);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->trace('Modification du type sur le connecteur global connecteurbl -> adm_connecteur : ');
$sql_select = "SELECT id_ce FROM connecteur_entite WHERE id_e=0 AND type='connecteur' AND id_connecteur='connecteurbl'";
if ($sqlQuery->queryOne($sql_select)) {
    $sql = "UPDATE connecteur_entite SET type = 'adm_connecteur' WHERE id_e=0 AND id_connecteur='connecteurbl'";
    $sqlQuery->queryOne($sql);
    $blScript->traceln('OK');
} else {
    $blScript->traceln('DEJA FAIT');
}

$blScript->traceln('Ajout des connecteurs d\'administration sur les entités entreprises : ');
// Chargement de la liste des entités entreprises
$sql_entreprise = "SELECT id_e, denomination FROM entite WHERE entite_mere = 0 AND id_e <> 0";
$liste_entreprise = $sqlQuery->query($sql_entreprise);
foreach($liste_entreprise as $entreprise) {
    $blScript->traceln('Entite entreprise : ' . $entreprise['denomination']);
    $blScript->createConnecteur('connecteurbl', 'Administration Connecteur BL', $entreprise['id_e']);
    $blScript->createConnecteur('fluxbl', 'Administration Flux BL', $entreprise['id_e']);
    $blScript->createConnecteur('signaturebl', 'Administration Signature BL', $entreprise['id_e']);
    $blScript->createConnecteur('tdtbl', 'Administration TdT BL', $entreprise['id_e']);
}

$blScript->traceln('Ajout du connecteur global entitebl : ');
$blScript->createConnecteur('entitebl', 'entitebl global', 0);
