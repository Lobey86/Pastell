<?php 
class FournisseurFactureRenvoi extends ActionExecutor {
	
	public function go(){
		$this->getDonneesFormulaire()->setData('affiche_renvoi', true);
		$this->addActionOK("Edition de la facture pour préparer son renvoi");
		$page = $this->getFormulaire()->getTabNumber("Renvoi");
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=$page");
	}
}