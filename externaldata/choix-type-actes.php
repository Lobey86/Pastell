<?php

require_once( PASTELL_PATH . "/externaldata/lib/TypeActes.class.php");


$typeActes = new TypeActes(PASTELL_PATH . "/data-exemple/nomenclature.csv");

$page_title = "Choix du type d'Actes";


include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">
<h2>Types d'Actes</h2>
Veuillez sélectionner un type d'Actes :
<?php $typeActes->afficheClassification("document/external-data-controler.php?id_e=$id_e&id_d=$id_d&page=$page&field=$field")?>
</div>
<?php include( PASTELL_PATH ."/include/bas.php");