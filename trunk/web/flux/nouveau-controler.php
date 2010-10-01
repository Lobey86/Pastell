<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/base/PasswordGenerator.class.php");
require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");

require_once( PASTELL_PATH . "/lib/FileUploader.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionCreator.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionSQL.class.php");
require_once( PASTELL_PATH . "/lib/transaction/message/MessageSQL.class.php");
require_once( PASTELL_PATH . "/lib/flux/message/MessageFactory.class.php");
require_once( PASTELL_PATH . "/lib/flux/FluxFactory.class.php");
require_once( PASTELL_PATH . "/lib/flux/Flux.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/Journal.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");


//Récupération des données
$recuperateur = new Recuperateur($_POST);
$flux = $recuperateur->get('flux');
$destinataire = $recuperateur->get('destinataire',array());
$message = $recuperateur->get('message',"");
$objet =  $recuperateur->get('objet',"");
$message_type =  $recuperateur->get('message_type');

$id_t = $recuperateur->get('id_t');

$fileUploader = new FileUploader($_FILES);

//Création de la transaction
$transaction = new TransactionSQL($sqlQuery,$id_t);
$info = $transaction->getInfo();

if (! $id_t ){
	$transactionCreator = new TransactionCreator($sqlQuery,new PasswordGenerator());
	$id_t = $transactionCreator->getNewTransactionNum();
	$transaction = new TransactionSQL($sqlQuery,$id_t);
}

if (!$info){	
	$transaction->create($flux,Flux::STATE_INIT,$objet);
	$transaction->addRole($infoEntite['siren'],"emmeteur");
	foreach($destinataire as $dest){
		$transaction->addRole($dest,"detinataire");
	}
	$transaction = new TransactionSQL($sqlQuery,$id_t);
	$info = $transaction->getInfo();
} 

//Création du message
//TODO (le cas échéant ?)
$messageSQL = new MessageSQL($sqlQuery);
$id_m = $messageSQL->create($id_t,$message_type,$infoEntite['siren'],$message);

$message = MessageFactory::getInstance($message_type);
if ($message->getFormulaire()){
	$formulaire_file = $message->getFormulaire();
	$formulaire = new Formulaire( PASTELL_PATH ."/form/".$formulaire_file);
	
	$donneesFormulaire = new DonneesFormulaire( WORKSPACE_PATH  . "/$id_t.yml");
	$donneesFormulaire->setFormulaire($formulaire);
		
	$formulaire->setTabNumber(0);
	
	$donneesFormulaire->save($recuperateur,$fileUploader);

	foreach ($donneesFormulaire->getAllRessource() as $ressource){
		$messageSQL->addRessource($id_m,$ressource['url'],$ressource['type']);
	}
		
} else {

	//TODO : pas le cas standard !
	foreach($fileUploader->getAll() as $filename => $orig_filename){
		$url = WORKSPACE_PATH . "/$id_t" . "_" . $filename;
		$fileUploader->save($filename,$url);
		$messageSQL->addRessource($id_m,$url,"file",$orig_filename);
	}
}

//Ajout des destinataire au message (TODO en double avec les règle de la transaction ? )
foreach($destinataire as $dest){
	$messageSQL->addDestinataire($id_m,$dest);
}

$theFlux = FluxFactory::getInstance($info['type']);
$next_step = $theFlux->getNextState($info['etat']);


//TODO Validation des fournisseur
if ($info['type'] == FluxInscriptionFournisseur::TYPE) {
	$siren = $transaction->getRole("emmeteur");
	$fournisseur = new Entite($sqlQuery,$siren);
	if ($message->getType() == 'inscription_accepter'){
		$fournisseur->setEtat(Entite::ETAT_VALIDE);	
		$next_step = FluxInscriptionFournisseur::STATE_ACCEPT;
	}
	if ($message->getType() == 'inscription_refuser'){
		$fournisseur->setEtat(Entite::ETAT_REFUSER);	
		$next_step = FluxInscriptionFournisseur::STATE_REFUS;
	}
	
}

//TODO Traitement des notification, enregistrement dans le journal
/*if ( $next_step ){
	$transaction->setEtat($next_step);
	
	$message ="La transaction " . $id_t . " est passée dans l'état $next_step";
	$journal = new Journal($sqlQuery);
	
	$zMail = new ZenMail($zLog);
	$notification = new Notification($sqlQuery,$zMail);
	$notification->setJournal($journal);
	
	$journal->add(Journal::CHANGEMENT_ETAT, $id_t,$message);
	$notification->notifyAll($id_t,$message);
	$transaction->traitementOK();
}*/


header("Location: " . SITE_BASE . "flux/detail-transaction.php?id_t=$id_t");
