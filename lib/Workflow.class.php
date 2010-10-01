<?php 

require_once( PASTELL_PATH . "/lib/base/ZenMail.class.php");

require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionSQL.class.php");
require_once( PASTELL_PATH . "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH . "/lib/Journal.class.php");
require_once( PASTELL_PATH . "/lib/notification/Notification.class.php");
require_once( PASTELL_PATH . "/lib/flux/FluxActesRH.class.php");

require_once( PASTELL_PATH . "/lib/flux/FluxInscriptionFournisseur.class.php");

class Workflow {
	
	private $journal;
	private $notification;
	private $sqlQuery;
	private $transactionFinder;
	
	public function __construct($sqlQuery){
		$this->sqlQuery = $sqlQuery;
		//TODO permettre d'injecter tous ces nouveau objet...
		$this->journal = new Journal($sqlQuery);
		$zMail = new ZenMail();
		$this->notification = new Notification($sqlQuery,$zMail);
		$this->notification->setJournal($this->journal);
		$this->transactionFinder = new TransactionFinder($sqlQuery);	
	}
	
	
	public function doStep($type_flux,$state_begin,$state_end,$callback){
		
		$allTransaction  = $this->transactionFinder->getTransactionByFluxAndState($type_flux,$state_begin);
		foreach($allTransaction as $infoTransaction){
			echo "Traitement de " . $infoTransaction['id_t']."\n";
			$the_state = $callback($infoTransaction['id_t'],$state_begin,$state_end);
			$this->nextState($infoTransaction['id_t'],$the_state);
		}	
	}
	
	private function nextState($id_t,$state){
		$transaction = new TransactionSQL($this->sqlQuery,$id_t);
		$transaction->setEtat($state);
		$message ="La transaction " . $id_t . " est passé dans l'état ". $state;	
		$this->journal->add(Journal::CHANGEMENT_ETAT, $id_t,$message);
		$this->notification->notifyAll($id_t,$message);
		$transaction->traitementOK();		
	}
	
	
}