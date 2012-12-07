<?php
class PastellControler extends Controler {
	

	public function hasDroitEdition($id_e){
		$droit_ecriture = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),
															"entite:edition",$id_e);
		if ( ! $droit_ecriture ){
			$this->LastError->setLastError("Vous n'avez pas le droit d'édition sur cette entité");
			$this->redirect("/entite/detail.php?id_e=$id_e&page=2");
		}

		if ( $id_e && ! $this->EntiteSQL->getInfo($id_e)){
			$this->LastError->setLastError("L'entité $id_e n'existe pas");
			$this->redirect("/entite/detail.php?id_e=$id_e");
		}
	}
	
}