<?php
class EnvoieCDGChange extends ActionExecutor{
	
	public function go(){	
		$recuperateur = new Recuperateur($_POST);		
		if ( $recuperateur->get('suivant') || $recuperateur->get('precedent')){
			return;
		}		
		if (! $this->getDonneesFormulaire()->get('envoi_cdg')){
			return;
		}
		
		$page = $this->getFormulaire()->getTabNumber("Agent");
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=$page");
	}
	
	
}