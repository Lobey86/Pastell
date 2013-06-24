<?php
class EnvoieSAEChange extends ActionExecutor{
	
	public function go(){	
		$recuperateur = new Recuperateur($_POST);

		$has_information_complementaire = $this->getDonneesFormulaire()->get('has_information_complementaire');
		$info_needed = ( (! $this->getDonneesFormulaire()->get('envoi_tdt')) && 
									$this->getDonneesFormulaire()->get('envoi_sae'));
		
		if ($has_information_complementaire == $info_needed){
			return true;
		}
		
		$this->getDonneesFormulaire()->setData('has_information_complementaire', $info_needed);
		
		if ( $recuperateur->get('suivant') || $recuperateur->get('precedent') || ! $info_needed){
			return;
		}		
		
		$page = $this->getFormulaire()->getTabNumber("Informations complémentaires");
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=$page");
	}
}