<?php
class EnvoieSAEChange extends ActionExecutor{
	
	public function go(){	
		$recuperateur = new Recuperateur($_POST);

		$has_bordereau = $this->getDonneesFormulaire()->get('has_bordereau');
		$bordereau_needed = ( (! $this->getDonneesFormulaire()->get('envoi_tdt')) && 
									$this->getDonneesFormulaire()->get('envoi_sae'));
		
		if ($has_bordereau == $bordereau_needed){
			return true;
		}
		
		$this->getDonneesFormulaire()->setData('has_bordereau', $bordereau_needed);
		
		if ( $recuperateur->get('suivant') || $recuperateur->get('precedent') || ! $bordereau_needed){
			return;
		}		
		
		$page = $this->getFormulaire()->getTabNumber("Bordereau");
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=$page");
	}
}