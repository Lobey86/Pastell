<?php 
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

class MailSecControler extends PastellControler {
	
	public function annuaireAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e');
		$this->verifDroit($id_e, "annuaire:lecture");
		
		$this->can_edit = $this->hasDroit($id_e,"annuaire:edition");
		
		$annuaire = new Annuaire($this->SQLQuery,$id_e);
		
		$this->listUtilisateur = $annuaire->getUtilisateur();
		
		if ($id_e){
			$this->infoEntite = $this->EntiteSQL->getInfo($id_e);	
		} else  {
			$this->infoEntite = array("denomination"=>"Annuaire global");
			
		}
		$this->id_e = $id_e;
		$this->page= "Carnet d'adresses";
		$this->page_title= $this->infoEntite['denomination'] . " - Carnet d'adresses";
		$this->template_milieu = "MailSecAnnuaire";
		$this->renderDefault();
	}
	
	public function indexAction(){
		$recuperateur = new Recuperateur($_GET);
		$key = $recuperateur->get('key');
		
		$info  = $this->DocumentEmail->getInfoFromKey($key);
		if (! $info ){
			$this->redirect("/mailsec/invalid.php");
		}
		
		$id_e = $this->DocumentEntite->getEntiteWithRole($info['id_d'],'editeur');
		
		$infoEntite = $this->EntiteSQL->getInfo($id_e);
		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType('mailsec-destinataire');
		$formulaire = $documentType->getFormulaire();
		$donneesFormulaire = $this->DonneesFormulaireFactory->get($info['id_d'],'mailsec-destinataire');
		
		$ip = $_SERVER['REMOTE_ADDR'];
		
		if ($donneesFormulaire->get('password') && (empty($_SESSION["consult_ok_{$key}_{$ip}"]))){
			$this->redirect("/mailsec/password.php?key=$key");
		}
		$info  = $this->DocumentEmail->consulter($key,$this->Journal);
		
		$this->afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
		$this->key = $key;
		$this->page= "Mail sécurisé";
		$this->page_title= $infoEntite['denomination'] . " - Mail sécurisé";
		$this->template_milieu = "MailSecIndex";
		$this->renderDefault();
	}
	
	public function passwordAction(){
		$recuperateur = new Recuperateur($_GET);
		$key = $recuperateur->get('key');
		$info  = $this->DocumentEmail->getInfoFromKey($key);
		if (! $info ){
			$this->redirect("/mailsec/invalid.php");
		}
		
		$this->page= "Mail sécurisé";
		$this->page_title= " Mail sécurisé";
		$this->the_key = $key;
		$this->template_milieu = "MailSecPassword";
		$this->renderDefault();
	}
	
	public function groupeListAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e');
		$this->verifDroit($id_e, "annuaire:lecture");
		$this->can_edit = $this->hasDroit($id_e,"annuaire:edition");
		$annuaireGroupe = new AnnuaireGroupe($this->SQLQuery,$id_e);
		$this->listGroupe = $annuaireGroupe->getGroupe();
		
		
		$infoEntite = $this->EntiteSQL->getInfo($id_e);
		if ($id_e == 0){
			$infoEntite = array("denomination"=>"Annuaire global");
		}
		
		$all_ancetre = $this->EntiteSQL->getAncetreId($id_e);
		$this->groupe_herited = $annuaireGroupe->getGroupeHerite($all_ancetre);
		$this->annuaireGroupe = $annuaireGroupe;
		$this->infoEntite = $infoEntite;
		$this->id_e = $id_e;
		$this->page= "Carnet d'adresses";
		$this->page_title= $infoEntite['denomination'] . " - Carnet d'adresses";
		$this->template_milieu = "MailSecGroupeList";
		$this->renderDefault();
	}
	
	public function groupeAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e');
		$id_g = $recuperateur->getInt('id_g');
		$offset = $recuperateur->getInt('offset');
		$this->verifDroit($id_e, "annuaire:lecture");
		$this->can_edit = $this->hasDroit($id_e,"annuaire:edition");
		
		$annuaireGroupe = new AnnuaireGroupe($this->SQLQuery,$id_e);
		$this->infoGroupe = $annuaireGroupe->getInfo($id_g);
		$this->listUtilisateur = $annuaireGroupe->getUtilisateur($id_g,$offset);
		$this->nbUtilisateur = $annuaireGroupe->getNbUtilisateur($id_g);
		
		if ($id_e){
			$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		} else{
			$this->infoEntite = array("denomination"=>"Annuaire global");
		}
		
		$this->id_e = $id_e;
		$this->id_g = $id_g;
		$this->offset = $offset;
		
		$this->page= "Carnet d'adresses";
		$this->page_title= $this->infoEntite['denomination'] . " - Carnet d'adresses";
		
		$this->template_milieu = "MailSecGroupe";
		$this->renderDefault();
	}
	
	public function groupeRoleListAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e');
		$this->verifDroit($id_e, "annuaire:lecture");
		$this->can_edit = $this->hasDroit($id_e,"annuaire:edition");
				
		$this->arbre = $this->RoleUtilisateur->getArbreFille($this->getId_u(),"entite:edition");
		
		$this->listGroupe = $this->AnnuaireRoleSQL->getAll($id_e);
		
		if ($id_e){
			$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		} else {
			$this->infoEntite = array("denomination"=>"Annuaire global");
		}
		
		$all_ancetre = $this->EntiteSQL->getAncetreId($id_e);
		$this->groupe_herited = $this->AnnuaireRoleSQL->getGroupeHerite($all_ancetre);
		$this->id_e = $id_e;
		$this->annuaireRole = $this->AnnuaireRoleSQL;
		$this->page= "Carnet d'adresses";
		$this->page_title= $this->infoEntite['denomination'] . " - Carnet d'adresses";
		$this->template_milieu = "MailSecGroupeRoleList";
		$this->renderDefault();
	}
	
}