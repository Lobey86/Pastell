<?php
require_once( __DIR__ . "/../connecteur/Megalis.class.php");

class MegalisSupprime extends ActionExecutor{
	
	public function go(){
		
		$megalis =  $this->getConnecteur('Megalis');
				
		$donneesFormulaire = $this->getDonneesFormulaireFactory()->get($this->id_d,'megalis');
		$archive = $donneesFormulaire->get('archive');
		$filename = $archive[0];
		
		if (! $megalis->delete($filename)){
			$message = "Erreur lors de la supression de $filename";
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
