<?php 

class SAEEnvoiHelios extends ActionExecutor {
	
	public function go(){
		$tmp_folder = $this->objectInstancier->TmpFolder->create();
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		$pes_aller = $donneesFormulaire->copyFile('fichier_pes_signe',$tmp_folder);
		$pes_retour = $donneesFormulaire->copyFile('fichier_reponse',$tmp_folder);
		
		$transactionsInfo = array(
				'unique_id' => $donneesFormulaire->get('tedetis_transaction_id'),
				'date' => date("Y-m-d"), 
				'description' => 'inconnu', 
				'pes_retour_description' => 'inconnu', 
				'pes_aller' => $pes_aller,
				'pes_retour' => $pes_retour,
				'pes_description' => 'inconnu',
		);
		
		
		$heliosSEDA = $this->getConnecteur('Bordereau SEDA');
		$bordereau = $heliosSEDA->getBordereau($transactionsInfo);
		
		$sae = $this->getConnecteur('SAE');
		$archive_path = $sae->generateArchive($bordereau,$tmp_folder);
		
		$result = $sae->sendArchive($bordereau,$archive_path);
		$this->objectInstancier->TmpFolder->delete($tmp_folder);
		
		if (! $result){
			$this->setLastMessage("L'envoi du bordereau a échoué : " . $sae->getLastError());
			return false;
		} 
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au SAE");
		
		$this->setLastMessage("La transaction à été envoyé au SAE ");
		return true;
	}
} 