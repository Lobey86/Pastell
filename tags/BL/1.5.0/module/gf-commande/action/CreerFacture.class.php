<?php 


class CreerFacture extends ActionExecutor {
	
	public function go(){
		$this->getDonneesFormulaire()->setData('has_facture',true);
		$this->addActionOK("Création de la facture");
		header("Location: edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=3&action=modif_facture");
		exit;

	}
	
}