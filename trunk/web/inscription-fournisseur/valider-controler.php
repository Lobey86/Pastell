<?php
require_once("init-information.php");

require_once( ZEN_PATH . "/lib/PasswordGenerator.class.php");


require_once( PASTELL_PATH . '/lib/transaction/TransactionCreator.class.php');
require_once( PASTELL_PATH . '/lib/transaction/TransactionSQL.class.php');
require_once( PASTELL_PATH . '/lib/flux/FluxInscriptionFournisseur.class.php');
require_once( PASTELL_PATH . '/lib/transaction/message/MessageSQL.class.php');
require_once( PASTELL_PATH . '/lib/notification/MailNotificationSQL.class.php');

if (! $donneesFormulaire->isValidable()) {
	$lastError->setLastError("Le formulaire n'est pas terminé");
	header("Location: index.php");
	exit;
}

$transactionCreator = new TransactionCreator($sqlQuery,new PasswordGenerator());

$id_t = $transactionCreator->getNewTransactionNum();

$transaction = new TransactionSQL($sqlQuery,$id_t);
$transaction->create(FluxInscriptionFournisseur::TYPE,'poste',"Inscription fournisseur");

$messageSQL = new MessageSQL($sqlQuery);
$id_m = $messageSQL->create($id_t,FluxInscriptionFournisseur::TYPE,$infoEntite['siren'],"");
//TODO ! 
$messageSQL->addDestinataire($id_m,"160641569");

foreach ($donneesFormulaire->getAllRessource() as $ressource){
	$messageSQL->addRessource($id_m,$ressource['url'],$ressource['type']);
}

$transaction->addRole($infoEntite['siren'],"emmeteur");
$transaction->addRole("160641569","detinataire");

$mailNotificationSQL = new MailNotificationSQL($sqlQuery);
$mailNotificationSQL->addNotification($infoEntite['siren'],$infoUtilisateur['email'],"default");

$entite->setEtat(Entite::ETAT_EN_COURS_VALIDATION);

header("Location: valider-ok.php");
