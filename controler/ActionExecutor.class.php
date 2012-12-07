<?php
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaireFactory.class.php");

abstract class ActionExecutor {
	
	protected $id_d;
	protected $id_e;
	protected $id_u;
	protected $action;
	protected $id_destinataire;
	protected $from_api;
	protected $id_ce;
	
	protected $objectInstancier;
	
	private $lastMessage; 
	
	//$type = type de document
	public function __construct(ObjectInstancier $objectInstancier,
									$id_e,$id_u,$type,
									$id_destinataire,$action_name,$from_api
									
									){
		$this->objectInstancier = $objectInstancier;
		$this->id_e = $id_e;	
		$this->id_u = $id_u;
		$this->type = $type;
		$this->id_destinataire = $id_destinataire;	
		$this->action = $action_name;
		$this->from_api = $from_api;
	}
	
	public function setConnecteurId($id_ce){
		$this->id_ce = $id_ce;
	}
	
	public function setEntite($id_e){
		$this->id_e = $id_e;	
	}
	
	public function setDocumentId($id_d){
		$this->id_d = $id_d;
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
		return new ActionCreator($this->getSQLQuery(),$this->getJournal(),$this->id_d);	
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
	
	public function getConnecteurProperties(){
		assert('$this->id_ce');
		return $this->getConnecteurConfig($this->id_ce);
	}
	
	public function getGlobalConnecteur($type){
		$dispo = $this->objectInstancier->ConnecteurEntiteSQL->getDisponible(0,$type);
		return $this->objectInstancier->ConnecteurFactory->getConnecteurById($dispo[0]['id_ce']);
	}
	
	public function getMyConnecteur(){
		assert('$this->id_ce');
		return $this->objectInstancier->ConnecteurFactory->getConnecteurById($this->id_ce);
	}
	
	public function getConnecteur($type_connecteur){
		return $this->objectInstancier->ConnecteurFactory->getConnecteurByType($this->id_e,$this->type,$type_connecteur);
	}
	
	public function getConnecteurConfigByType($type_connecteur){
		$connecteur_info = $this->objectInstancier->FluxEntiteSQL->getConnecteur($this->id_e,$this->type,$type_connecteur);
		if (! $connecteur_info){
			throw new Exception("Aucun connecteur $type_connecteur n'est défini pour le flux {$this->type}");
		}
		return $this->objectInstancier->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($connecteur_info['id_ce']);		
	}
	
	public function getConnecteurConfig($id_ce){
		return $this->objectInstancier->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
	}
	
	public function addAction($id_u,$actionName,$message){
		$this->getActionCreator()->addAction($this->id_e,$id_u,$actionName,$message);
	}

	public function addActionOK($message){
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,$message);
		$this->setLastMessage($message);
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