<?php 

class InscriptionControler extends PastellControler {
	
	public function citoyenIndexAction(){
		$this->page_title = "Inscription sur Pastell";
		$this->template_milieu = "InscriptionCitoyenIndex";
		$this->renderDefault();
	}
	
	public function citoyenOKAction(){
		$this->page_title = "Inscription en cours";
		$this->template_milieu = "InscriptionCitoyenOK";
		$this->renderDefault();
	}
	
	public function fournisseurIndexAction(){
		$this->page_title = "Inscription sur Pastell";
		$this->template_milieu = "InscriptionFournisseurIndex";
		$this->renderDefault();
	}
	
	public function fournisseurOKAction(){
		$this->page_title = "Inscription en cours";
		$this->template_milieu = "InscriptionFournisseurOK";
		$this->renderDefault();
	}
	
	public function fournisseurMailAction(){
		if (! $this->getId_u()){
			$this->redirect("/connexion/connexion.php");
		}
		$this->infoUtilisateur = $this->Utilisateur->getInfo($this->getId_u());
		$this->page_title = "Inscription en cours de finalisation";
		$this->template_milieu = "InscriptionFournisseurMail";
		$this->renderDefault();
	}
	
}