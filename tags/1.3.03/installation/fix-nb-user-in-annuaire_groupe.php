<?php
require_once( __DIR__ . "/../web/init.php");


$sql = "SELECT annuaire_groupe_contact.* FROM annuaire_groupe_contact LEFT JOIN annuaire ON annuaire_groupe_contact.id_a=annuaire.id_a WHERE annuaire.id_e IS NULL";

$all = $objectInstancier->sqlQuery->query($sql);

$sql = "DELETE FROM annuaire_groupe_contact WHERE id_a=? AND id_g=?";
foreach($all as $info) {
	$all = $objectInstancier->sqlQuery->query($sql,$info['id_a'],$info['id_g']);
	echo "DELETE {$info['id_a']},{$info['id_g']}\n";
}

echo "DONE\n";