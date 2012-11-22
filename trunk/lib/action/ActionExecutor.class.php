<?php

require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionCreator.class.php");
require_once( PASTELL_PATH. "/lib/document/DocumentTypeFactory.class.php");
require_once( PASTELL_PATH. "/lib/formulaire/DonneesFormulaireFactory.class.php");
require_once( PASTELL_PATH . "/lib/journal/Journal.class.php");
require_once( PASTELL_PATH . "/lib/connecteur/tedetis/TedetisFactory.class.php");


abstract class ActionExecutor {
	
	private $sqlQuery;
	
	protected $id_d;
	protected $id_e;
	protected $id_u;
	protected $action;
	protected $destinataire;
	
	
	private $donneesFormulaire;
	
	
	private $lastMessage; 
	
	private $entite;
	private $documentEntite;
	private $journal;
	private $actionCreator;
	private $collectiviteProperties;
	private $notificationMail;
	private $documentTypeFactory;
	private $document;
	private $donneesFormulaireFactory;
	private $zLog;
	
	private $from_api;
	
	
	public function __construct(ZLog $zLog, SQLQuery $sqlQuery,$id_d,$id_e,$id_u,$type){
		
		$this->zLog = $zLog;
		$this->sqlQuery = $sqlQuery;
		
		$this->id_d = $id_d;
		$this->id_e = $id_e;		
		$this->id_u = $id_u;
		$this->type = $type;
		
	
		$signServer = new SignServer(SIGN_SERVER_URL,new OpensslTSWrapper(OPENSSL_PATH,$zLog));
		
		
		$this->setEntite(new Entite($sqlQuery,$id_e));
		$this->setDocumentEntite(new DocumentEntite($sqlQuery));
		$this->setJournal(new Journal($signServer,$sqlQuery,$id_u));
		$this->setActionCreator(new ActionCreator($sqlQuery,$this->journal,$id_d));		

		
		$this->setDocumentTypeFactory(new DocumentTypeFactory());
		$this->setDocument(new Document($sqlQuery));
		
		$this->setDonneesFormulaireFactory(new DonneesFormulaireFactory($this->getDocumentTypeFactory(),WORKSPACE_PATH));
		$this->setDonneesFormulaire($this->getDonneesFormulaireFactory()->get($id_d,$type));
		
	}
	
	public function setDestinataire(array $destinataire){
		$this->destinataire = $destinataire;
	}
	
	public function setAction($action){
		$this->action = $action;
	}
	
	public function getZLog(){
		return $this->zLog;
	}
	
	public function getSQLQuery(){
		return $this->sqlQuery;
	}

	public function setDonneesFormulaire(DonneesFormulaire $donneesFormulaire){
		$this->donneesFormulaire = $donneesFormulaire;
	}
	
	public function setEntite(Entite $entite){
		$this->entite = $entite;
	}
	
	public function getEntite(){
		return $this->entite;
	}
	
	public function setDocumentEntite(DocumentEntite $documentEntite){
		$this->documentEntite = $documentEntite;
	}
	
	public function getDocumentEntite(){
		return $this->documentEntite;
	}
	
	public function setJournal(Journal $journal){
		$this->journal = $journal;
	}
	
	public function getJournal(){
		return $this->journal;
	}
	
	public function setActionCreator(ActionCreator $actionCreator){
		$this->actionCreator = $actionCreator;
	}
	
	public function getActionCreator(){
		return $this->actionCreator;
	}
	
	public function setDocumentTypeFactory(DocumentTypeFactory $documentTypeFactory){
		$this->documentTypeFactory = $documentTypeFactory;
	}
	
	public function getDocumentTypeFactory(){
		return $this->documentTypeFactory;
	}
	
	
	public function setDocument(Document $document){
		$this->document = $document;
	}
	
	public function getDocument(){
		return $this->document;
	}
	
	public function setDonneesFormulaireFactory(DonneesFormulaireFactory $donneesFormulaireFactory){
		$this->donneesFormulaireFactory = $donneesFormulaireFactory;
	}
	
	public function getDonneesFormulaireFactory(){
		return $this->donneesFormulaireFactory;
	}
	
	
	public function addAction($id_u,$actionName,$message){
		assert('$this->actionCreator');		
		$this->actionCreator->addAction($this->id_e,$id_u,$actionName,$message);
	}
	
	public function setNotificationMail(NotificationMail $notificationMail){
		$this->notificationMail = $notificationMail;
	}
	
	public function getNotificationMail(){
		assert('$this->notificationMail');
		return $this->notificationMail;
	}
	
	public function notify($actionName,$type,$message){
		assert('$this->notificationMail');
		$this->notificationMail->notify($this->id_e,$this->id_d,$actionName,$type,$message);
	}
	
	public function getLastMessage(){
		return $this->lastMessage;
	}
	
	public function setLastMessage($message){
		$this->lastMessage = $message;
	}
	
	public function getDonneesFormulaire(){
		return $this->donneesFormulaire;
	}
	
	public function setCollectiviteProperties(DonneesFormulaire $collectiviteProperties){
		$this->collectiviteProperties = $collectiviteProperties;
	}
	
	public function getCollectiviteProperties(){
		assert('$this->collectiviteProperties');
		return $this->collectiviteProperties;
	}
	
	abstract public function go();
	
	public function setFromAPI(){
		$this->from_api = 1;
	}
	
	public function isFromAPI(){
		return $this->from_api;
	}
	
}