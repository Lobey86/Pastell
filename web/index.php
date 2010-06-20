<?php
require_once(dirname(__FILE__)."/init-authenticated.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");
require_once( PASTELL_PATH . "/include/TransactionListe.class.php");
require_once( PASTELL_PATH ."/lib/flux/Flux.class.php");

$transactionFinder = new TransactionFinder($sqlQuery);
if ( ! $authentification->isAdmin()){
	$transactionFinder->setSiren($infoEntite['siren']);
}
$transactionFinder->setAllInfo();


$transactionListe = new TransactionListe();
$transactionListe->showTypeFlux();

if (! $entite || $infoEntite['type'] == Entite::TYPE_COLLECTIVITE ){
	$transactionListe->setAfficheEtatAvance();
}

$page='index';
$page_title='Bienvenue sur Pastell';

include( PASTELL_PATH ."/include/haut.php");

if ($infoEntite && $infoEntite['etat'] != Entite::ETAT_VALIDE) : 
?>
<div class='box_alert'><p>Attention, vous devez valider votre inscription en remplissant 

<a href='inscription-fournisseur/index.php'>vos informations !</a> 

</p></div>
<?php endif; ?>

<div class="box_contenu clearfix">

<h2>Liste des dernières transactions traitées</h2>

<?php 
$transactionListe->affiche($transactionFinder);
?>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
