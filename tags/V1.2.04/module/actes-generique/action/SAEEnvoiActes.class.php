<?php 

class SAEEnvoiActes extends ActionExecutor {
	
	public function go(){
		$tmp_folder = $this->objectInstancier->TmpFolder->create();
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$arrete = $donneesFormulaire->copyFile('arrete',$tmp_folder);
		$annexe = $donneesFormulaire->copyAllFiles('autre_document_attache',$tmp_folder);
		$ar_actes = $donneesFormulaire->copyFile('aractes',$tmp_folder);
		
		$acte_nature = $this->getFormulaire()->getField('acte_nature')->getSelect();
		
		$echange_prefecture = $donneesFormulaire->copyAllFiles('echange_prefecture',$tmp_folder);
		$echange_prefecture_ar = $donneesFormulaire->copyAllFiles('echange_prefecture_ar',$tmp_folder);
		
		@ unlink($tmp_folder."/empty");
		
		$echange_prefecture_type = array();
		foreach($echange_prefecture as $i => $echange){
			$echange_prefecture_type[$i] = $donneesFormulaire->get("echange_prefecture_type_$i");
		}
		
		$transactionsInfo = array(
			'numero_acte_collectivite' => $donneesFormulaire->get('numero_de_lacte'),
			'subject' => $donneesFormulaire->get('objet'),
			'decision_date' =>  $donneesFormulaire->get("date_de_lacte"),
			'latest_date' => $donneesFormulaire->get("date_de_lacte"),
			'nature_descr' => $acte_nature[$donneesFormulaire->get('acte_nature')],
			'nature_code' => $donneesFormulaire->get('acte_nature'),
			'classification' => $donneesFormulaire->get('classification'),
			'actes_file' => $arrete,
			'ar_actes' => $ar_actes,
			'annexe' => $annexe,
			'echange_prefecture' => $echange_prefecture,
			'echange_prefecture_ar' => $echange_prefecture_ar,
			'echange_prefecture_type' => $echange_prefecture_type,
		);
		
		if ($donneesFormulaire->get("signature")){
			$transactionsInfo['signature'] = $donneesFormulaire->copyAllFiles('signature',$tmp_folder);
		}
		
		$actesSEDA = $this->getConnecteur('Bordereau SEDA');
		$bordereau = $actesSEDA->getBordereau($transactionsInfo);
		
				
		$sae = $this->getConnecteur('SAE');
		$archive_path = $sae->generateArchive($bordereau,$tmp_folder);
	
		
		$transferId = $sae->getTransferId($bordereau);
		
		$result = $sae->sendArchive($bordereau,$archive_path);
		
		$this->objectInstancier->TmpFolder->delete($tmp_folder);
		
		if (! $result){
			$this->setLastMessage("L'envoi du bordereau a échoué : " . $sae->getLastError());
			return false;
		} 
		
		$donneesFormulaire->setData("sae_transfert_id",$transferId);
		$this->addActionOK("Le document a été envoyé au SAE");
		return true;
	}
} 