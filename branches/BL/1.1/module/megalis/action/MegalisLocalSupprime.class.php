<?php 

class MegalisLocalSupprime extends ActionExecutor{
	
	public function go(){
		$donneesFormulaire = $this->getDonneesFormulaire();		
		
		$donneesFormulaire->removeFile('archive');
		$donneesFormulaire->removeFile('bordereau');
		$donneesFormulaire->removeFile('fichier_attache');
		
		$message = "Fichiers supprimé sur Pastell";
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,"supprimer-local",$message);
			
		$this->setLastMessage($message);
		return true; 
	}
}
