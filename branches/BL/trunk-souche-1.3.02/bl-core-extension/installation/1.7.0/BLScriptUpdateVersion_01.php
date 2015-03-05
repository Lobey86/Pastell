<?php

require_once( __DIR__ . "/../../../web/init.php");
require_once( __DIR__ . "/../../batch/BLBatch.class.php");

$blScript = new BLBatch();

$blScript->trace('Nettoyage de la table DOCUMENT_ACTION_ENTITE : ');
/** @var $stmt PDOStatement */
$sql = "DELETE ae.* FROM document_action_entite ae";
$sql .= " LEFT JOIN document_action a ON ae.id_a = a.id_a";
$sql .= " WHERE a.id_a IS NULL";
$stmt = $blScript->sqlPrepare($objectInstancier->SQLQuery, $sql);
$rowCount = $blScript->sqlDelete($stmt);
$blScript->traceln($rowCount === FALSE ? "ERREUR" : "$rowCount ligne(s) supprimée(s)");

$blScript->trace('Création de l\'index FLUX_ENTITE.id_ce : ');
$index_structure = $objectInstancier->SQLQuery->query("SHOW INDEX FROM flux_entite WHERE KEY_NAME = 'id_ce'");
if (!$index_structure) {
    $objectInstancier->SQLQuery->query("ALTER TABLE flux_entite ADD INDEX id_ce (id_ce)");
    $blScript->traceln('OK');
} else {
    $blScript->traceln('déjà fait');
}

$blScript->trace('Suppression de l\'index JOURNAL.message_horodate : ');
$index_structure = $objectInstancier->SQLQuery->query("SHOW INDEX FROM journal WHERE KEY_NAME = 'message_horodate'");
if ($index_structure) {
    $objectInstancier->SQLQuery->query("ALTER TABLE journal DROP INDEX message_horodate");
    $blScript->traceln('OK');
} else {
    $blScript->traceln('déjà fait');
}

$blScript->trace('Création de l\'index JOURNAL.date : ');
$index_structure = $objectInstancier->SQLQuery->query("SHOW INDEX FROM journal WHERE KEY_NAME = 'date'");
if (!$index_structure) {
    $objectInstancier->SQLQuery->query("ALTER TABLE journal ADD INDEX date (date)");
    $blScript->traceln('OK');
} else {
    $blScript->traceln('déjà fait');
}

$blScript->trace('Création de l\'index JOURNAL.id_e : ');
$index_structure = $objectInstancier->SQLQuery->query("SHOW INDEX FROM journal WHERE KEY_NAME = 'id_e'");
if (!$index_structure) {
    $objectInstancier->SQLQuery->query("ALTER TABLE journal ADD INDEX id_e (id_e)");
    $blScript->traceln('OK');
} else {
    $blScript->traceln('déjà fait');
}

$blScript->trace('Création de l\'index JOURNAL.id_d : ');
$index_structure = $objectInstancier->SQLQuery->query("SHOW INDEX FROM journal WHERE KEY_NAME = 'id_d'");
if (!$index_structure) {
    $objectInstancier->SQLQuery->query("ALTER TABLE journal ADD INDEX id_d (id_d)");
    $blScript->traceln('OK');
} else {
    $blScript->traceln('déjà fait');
}

$blScript->trace('Création de l\'index JOURNAL.type : ');
$index_structure = $objectInstancier->SQLQuery->query("SHOW INDEX FROM journal WHERE KEY_NAME = 'type'");
if (!$index_structure) {
    $objectInstancier->SQLQuery->query("ALTER TABLE journal ADD INDEX type (type)");
    $blScript->traceln('OK');
} else {
    $blScript->traceln('déjà fait');
}

