<?php 

class SAEEnvoiActes extends ActionExecutor {
	
	public function go(){
		$tmp_folder = $this->objectInstancier->TmpFolder->create();
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		
		$arrete = $donneesFormulaire->copyFile('arrete',$tmp_folder,0,"acte");
		$annexe = $donneesFormulaire->copyAllFiles('autre_document_attache',$tmp_folder,"annexe");
		$ar_actes = $donneesFormulaire->copyFile('aractes',$tmp_folder,0,"aractes");
		
		$acte_nature = $this->getFormulaire()->getField('acte_nature')->getSelect();
		
		
		@ unlink($tmp_folder."/empty");
		
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
			'actes_file_orginal_filename' => $donneesFormulaire->getFileName('arrete',0),
			'annexe_original_filename' => $donneesFormulaire->get('autre_document_attache'),
		);
		
		if ($this->getDonneesFormulaire()->get('has_information_complementaire')){
			$echangePrefecture = $this->getEchangePrefecture($donneesFormulaire,$tmp_folder);
		} else {
			$echangePrefecture = $this->getFromDocument($donneesFormulaire,$tmp_folder);
		}
		
		
		$transactionsInfo = array_merge($transactionsInfo,$echangePrefecture);
		
		
		if ($donneesFormulaire->get("signature")){
			$transactionsInfo['signature'] = $donneesFormulaire->copyAllFiles('signature',$tmp_folder,"signature");
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
	
	public function getEchangePrefecture(DonneesFormulaire $donneesFormulaire,$tmp_folder) {
		$result['echange_prefecture'] = $donneesFormulaire->copyAllFiles('echange_prefecture',$tmp_folder,"document-prefecture");
		$result['echange_prefecture_ar'] = array();
		$result['echange_prefecture_type'] = array();
		if ($donneesFormulaire->get("echange_prefecture_ar")) {
			foreach($donneesFormulaire->get("echange_prefecture_ar") as $i => $ar_name){
				if ($ar_name == "empty"){
					$result['echange_prefecture_ar'][$i] = "empty";
				} else {
					$result['echange_prefecture_ar'][$i] = $donneesFormulaire->copyFile('echange_prefecture_ar', $tmp_folder,$i,"ar-prefecture-$i");
				}
			}		
		}
		
		
		foreach($result['echange_prefecture'] as $i => $echange){
			$result['echange_prefecture_type'][$i] = $donneesFormulaire->get("echange_prefecture_type_$i");
		}
		$result['echange_prefecture_original_filename'] = $donneesFormulaire->get('echange_prefecture');
		return $result;
	}
	
	
	public function getFromDocument(DonneesFormulaire $donneesFormulaire,$tmp_folder){
		$nb_document  = 1;
		$result['echange_prefecture'] = array();
		$result['echange_prefecture_ar'] = array();
		$result['echange_prefecture_type'] = array();
		$result['echange_prefecture_original_filename'] = array();
		
		if ($donneesFormulaire->get('has_courrier_simple')){
			$result['echange_prefecture'][] = $donneesFormulaire->copyFile('courrier_simple', $tmp_folder,0,"document-prefecture-".$nb_document++);
			$result['echange_prefecture_ar'][] = "empty";
			$result['echange_prefecture_type'][] = "2A";
			$result['echange_prefecture_original_filename'][] = $donneesFormulaire->getFileName('courrier_simple',0);
		}
		
		if($donneesFormulaire->get('has_demande_piece_complementaire')){
			$result['echange_prefecture'][] = $donneesFormulaire->copyFile('demande_piece_complementaire', $tmp_folder,0,"document-prefecture-".$nb_document++);
			$result['echange_prefecture_ar'][] = "empty";
			$result['echange_prefecture_type'][] = "3A";
			$result['echange_prefecture_original_filename'][] = $donneesFormulaire->getFileName('demande_piece_complementaire',0);
		}
		
		if($donneesFormulaire->get('has_reponse_demande_piece_complementaire')){
			$result['echange_prefecture'][] = $donneesFormulaire->copyFile('reponse_demande_piece_complementaire', $tmp_folder,0,"document-prefecture-".$nb_document++);
			$result['echange_prefecture_ar'][] = "empty";
			$result['echange_prefecture_type'][] = "3R";
			$result['echange_prefecture_original_filename'][] = $donneesFormulaire->getFileName('reponse_demande_piece_complementaire',0);
			if ($donneesFormulaire->get('reponse_pj_demande_piece_complementaire')) {
				foreach($donneesFormulaire->get('reponse_pj_demande_piece_complementaire') as $i => $filename){
					$result['echange_prefecture'][] = $donneesFormulaire->copyFile('reponse_pj_demande_piece_complementaire', $tmp_folder,$i,"document-prefecture-".$nb_document++);
					$result['echange_prefecture_ar'][] = "empty";
					$result['echange_prefecture_type'][] = "3RB";
					$result['echange_prefecture_original_filename'][] = $donneesFormulaire->getFileName('reponse_pj_demande_piece_complementaire',$i);
				}
			}
		}
		if($donneesFormulaire->get('has_lettre_observation')){
			$result['echange_prefecture'][] = $donneesFormulaire->copyFile('lettre_observation', $tmp_folder,0,"document-prefecture-".$nb_document++);
			$result['echange_prefecture_ar'][] = "empty";
			$result['echange_prefecture_type'][] = "4A";
			$result['echange_prefecture_original_filename'][] = $donneesFormulaire->getFileName('lettre_observation',0);
		}
		
		if($donneesFormulaire->get('has_reponse_lettre_observation')){
			$result['echange_prefecture'][] = $donneesFormulaire->copyFile('reponse_lettre_observation', $tmp_folder,0,"document-prefecture-".$nb_document++);
			$result['echange_prefecture_ar'][] = "empty";
			$result['echange_prefecture_type'][] = "4R";
			$result['echange_prefecture_original_filename'][] = $donneesFormulaire->getFileName('reponse_lettre_observation',0);
		}
		
		if($donneesFormulaire->get('has_defere_tribunal_administratif')){
			$result['echange_prefecture'][] = $donneesFormulaire->copyFile('defere_tribunal_administratif', $tmp_folder,0,"document-prefecture-".$nb_document++);
			$result['echange_prefecture_ar'][] = "empty";
			$result['echange_prefecture_type'][] = "5A";
			$result['echange_prefecture_original_filename'][] = $donneesFormulaire->getFileName('defere_tribunal_administratif',0);
		}
		
		return $result;
	}
	
	
} 