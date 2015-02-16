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
$blScript->traceln($rowCount === FALSE ? "ERREUR"  : "$rowCount ligne(s) supprimée(s)");
