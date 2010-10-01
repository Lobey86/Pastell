<?php
require_once(dirname(__FILE__)."/init-authenticated.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");
require_once( PASTELL_PATH . "/include/TransactionListe.class.php");
require_once( PASTELL_PATH ."/lib/flux/Flux.class.php");

$transactionFinder = new TransactionFinder($sqlQuery);
	//$transactionFinder->setSiren($infoEntite['siren']);
$transactionFinder->setAllInfo();


$transactionListe = new TransactionListe();
$transactionListe->showTypeFlux();

$transactionListe->setAfficheEtatAvance();

$page='index';
$page_title='Bienvenue sur Pastell';

include( PASTELL_PATH ."/include/haut.php");

?>
<div class="box_contenu clearfix">

<h2>Liste des derni�res transactions trait�es</h2>

<?php 
$transactionListe->affiche($transactionFinder);
?>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
