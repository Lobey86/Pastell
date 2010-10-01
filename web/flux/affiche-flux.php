<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");

require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");
require_once( PASTELL_PATH . "/include/TransactionListe.class.php");
require_once( PASTELL_PATH ."/lib/flux/Flux.class.php");
require_once( PASTELL_PATH ."/lib/flux/FluxFactory.class.php");

require_once (PASTELL_PATH . "/lib/document/Document.class.php");

$recuperateur = new Recuperateur($_GET);
$theFlux = $recuperateur->get('flux');
$siren = $recuperateur->get('siren');


$flux = FluxFactory::getInstance($theFlux);


$transactionFinder = new TransactionFinder($sqlQuery);
$transactionFinder->setAllInfo();

$transactionFinder->setSiren($siren);

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


if ( $flux->canCreate($infoEntite['type']) && $theFlux){
	$nouveau_bouton_url = "flux/nouveau.php?flux=$theFlux";
}

$document = new Document($sqlQuery);

include( PASTELL_PATH ."/include/haut.php");
?>


<div class="box_contenu clearfix">

<h2>Brouillons</h2>
				
<ul>
	<?php foreach($document->getAll($theFlux) as $f) : ?>
		<li><a href='document/detail.php?id_d=<?php echo $f['id_d']?>'><?php echo $f['id_d']?></a></li>
	<?php endforeach;?>
</ul>
</div>

<div class="box_contenu clearfix">


<h2>Documents envoyés</h2>

<?php if (count($transactionFinder->getCountResult())): ?>
<?php $transactionListe->affiche($transactionFinder);?>
<?php else :?>
<div class='box_info'><p>Aucun message de type  « <?php echo FluxFactory::getTitre($theFlux) ?> ».</p></div>
<?php endif;?>
</div>
<?php
include( PASTELL_PATH ."/include/bas.php");
