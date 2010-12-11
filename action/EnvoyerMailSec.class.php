<?php 

require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/lib/document/DocumentEmail.class.php");

class EnvoyerMailSec extends ActionExecutor {
	
	private $collectiviteProperties;
	private $collectiviteForm;
	private $zenMail;
	private $message;
	
	private function getProperties($propertieName){
		$default = $this->collectiviteForm->getField($propertieName)->getDefault();
		return $this->collectiviteProperties->get($propertieName,$default);
	}
	
	private function prepareMail(){
		$this->collectiviteProperties = $this->getDonneesFormulaireFactory()->get($this->id_e,'collectivite-properties');	
		$this->collectiviteForm = $this->getDocumentTypeFactory()->getDocumentType('collectivite-properties')->getFormulaire();		
		
		$this->zenMail = new zenMail($this->getZLog());
		
		$this->zenMail->setEmmeteur($this->getProperties('mailsec_from_description'),$this->getProperties('mailsec_from'));
		$this->zenMail->setSujet($this->getProperties('mailsec_subject'));
		
		$this->message = $this->getProperties('mailsec_content');
	}
	
	public function go(){
		
		$this->prepareMail();
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$to = $donneesFormulaire->get('to');

		
		$documentEmail = new DocumentEmail($this->getSQLQuery());
		$key = $documentEmail->add($this->id_d,$to);
		
		$link = SITE_BASE . "mailsec/index.php?key=$key";
		
		
		$this->zenMail->setDestinataire($to);
		$this->zenMail->setContenuText($this->message . "\n" . $link);
		$this->zenMail->send();
		$this->getJournal()->addActionAutomatique(Journal::MAIL_SECURISE,$this->id_e,$this->id_d,'envoi',"Mail sécurisé envoyée à $to");
	
		
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'envoi', "Le document a été envoyé");
		
		$this->setLastMessage("Le document a été envoyé au(x) personne(s) selectionnée(s)");
		return true;		
	}
}