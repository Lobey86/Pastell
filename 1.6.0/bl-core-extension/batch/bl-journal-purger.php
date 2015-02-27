<?php

require_once("/var/www/pastell/web/init.php");
require_once(PASTELL_PATH . "/bl-core-extension/batch/BLBatch.class.php");

$blscript = new BLBatch();

$nb_jours_conservation = $blscript->getArg('nb_jours_conservation');

$sql = "DELETE j.* FROM journal j";
$sql .= " LEFT JOIN document d ON j.id_d = d.id_d";
$sql .= " WHERE d.id_d IS NULL";
$sql .= " AND date < DATE_SUB(CURDATE(), INTERVAL ? DAY)";

$objectInstancier->SQLQuery->query($sql, $nb_jours_conservation);


