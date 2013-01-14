<?php

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
	

	public function __construct(ObjectInstancier $objectInstancier){
		$this->objectInstancier = $objectInstancier;
	}
	
	public function setEntiteId($id_e){
		$this->id_e = $id_e;	
	}
	
	public function setUtilisateurId($id_u){
		$this->id_u = $id_u;
	}
		
	public function setAction($action_name){
		$this->action = $action_name;
	}
		
	public function setConnecteurId($type, $id_ce){
		$this->id_ce = $id_ce;
		$this->type = $type;
	}
	
	public function setDocumentId($type, $id_d){
		$this->id_d = $id_d;
		$this->type = $type;
	}
	
	public function setDestinataireId(array $id_destinataire){
		$this->id_destinataire = $id_destinataire;	
	}
	
	public function setFromApi($from_api){
		$this->from_api = $from_api;
	}
	
	public function getLastMessage(){
		return $this->lastMessage;
	}
	
	public function setLastMessage($message){
		$this->lastMessage = $message;
	}
	
	public function getActionCreator(){
		return new ActionCreator($this->getSQLQuery(),$this->getJournal(),$this->id_d);	
	}
	
	public function getDonneesFormulaire(){
		return $this->getDonneesFormulaireFactory()->get($this->id_d);
	}
	
	public function getFormulaire(){
		$formulaire = $this->objectInstancier->DocumentTypeFactory->getFluxDocumentType($this->type)->getFormulaire();
		$formulaire->addDonnesFormulaire($this->getDonneesFormulaire());
		return $formulaire;
	}
	
	public function getJournal(){
		return $this->objectInstancier->Journal;
	}
	
	public function getZenMail(){
		return $this->objectInstancier->ZenMail;
	}
	
	public function getDonneesFormulaireFactory(){
		return $this->objectInstancier->DonneesFormulaireFactory;
	}
	
	public function getDocumentEntite(){
		return $this->objectInstancier->DocumentEntite;
	}

	public function getDocument(){
		return $this->objectInstancier->Document;
	}
	
	public function getDocumentActionEntite(){
		return $this->objectInstancier->DocumentActionEntite;
	}
	
	public function getDocumentTypeFactory(){
		return $this->objectInstancier->DocumentTypeFactory;
	}

	public function getEntite(){
		static $entite;
		if (! $entite){
			$entite = new Entite($this->getSQLQuery(),$this->id_e);
		}
		return $entite;
	}
	
	public function getSQLQuery(){
		return $this->objectInstancier->SQLQuery;
	}
	
	public function getNotificationMail(){
		return $this->objectInstancier->NotificationMail;
	}
	
	public function getActionName(){
		return $this->getDocumentTypeFactory()->getFluxDocumentType($this->type)->getAction()->getActionName($this->action);
	}
	
	
	/**** Récupération de connecteur ****/
	public function getConnecteurProperties(){
		assert('$this->id_ce');
		return $this->getConnecteurConfig($this->id_ce);
	}
	
	public function getGlobalConnecteur($type){
		return $this->objectInstancier->ConnecteurFactory->getGlobalConnecteur($type);
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
	
	/***** Fonction utilitaire *****/
	
	public function addActionOK($message){
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,$message);
		$this->setLastMessage($message);
	}
	
	public function notify($actionName,$type,$message){
		$this->getNotificationMail()->notify($this->id_e,$this->id_d,$actionName,$type,$message);
	}

	public function redirect($to){
		if (! $this->from_api) {
			header("Location: ".SITE_BASE."$to");
			exit;
		}
	}
	
	abstract public function go();
	
	
	
}