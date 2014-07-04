<?php
require_once( __DIR__ . "/../../web/init.php");
require_once( __DIR__ . "/../batch/BLBatch.class.php");

$blScript = new BLBatch();

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

