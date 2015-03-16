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

/*
 * Merge souche 1.3.02
 */
$blScript->trace('Migration de la structure de la BD vers souche 1.3.02 : ');
$result = $objectInstancier->SQLQuery->query("SHOW TABLES LIKE 'notification_digest'");
if (!$result) {
    $objectInstancier->SQLQuery->query("ALTER TABLE `action_auto_log` CHANGE `etat_source` `etat_source` varchar(64) NOT NULL;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `action_auto_log` CHANGE `etat_cible` `etat_cible` varchar(64) NOT NULL;");
    $objectInstancier->SQLQuery->query("DROP INDEX id_ei ON action_auto_log;");

    $objectInstancier->SQLQuery->query("CREATE  UNIQUE INDEX id_e ON action_auto_log (`id_e`,`id_d`,`first_try`) ;");

    $objectInstancier->SQLQuery->query("CREATE TABLE action_programmee (
        `id_d` varchar(32) NOT NULL,
        `id_e` int(11) NOT NULL,
        `id_u` int(11) NOT NULL,
        `action` varchar(32) NOT NULL
        )  ENGINE=InnoDB  ;");
    $objectInstancier->SQLQuery->query("CREATE TABLE collectivite_fournisseur (
        `id_e_col` int(11) NOT NULL,
        `id_e_fournisseur` int(11) NOT NULL,
        `is_valid` tinyint(1) NOT NULL
        )  ENGINE=InnoDB  ;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `document_entite` CHANGE `last_action` `last_action` varchar(64) NOT NULL;");
    $objectInstancier->SQLQuery->query("CREATE TABLE document_index (
        `id_d` varchar(64) NOT NULL,
        `field_name` varchar(128) NOT NULL,
        `field_value` varchar(128) NOT NULL,
        PRIMARY KEY (`id_d`,`field_name`)
        )  ENGINE=InnoDB  ;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `entite` ADD `is_active` tinyint(1) NOT NULL DEFAULT '1';");
    $objectInstancier->SQLQuery->query("ALTER TABLE `flux_entite` CHANGE `id_ce` `id_ce` int(11) NOT NULL;");

    $objectInstancier->SQLQuery->query("ALTER TABLE `journal` CHANGE `type` `type` int(11) NOT NULL;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `journal` CHANGE `id_e` `id_e` int(11) NOT NULL;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `journal` CHANGE `id_d` `id_d` varchar(16) NOT NULL;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `journal` CHANGE `message` `message` text NOT NULL;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `journal` CHANGE `date` `date` datetime NOT NULL;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `journal` CHANGE `message_horodate` `message_horodate` text NOT NULL;");

    $objectInstancier->SQLQuery->query("ALTER TABLE `notification` CHANGE `action` `action` varchar(64) NOT NULL;");
    $objectInstancier->SQLQuery->query("ALTER TABLE `notification` ADD `daily_digest` tinyint(1) NOT NULL;");
    $objectInstancier->SQLQuery->query("CREATE TABLE notification_digest (
        `id_nd` int(11) NOT NULL AUTO_INCREMENT,
        `mail` varchar(255) NOT NULL,
        `id_e` int(11) NOT NULL,
        `id_d` varchar(32) NOT NULL,
        `action` varchar(32) NOT NULL,
        `type` varchar(32) NOT NULL,
        `message` text NOT NULL,
        PRIMARY KEY (`id_nd`)
        )  ENGINE=InnoDB  ;");
    $blScript->traceln('OK');
} else {
    $blScript->traceln('déjà fait');
}


