<?php 

require_once( __DIR__ . "/../lib/HeliosArchiveSEDA.class.php");

class SAEEnvoiHelios extends ActionExecutor {
	
	public function go(){
	
		$sae_config = $this->getConnecteurConfigByType('SAE');
		
	
		$entite = $this->getEntite();
		$entiteInfo = $entite->getInfo();
	
		$authorityInfo = array(
				"sae_id_versant" =>  $sae_config->get("sae_identifiant_versant"),
				"sae_id_archive" =>  $sae_config->get("sae_identifiant_archive"),
				"sae_numero_aggrement" =>  $sae_config->get("sae_numero_agrement"),
				"sae_originating_agency" =>  $sae_config->get("sae_originating_agency"),
				"name" =>  $entiteInfo['denomination'],
				"siren" => $entiteInfo['siren'],
		);
		
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		//TODO : je n'ai pas d'information là-dessus
		$transactionsInfo = array(
				'unique_id' => $donneesFormulaire->get('tedetis_transaction_id'),
				'date' => date("Y-m-d"), 
				'description' => 'bla', 
				'pes_retour_description' => 'bla', 
		);
	
		$heliosArchivesSEDA = new HeliosArchiveSEDA("/tmp/");
		$heliosArchivesSEDA->setAuthorityInfo($authorityInfo);
		
		$pes_aller = $donneesFormulaire->get('fichier_pes_signe');
		$pes_aller = $pes_aller[0];
		copy($donneesFormulaire->getFilePath('fichier_pes_signe'),"/tmp/$pes_aller");
		
		$pes_retour = $donneesFormulaire->get('fichier_reponse');
		$pes_retour = $pes_retour[0];
		copy($donneesFormulaire->getFilePath('fichier_reponse'),"/tmp/$pes_retour");
		
		$heliosArchivesSEDA->addFiles($pes_aller,$pes_retour);
		
		$files_size_in_mo = sprintf("%0.3f",(filesize("/tmp/$pes_aller") + filesize("/tmp/$pes_retour") )/1024/1024);
		
		
		$heliosArchivesSEDA->setFileSize($files_size_in_mo);
		
		$archive_path = $heliosArchivesSEDA->getArchive();
		if (! $archive_path){
			$this->setLastMessage("La création de l'archive a échoué : " . $heliosArchivesSEDA->getLastError());
			return false;
		}
		
		$bordereau = $heliosArchivesSEDA->getBordereau($transactionsInfo);
	
		$sae = $this->getConnecteur('SAE');
		
		
		$result = $sae->sendArchive($bordereau,$archive_path);
		
		if (! $result){
			$this->setLastMessage("L'envoi du bordereau a échoué : " . $sae->getLastError());
			return false;
		} 
		
		$this->addActionOK("La transaction à été envoyé au SAE ({$authorityInfo['sae_wsdl']})");
		return true;
	}
} 