<?php
class EnvoieIparapheurChange extends ActionExecutor{
	
	public function go(){	
		$recuperateur = new Recuperateur($_POST);		
		if ( $recuperateur->get('suivant') || $recuperateur->get('precedent')){
			return;
		}		
		if (! $this->getDonneesFormulaire()->get('envoi_iparapheur')){
			return;
		}
		
		$page = $this->getFormulaire()->getTabNumber("iParapheur");
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=$page");
	}
	
	
}