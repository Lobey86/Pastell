<?php 

class CreerBdc extends ActionExecutor {
	
	public function go(){
		$this->getDonneesFormulaire()->setData('has_bon_de_commande',true);
		$this->addActionOK("Création du bon de commande");
		header("Location: edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page=1&action=modif_bcd");
		exit;
		
	}
	
}