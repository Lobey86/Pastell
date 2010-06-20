<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( ZEN_PATH . "/lib/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");
require_once( PASTELL_PATH . "/include/TransactionListe.class.php");
require_once( PASTELL_PATH ."/lib/flux/Flux.class.php");
require_once( PASTELL_PATH ."/lib/flux/FluxFactory.class.php");


$recuperateur = new Recuperateur($_GET);
$theFlux = $recuperateur->get('flux');
$siren = $recuperateur->get('siren');


$flux = FluxFactory::getInstance($theFlux);


$transactionFinder = new TransactionFinder($sqlQuery);
$transactionFinder->setAllInfo();
if ( ! $authentification->isAdmin()){
	$transactionFinder->setSiren($infoEntite['siren']);
} else {
	$transactionFinder->setSiren($siren);
}

$transactionFinder->setFlux($theFlux);


$transactionListe = new TransactionListe();

$infoEntite = false;
if ($siren){
	$entite = new Entite($sqlQuery,$siren);
	$infoEntite = $entite->getInfo();
	$titleEntite = $infoEntite['denomination'];
}

if ($infoEntite){
	if ($theFlux){
		$page_title = "Transactions « " .  FluxFactory::getTitre($theFlux) . " » pour " . $infoEntite['denomination'];
	} else {
		$page_title = "Toutes les transactions pour " . $infoEntite['denomination'];
	}
} else {
	if ($theFlux){
	$page_title = "Transactions « " .  FluxFactory::getTitre($theFlux) . " »";
	} else {
		$page_title = "Toutes les transactions";
	}
}
if  (!$theFlux){
	$transactionListe->showTypeFlux();
}


if ($entite && $flux->canCreate($infoEntite['type'])){
	$nouveau_bouton_url = "flux/nouveau.php?flux=$theFlux";
}

include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">
<h2>Tableau de bord</h2>

<?php if (count($transactionFinder->getCountResult())): ?>
<?php $transactionListe->affiche($transactionFinder);?>
<?php else :?>
<div class='box_info'><p>Aucun message de type  « <?php echo FluxFactory::getTitre($theFlux) ?> ».</p></div>
<?php endif;?>
</div>
<?php
include( PASTELL_PATH ."/include/bas.php");
