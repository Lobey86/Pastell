<?php 

require_once( PASTELL_PATH . "/lib/helper/mail_validator.php");

class EnvoyerMailSec extends ActionExecutor {
	
	private $zenMail;
	private $message;
	private $documentEmail;
	

	private function prepareMail(){		
		$mailsec_config = $this->getConnecteurConfigByType('mailsec');
		
		$this->zenMail = $this->getZenMail();
		$this->zenMail->setEmmeteur($mailsec_config->getWithDefault('mailsec_from_description'),$mailsec_config->getWithDefault('mailsec_from'));
		$this->zenMail->setSujet($mailsec_config->getWithDefault('mailsec_subject'));
		$this->message = $mailsec_config->getWithDefault('mailsec_content');
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
		
		$annuaireRoleSQL = $this->objectInstancier->AnnuaireRoleSQL;
		
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