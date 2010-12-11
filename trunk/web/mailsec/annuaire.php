<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:lecture",$id_e)){
	header("Location: ".SITE_BASE."index.php");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$page= "Carnet d'adresse";
$page_title= $infoEntite['denomination'] . " - Carnet d'adresse";

include( PASTELL_PATH ."/include/haut.php");?>
<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=3'>« Administration de <?php echo $infoEntite['denomination']?></a>


<?php include( PASTELL_PATH ."/include/bas.php");
