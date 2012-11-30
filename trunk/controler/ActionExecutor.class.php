<?php
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaireFactory.class.php");
require_once( PASTELL_PATH . "/lib/connecteur/tedetis/TedetisFactory.class.php");

abstract class ActionExecutor {
	
	protected $id_d;
	protected $id_e;
	protected $id_u;
	protected $action;
	protected $id_destinataire;
	protected $from_api;
	
	protected $objectInstancier;
	
	private $lastMessage; 
	
	//$type = type de document
	public function __construct(ObjectInstancier $objectInstancier,$id_d,$id_e,$id_u,$type,$id_destinataire,$action_name,$from_api){
		$this->objectInstancier = $objectInstancier;
		$this->id_d = $id_d;
		$this->id_e = $id_e;		
		$this->id_u = $id_u;
		$this->type = $type;
		$this->id_destinataire = $id_destinataire;	
		$this->action = $action_name;
		$this->from_api = $from_api;
	}
	
	public function getLastMessage(){
		return $this->lastMessage;
	}
	
	public function setLastMessage($message){
		$this->lastMessage = $message;
	}
	
	public function getEntite(){
		static $entite;
		if (! $entite){
			$entite = new Entite($this->getSQLQuery(),$this->id_e);
		}
		return $entite;
	}
	
	public function getActionCreator(){
		static $actionCreator;
		if (! $actionCreator){
			$actionCreator = new ActionCreator($this->getSQLQuery(),$this->getJournal(),$this->id_d);	
		}
		return $actionCreator;	
	}
	
	public function getDonneesFormulaire(){
		static $donneesFormulaire;
		if (! $donneesFormulaire){
			$donneesFormulaire = $this->getDonneesFormulaireFactory()->get($this->id_d,$this->type);
		}
		return $donneesFormulaire;	
	}
	
	
	
	public function getZenMail(){
		return $this->objectInstancier->ZenMail;
	}

	public function getSQLQuery(){
		return $this->objectInstancier->SQLQuery;
	}
	
	public function getDocumentEntite(){
		return $this->objectInstancier->DocumentEntite;
	}
	
	public function getJournal(){
		return $this->objectInstancier->Journal;
	}
	
	public function getDocumentTypeFactory(){
		return $this->objectInstancier->DocumentTypeFactory;
	}

	public function getDocument(){
		return $this->objectInstancier->Document;
	}
	
	public function getDonneesFormulaireFactory(){
		return $this->objectInstancier->DonneesFormulaireFactory;
	}
	
	public function getDocumentActionEntite(){
		return $this->objectInstancier->DocumentActionEntite;
	}
	
	public function getNotificationMail(){
		return $this->objectInstancier->NotificationMail;
	}
	
	public function getGlobalProperties(){
		return $this->objectInstancier->donneesFormulaireFactory->getEntiteFormulaire(0);	
	}
	
	public function getCollectiviteProperties(){
		$id_e_col = $this->objectInstancier->EntiteSQL->getCollectiviteAncetre($this->id_e)?:0;
		return $this->objectInstancier->donneesFormulaireFactory->getEntiteFormulaire($id_e_col);	
	}
	
	public function addAction($id_u,$actionName,$message){
		$this->getActionCreator()->addAction($this->id_e,$id_u,$actionName,$message);
	}
	
	public function notify($actionName,$type,$message){
		$this->getNotificationMail()->notify($this->id_e,$this->id_d,$actionName,$type,$message);
	}

	public function redirect($to){
		if (! $this->from_api) {
			header("Location: $to");
			exit;
		}
		
	}
	
	abstract public function go();
}