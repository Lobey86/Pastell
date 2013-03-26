<?php

class MegalisSupprime extends ActionExecutor{
	
	public function go(){
		
		$megalis = $this->getGlobalConnecteur('Megalis');
		
		$donneesFormulaire = $this->getDonneesFormulaire();		
		$archive = $donneesFormulaire->get('archive');
		$filename = $archive[0];
		
		if (! $megalis->delete($filename)){
			$message = "Erreur lors de la suppression de $filename";
			$this->getActionCreator()->addAction($this->id_e,$this->id_u,"erreur-supprimer-distant",$message);
			$this->setLastMessage($message);
			$this->getNotificationMail()->notify($this->id_e,$this->id_d,"erreur-supprimer-distant", $this->type,$message);	
			return false;
		}
		
		$message = "Le fichier $filename a été supprimé du serveur distant";
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,"termine",$message);
			
		$this->setLastMessage($message);
		return true; 
	}
}
