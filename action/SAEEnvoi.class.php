<?php 
require_once( PASTELL_PATH . "/lib/system/ActesArchivesSEDA.class.php");

class SAEEnvoi extends ActionExecutor {
	
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
		
		if ($donneesFormulaire->getFilePath('aractes')){
			$aractes = file_get_contents($donneesFormulaire->getFilePath('aractes'));
		} else {
			$aractes = "";
		} 
		
		$actesTransactionsStatusInfo = array(
				'transaction_id' => $donneesFormulaire->get('numero_de_lacte'),
				'flux_retour' => $aractes,
				'date' => $donneesFormulaire->get("date_de_lacte"),
		);
	
		
		$acte_nature = $this->getDocumentTypeFactory()->getDocumentType("actes")->getFormulaire()->getField('acte_nature')->getSelect();
		
		$transactionsInfo = array(
				'unique_id' => $donneesFormulaire->get('numero_de_lacte'),
				'subject' => $donneesFormulaire->get('objet'),
				'decision_date' =>  $donneesFormulaire->get("date_de_lacte"),
				'nature_descr' => $acte_nature[$donneesFormulaire->get('acte_nature')],
				'nature_code' => $donneesFormulaire->get('acte_nature'),
				'classification' => $donneesFormulaire->get('classification'),
		);
	
		$actesArchivesSEDA = new ActesArchiveSEDA("/tmp/");
		$actesArchivesSEDA->setAuthorityInfo($authorityInfo);
		
		$filename = $donneesFormulaire->get('arrete');
		$filename = $filename[0];
		copy($donneesFormulaire->getFilePath('arrete'),"/tmp/$filename");
		$actesArchivesSEDA->setActesFileName($filename);
		$actesArchivesSEDA->setTransactionStatusInfo($actesTransactionsStatusInfo);
	
		
		$finfo = new finfo(FILEINFO_MIME);
		if ($donneesFormulaire->get('autre_document_attache')) {
			foreach($donneesFormulaire->get('autre_document_attache') as $i => $annexe){
				copy($donneesFormulaire->getFilePath('autre_document_attache',$i),"/tmp/$annexe");
				$content_type = $finfo->file($donneesFormulaire->getFilePath('autre_document_attache',$i),FILEINFO_MIME_TYPE);
				$actesArchivesSEDA->addAnnexe($annexe,$content_type);
			}
		}
		
		$archive_path = $actesArchivesSEDA->getArchive();
		if (! $archive_path){
			$this->setLastMessage("La création de l'archive a échoué : " . $actesArchivesSEDA->getLastError());
			return false;
		}
		$bordereau = $actesArchivesSEDA->getBordereau($transactionsInfo);

		
		$sae = $this->getConnecteur('SAE');
		
		$result = $sae->sendArchive($bordereau,$archive_path);
		
		if (! $result){
			$this->setLastMessage("L'envoi du bordereau a échoué : " . $sae->getLastError());
			return false;
		} 
		
		$this->getActionCreator()->addAction($this->id_e,$this->id_u,$this->action,"Le document a été envoyé au SAE");
		
		$this->setLastMessage("La transaction à été envoyé au SAE ({$authorityInfo['sae_wsdl']})");
		return true;
	}
} 