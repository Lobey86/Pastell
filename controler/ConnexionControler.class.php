<?php
class ConnexionControler extends PastellControler {
	
	public function oublieIdentifiantAction(){
		$this->page="oublie_identifiant";
		$this->page_title = "Oublie des identifiants";
		$this->template_milieu = "ConnexionOublieIdentifiant";
		$this->renderDefault();
	}
	
}