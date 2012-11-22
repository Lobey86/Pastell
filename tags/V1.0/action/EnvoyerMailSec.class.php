<?php 

require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/lib/document/DocumentEmail.class.php");
require_once( PASTELL_PATH . "/lib/helper/mail_validator.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireGroupe.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireRoleSQL.class.php");

class EnvoyerMailSec extends ActionExecutor {
	
	private $collectiviteProperties;
	private $collectiviteForm;
	private $zenMail;
	private $message;
	private $documentEmail;
	
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
	
	private function sendEmail($to,$type){
		if ($this->documentEmail->getKey($this->id_d,$to)){
			return;
		}
		$key = $this->documentEmail->add($this->id_d,$to,$type);
		$link = SITE_BASE . "mailsec/index.php?key=$key";
		$this->zenMail->setDestinataire($to);
		$this->zenMail->setContenuText($this->message . "\n" . $link);
		$this->zenMail->send();
		$this->getJournal()->addActionAutomatique(Journal::MAIL_SECURISE,$this->id_e,$this->id_d,'envoi',"Mail sécurisé envoyée à $to");
	}
	
	public function go(){
		
		$annuaireGroupe = new AnnuaireGroupe($this->getSQLQuery(),$this->id_e);
		$annuaireRoleSQL = new AnnuaireRoleSQL($this->getSQLQuery());
		
		$all_ancetre = $this->getEntite()->getAncetreId();
		
		$this->prepareMail();
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		$this->documentEmail = new DocumentEmail($this->getSQLQuery());
		
		foreach(array('to','cc','bcc') as $type){
			$lesMails = get_mail_list($donneesFormulaire->get($type));
			foreach($lesMails as $mail){
				if (preg_match("/^groupe: \"(.*)\"$/",$mail,$matches)){
					$groupe = $matches[1];
					$id_g = $annuaireGroupe->getFromNom($groupe);
					$utilisateur = $annuaireGroupe->getAllUtilisateur($id_g);
					foreach($utilisateur as $u){
						$this->sendEmail($u['email'],$type);
					}
				} elseif(preg_match("/^role: \"(.*)\"$/",$mail,$matches)){
					$role = $matches[1];
					$id_r = $annuaireRoleSQL->getFromNom($this->id_e,$role);
					$utilisateur = $annuaireRoleSQL->getUtilisateur($id_r);
					
					foreach($utilisateur as $u){
						$this->sendEmail($u['email'],$type);
					}
				} elseif(preg_match('/^groupe hérité de (.*): "(.*)"$/',$mail,$matches) || preg_match('/^groupe global: ".*"$/',$mail)) {
					$id_g = $annuaireGroupe->getFromNomDenomination($all_ancetre,$mail);
					$utilisateur = $annuaireGroupe->getAllUtilisateur($id_g);
					foreach($utilisateur as $u){
						$this->sendEmail($u['email'],$type);
					}
				} elseif(preg_match('/^rôle hérité de .*: ".*"$/',$mail,$matches) || preg_match('/^rôle global: ".*"$/',$mail)){
					$id_r = $annuaireRoleSQL->getFromNomDenomination($all_ancetre,$mail);
					$utilisateur = $annuaireRoleSQL->getUtilisateur($id_r);
					foreach($utilisateur as $u){
						$this->sendEmail($u['email'],$type);
					}
					
				} else {
					$this->sendEmail($mail,$type);
				}
			}
		}
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,'envoi', "Le document a été envoyé");
		
		$this->setLastMessage("Le document a été envoyé au(x) personne(s) selectionnée(s)");
		return true;		
	}
}