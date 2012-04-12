<?php 

require_once( PASTELL_PATH . "/lib/system/Asalae.class.php");
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");
require_once( PASTELL_PATH . "/lib/system/HeliosArchivesSEDA.class.php");

class SAEEnvoiHelios extends ActionExecutor {
	
	public function go(){
	
		$collectiviteProperties = $this->getCollectiviteProperties();
	
		$entite = $this->getEntite();
		$entiteInfo = $entite->getInfo();
	
		$authorityInfo = array(
				"sae_id_versant" =>  $collectiviteProperties->get("sae_identifiant_versant"),
				"sae_id_archive" =>  $collectiviteProperties->get("sae_identifiant_archive"),
				"sae_numero_aggrement" =>  $collectiviteProperties->get("sae_numero_agrement"),
				"sae_originating_agency" =>  $collectiviteProperties->get("sae_originating_agency"),
				"name" =>  $entiteInfo['denomination'],
				"siren" => $entiteInfo['siren'],
		);
		
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$transactionsInfo = array(
				'unique_id' => $donneesFormulaire->get('tedetis_transaction_id'),
				'date' => date("Y-m-d"), //TODO
				'description' => 'bla', //TODO
				'pes_retour_description' => 'bla', //TODO
		);
	
		$heliosArchivesSEDA = new HeliosArchiveSEDA("/tmp/");
		$heliosArchivesSEDA->setAuthorityInfo($authorityInfo);
		
		$journalFilename = "journal.xml";
		file_put_contents("/tmp/journal.xml", "<test></test>");
		
		$pes_aller = $donneesFormulaire->get('fichier_pes_signe');
		$pes_aller = $pes_aller[0];
		copy($donneesFormulaire->getFilePath('fichier_pes_signe'),"/tmp/$pes_aller");
		
		$pes_retour = $donneesFormulaire->get('fichier_reponse');
		$pes_retour = $pes_retour[0];
		copy($donneesFormulaire->getFilePath('fichier_reponse'),"/tmp/$pes_retour");
		
		$heliosArchivesSEDA->addFiles($journalFilename,$pes_aller,$pes_retour);
		
		$files_size_in_mo = sprintf("%0.3f",(filesize("/tmp/$pes_aller") + filesize("/tmp/$pes_retour") + filesize("/tmp/$journalFilename"))/1024/1024);
		
		
		$heliosArchivesSEDA->setFileSize($files_size_in_mo);
		
		$archive_path = $heliosArchivesSEDA->getArchive();
		if (! $archive_path){
			$this->setLastMessage("La création de l'archive a échoué : " . $heliosArchivesSEDA->getLastError());
			return false;
		}
		
		$bordereau = $heliosArchivesSEDA->getBordereau($transactionsInfo);
	

		$authorityInfo = array(
							"sae_wsdl" =>  $collectiviteProperties->get("sae_wsdl"),
							"sae_login" =>  $collectiviteProperties->get("sae_login"),
							"sae_password" =>  $collectiviteProperties->get("sae_password"),
							"sae_numero_aggrement" =>  $collectiviteProperties->get("sae_numero_agrement"),				
		);
			
		$asalae = new Asalae($authorityInfo);
		
		$result = $asalae->sendArchive($bordereau,$archive_path);
		
		if (! $result){
			$this->setLastMessage("L'envoi du bordereau a échoué : " . $asalae->getLastError());
			return false;
		} 
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au SAE");
		
		$this->setLastMessage("La transaction à été envoyé au SAE ({$authorityInfo['sae_wsdl']})");
		return true;
	}
} 