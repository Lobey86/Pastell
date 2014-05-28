<?php 

class SAEEnvoiHelios extends ActionExecutor {
	
	public function go(){
		$tmp_folder = $this->objectInstancier->TmpFolder->create();
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		if (! $donneesFormulaire->get('envoi_signature') && ! $donneesFormulaire->get('fichier_pes_signe')){
			$fichier_pes = $donneesFormulaire->getFileContent('fichier_pes');
			$file_name = $donneesFormulaire->get('fichier_pes');
			$donneesFormulaire->addFileFromData('fichier_pes_signe',$file_name[0],$fichier_pes);
		}
		
		$pes_aller = $donneesFormulaire->copyFile('fichier_pes_signe',$tmp_folder,0,"pes_aller");
		$pes_retour = $donneesFormulaire->copyFile('fichier_reponse',$tmp_folder,0,"pes_retour");
		
		if ($donneesFormulaire->get('iparapheur_historique')){
			$iparapheur_historique = $donneesFormulaire->copyFile('iparapheur_historique',$tmp_folder,0,"iparapheur_historique");
		} else {
			$iparapheur_historique = false;
		}
		
		$transactionsInfo = array(
				'unique_id' => $donneesFormulaire->get('tedetis_transaction_id'),
				'date' => date("Y-m-d"), 
				'description' => 'inconnu', 
				'pes_retour_description' => 'inconnu', 
				'pes_aller' => $pes_aller,
				'pes_retour' => $pes_retour,
				'pes_aller_original_filename' => $donneesFormulaire->getFileName('fichier_pes_signe',0),
				'pes_retour_original_filename' => $donneesFormulaire->getFileName('fichier_reponse',0),
				'pes_description' => 'inconnu',
				'pes_aller_content' => $donneesFormulaire->getFileContent('fichier_pes_signe'),
				'iparapheur_historique' => $iparapheur_historique
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
		
		$transferId = $sae->getTransferId($bordereau);
		$donneesFormulaire->setData("sae_transfert_id",$transferId);
		
		$this->addActionOK("Le document a été envoyé au SAE");
		return true;
	}
} 