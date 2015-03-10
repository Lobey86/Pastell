<?php

class TedetisEnvoieHelios  extends ActionExecutor {

	public function go(){
		$donneesFormulaire = $this->getDonneesFormulaire();
		if (! $donneesFormulaire->get('envoi_signature') && ! $donneesFormulaire->get('fichier_pes_signe')){
			$fichier_pes = $donneesFormulaire->getFileContent('fichier_pes');
			$file_name = $donneesFormulaire->get('fichier_pes');
			$donneesFormulaire->addFileFromData('fichier_pes_signe',$file_name[0],$fichier_pes);
		}
		
		$tdT = $this->getConnecteur("TdT");
		$tdT->postHelios($this->getDonneesFormulaire());
		$this->addActionOK("Le document a été envoyé au TdT");
		$this->notify($this->action, $this->type,"Le document a été envoyé au TdT");
		
		return true;			
	}
}