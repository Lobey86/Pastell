<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

$haut = PASTELL_PATH ."/include/haut.php";
$bas = PASTELL_PATH ."/include/bas.php";

require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/transaction/message/MessageRessource.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionSQL.class.php");


$recuperateur = new Recuperateur($_GET);
$page = $recuperateur->get('page');
$id = $recuperateur->get('id');

$messageRessource = new MessageRessource($sqlQuery,$id);
$info = $messageRessource->getInfo();

$donneesFormulaire = new DonneesFormulaire($info['ressource']);

$formulaire = $donneesFormulaire->getFormulaire();
$formulaire->setTabNumber($page);


$transactionSQL = new TransactionSQL($sqlQuery,$info['id_t']);
$infoTransaction = $transactionSQL->getInfo();

$page_title = "Transaction " .  $infoTransaction['id_t'] . " - formulaire ";


include( $haut );
?>
<a href='flux/detail-transaction.php?id_t=<?php echo $infoTransaction['id_t']?>'>« Revenir à la transaction</a>
<br/><br/>
<?php 

$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->afficheTab($page,"flux/detail-formulaire.php?id=$id");
?>
<div class="box_contenu">
<?php 
$afficheurFormulaire->afficheStatic($page,"recuperation-fichier.php?id=$id");
?>
</div>
<?php 
include( $bas );
