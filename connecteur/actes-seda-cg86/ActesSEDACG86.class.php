<?php 

//Conforme aux demandes du CG86
//Document utilisé : profil_actes_juin2012.ods

class ActesSEDACG86  extends SEDAConnecteur {
	
	private $authorityInfo;
	private $seda_config;
	
	public function  setConnecteurConfig(DonneesFormulaire $seda_config){
		$this->authorityInfo = array(
				"identifiant_versant" =>  utf8_encode($seda_config->get("identifiant_versant")),
				"identifiant_archive" =>  utf8_encode($seda_config->get("identifiant_archive")),
				"sae_numero_aggrement" =>  $seda_config->get("numero_agrement"),
				"identifiant_producteur" =>  utf8_encode($seda_config->get("identifiant_producteur")),
				"nom_entite" =>   $seda_config->get('nom_entite'),
				"siren_entite" =>  $seda_config->get('siren_entite'),
		);
		
		$this->seda_config = $seda_config;
	}
	
	
	
	private function getContentType($file_path){
		$finfo = finfo_open(FILEINFO_MIME_TYPE);
		$filetype = finfo_file($finfo,$file_path);
        if(preg_match("/^application.*xml$/",$filetype))
        	$filetype = "text/xml";

		return $filetype;
	}
	
	private function getTransferIdentifier(){
		$last_date = $this->seda_config->get("date_dernier_transfert");
		$numero_transfert = $this->seda_config->get("dernier_numero_transfert");
		
		$date = date('Y-m-d');
		if ($last_date == $date){
			$numero_transfert ++;
		} else {
			$numero_transfert = 1;
		}
		
		$this->seda_config->setData('date_dernier_transfert', $date);
		$this->seda_config->setData('dernier_numero_transfert', $numero_transfert);
		
		return $this->authorityInfo['sae_numero_aggrement'] ."-". $date ."-".$numero_transfert;
	}
	
	private function getLatestDate($transactionsInfo){
		$ar_actes_info = $this->getInfoARActes($transactionsInfo['ar_actes']);
		$date = $ar_actes_info['DateReception'];
		
		if ($transactionsInfo['echange_prefecture_ar']){
			foreach($transactionsInfo['echange_prefecture_ar'] as $echange_ar){
				if (! $echange_ar || basename($echange_ar) == 'empty'){
					continue;
				}
				try {
					$info = $this->getInfoARActes($echange_ar);
					$date = max($date,$info['DateReception']);
				} catch(Exception $e){
					
				}
			}
		}
		
		return $date;
	}
	
	private function checkInformation(array $information){
		$info = array('numero_acte_collectivite','subject','decision_date',
					'nature_descr','nature_code','classification',
					'latest_date','actes_file','ar_actes');		
		foreach($info as $key){
			if (empty($information[$key])){
				throw new Exception("Impossible de générer le bordereau : le paramètre $key est vide. ");
			}
		}
		$info = array('annexe','echange_prefecture','echange_prefecture_ar','echange_prefecture_type');
		foreach($info as $key){
			if (! isset($information[$key])){
				throw new Exception("Impossible de générer le bordereau : le paramètre $key est manquant. ");
			}
		}
		
		$info_sup = array('actes_file_orginal_filename','annexe_original_filename','echange_prefecture_original_filename');
		
		foreach($info_sup as $key){
			if (empty($information[$key])){
				$information[$key] = false;
			}
		}
		return $information;
	}
	
	public function getBordereau(array $transactionsInfo){
		
		$this->checkInformation($transactionsInfo);
		
		$ar_actes_info = $this->getInfoARActes($transactionsInfo['ar_actes']);
		
		$latestDate = $this->getLatestDate($transactionsInfo);
		
		$transactionsInfo['classification_tab'] = explode('.',$transactionsInfo['classification']);
		
		$archiveTransfer = new ZenXML('ArchiveTransfer');
		$archiveTransfer['xmlns'] = "fr:gouv:ae:archive:draft:standard_echange_v0.2";
		$archiveTransfer->Comment = "Transfert d'un acte soumis au contrôle de légalité";
		$archiveTransfer->Date = date('c');//"2011-08-12T11:03:32+02:00";
		$archiveTransfer->TransferIdentifier = $this->getTransferIdentifier();
		$archiveTransfer->TransferIdentifier['schemeAgencyName'] = "Pastell - ADULLACT";
		
		$archiveTransfer->TransferringAgency = "####SAE_ID_VERSANT####";
		$archiveTransfer->ArchivalAgency = "####SAE_ID_ARCHIVE####";
		
		$i = 0;
		foreach(array('ar_actes','actes_file') as $key){
			$archiveTransfer->Integrity[$i] = $this->getIntegrityMarkup($transactionsInfo[$key]);
			$i++;
		}
		foreach($transactionsInfo['annexe'] as $fileName){
			$archiveTransfer->Integrity[$i] = $this->getIntegrityMarkup($fileName);
			$i++;
		}
	
		foreach($transactionsInfo['echange_prefecture'] as $echange_prefecture){
			$archiveTransfer->Integrity[$i] = $this->getIntegrityMarkup($echange_prefecture);
			$i++;
		}
		
		foreach($transactionsInfo['echange_prefecture_ar'] as $echange_prefecture_ar){
			if (! $echange_prefecture_ar){
				continue;
			}
			if (basename($echange_prefecture_ar) == 'empty'){
				continue;
			}
			$archiveTransfer->Integrity[$i] = $this->getIntegrityMarkup($echange_prefecture_ar);
			$i++;
		}
		
		
		$archiveTransfer->Contains->ArchivalAgreement = $this->authorityInfo['sae_numero_aggrement'];
		$archiveTransfer->Contains->ArchivalAgreement['schemeName'] = "Convention de transfert";
		$archiveTransfer->Contains->ArchivalAgreement['schemeAgencyName'] = "S²LOW - ADULLACT";
		
		$archiveTransfer->Contains->ArchivalProfile = "ACTES-S2LOW-v1";
		$archiveTransfer->Contains->ArchivalProfile['schemeName'] = "Profil de données";
		$archiveTransfer->Contains->ArchivalProfile['schemeAgencyName'] = "Profil élaboré par les Archives départementales de la Vienne et mis en oeuvre sur la plate-forme S2LOW.";
		
		$archiveTransfer->Contains->DescriptionLanguage = "fr";
		$archiveTransfer->Contains->DescriptionLanguage['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->DescriptionLevel = "file";
		$archiveTransfer->Contains->DescriptionLevel['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->Name = "Contrôle de légalité : " . $transactionsInfo['nature_descr'] . 
											" du ". $this->authorityInfo['nom_entite'] .", en date du " .
											date('d/m/Y',strtotime($transactionsInfo['decision_date'])) .
											", télétransmis à la Préfecture le " .
											date('d/m/Y',strtotime($ar_actes_info['DateReception'])) .".";
		
		$archiveTransfer->Contains->ContentDescription->CustodialHistory = " Actes dématérialisés soumis au contrôle de légalité télétransmis via la plate-forme S2LOW de l'ADULLACT pour le ".
											$this->authorityInfo['nom_entite'] . 
											" puis transférés sur la plate-forme d'archivage électronique AS@LAE par l'ADULLACT, au moyen de l'outil Pastell. Les données archivées sont structurées selon le schéma métier Actes (Aide au contrôle de légalité dématérialisé) établi par le Ministère de l'intérieur, de l'outre mer et des collectivités territoriales. La description a été établie selon les règles du standard d'échange de données pour l'archivage version 0.2";
			
		$archiveTransfer->Contains->ContentDescription->Description = $transactionsInfo['nature_descr'] . " N° ".$transactionsInfo['numero_acte_collectivite'] . 
										" en date du ". date('d/m/Y',strtotime($transactionsInfo['decision_date'])).
										" portant sur : " . $transactionsInfo['subject'];
		
		$archiveTransfer->Contains->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->LatestDate = date('Y-m-d',strtotime($latestDate));
		$archiveTransfer->Contains->ContentDescription->OldestDate = date('Y-m-d',strtotime($transactionsInfo['decision_date']));

		$archiveTransfer->Contains->ContentDescription->OriginatingAgency = "####SAE_ORIGINATING_AGENCY####";
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordContent = $this->authorityInfo['nom_entite'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference = $this->authorityInfo['siren_entite'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeName'] = "SIRENE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeAgencyName'] = "INSEE";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType = "corpname";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType["listVersionID"] = "edition 2009";
		
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordContent = "Contrôle de légalité";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeName'] = "Thésaurus pour la description et l'indexation des archives locales anciennes, modernes et contemporaines_liste d'autorité Actions";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeAgencyName'] = "Direction des Archives de France";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeDataURI'] = "http://www.archivesdefrance.culture.gouv.fr/gerer/classement/normesoutils/thesaurus/";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeVersionID'] = "version 2009";			
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType = "subject";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType["listVersionID"] = "edition 2009";
		
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordContent = $transactionsInfo['nature_descr'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference = $transactionsInfo['nature_code'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeName'] = "ACTES.codeNatureActe";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeAgencyName'] = "Ministère de l'intérieur, de l'outre mer et des collectivités territoriales";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeVersionID'] = "ACTES V1.4";			
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType = "genreform";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType["listVersionID"] = "edition 2009";
		
		if ($transactionsInfo['classification_tab'][0] != 9 ){
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordContent = $this->getSujetActes($transactionsInfo['classification']);
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeName'] = "Thésaurus pour la description et l'indexation des archives locales anciennes, modernes et contemporaines_liste d'autorité Actions";
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeAgencyName'] = "Direction des Archives de France";	
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeDataURI'] = "http://www.archivesdefrance.culture.gouv.fr/gerer/classement/normesoutils/thesaurus/";	
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeVersionID'] = "version 2009";			
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType = "subject";
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType["listVersionID"] = "edition 2009";	
		}
		
		$archiveTransfer->Contains->Appraisal->Code = "conserver";
		$archiveTransfer->Contains->Appraisal->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Appraisal->Duration = "P1Y";
		$archiveTransfer->Contains->Appraisal->StartDate = date('Y-m-d',strtotime($latestDate));

		$archiveTransfer->Contains->AccessRestriction->Code = $this->getAccessRestriction($transactionsInfo['classification_tab'],$transactionsInfo['nature_code']);
		$archiveTransfer->Contains->AccessRestriction->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->AccessRestriction->StartDate = date('Y-m-d',strtotime($latestDate));
		
		
		$archiveTransfer->Contains->Contains[0] = $this->getDL("Contains","Acte soumis au contrôle de légalité",$ar_actes_info['IDActe']);
		
		$archiveTransfer->Contains->Contains[0]->Contains[0]->DescriptionLevel="item";
		$archiveTransfer->Contains->Contains[0]->Contains[0]->DescriptionLevel['listVersionID']="edition 2009";
		$archiveTransfer->Contains->Contains[0]->Contains[0]->Name="Acte";
		
		$contentType = $this->getContentType($transactionsInfo['actes_file']);
		$actes_is_signed = isset($transactionInfo['signature']);
	
		$archiveTransfer->Contains->Contains[0]->Contains[0]->Document = $this->getDocument(basename($transactionsInfo['actes_file']), $contentType,false,$transactionsInfo['actes_file_orginal_filename'], $actes_is_signed);
		
		if ($transactionsInfo['annexe']) {
			$c = $this->getDL("Contains","Annexe(s) d'un acte soumis au contrôle de légalité");
			foreach($transactionsInfo['annexe'] as $i => $annexe){
				$contentType = $this->getContentType($annexe);
				$c->Document[$i] = $this->getDocument(basename($annexe),$contentType,false,"Annexe n° ".($i+1) .": ".$transactionsInfo['annexe_original_filename'][$i],$actes_is_signed);
			}
			$archiveTransfer->Contains->Contains[0]->Contains[] =  $c;
		}
		$c = $this->getDL("Contains","Accusé de réception d'un acte soumis au contrôle de légalité",$ar_actes_info['IDActe']);
		$c->Document = $this->getDocument(basename($transactionsInfo['ar_actes']), "text/xml",$ar_actes_info['DateReception'],false,true);
		$archiveTransfer->Contains->Contains[0]->Contains[] = $c;
		
		
		$num_echange = 0;
		$num_contains = 0;
		while(isset($transactionsInfo['echange_prefecture_type'][$num_echange])){
			
			$type = $transactionsInfo['echange_prefecture_type'][$num_echange];
			
			$archiveTransfer->Contains->Contains[$num_contains+1] =$this->getDL("Contains",$this->getRelatedTransactionName($type), $ar_actes_info['IDActe']);
			$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[0]->DescriptionLevel="item";
			$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[0]->DescriptionLevel['listVersionID']="edition 2009";
			$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[0]->Name= $this->getRelatedTransactionType($type);
			
			$contentType = $this->getContentType($transactionsInfo['echange_prefecture'][$num_echange]);
			
			$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[0]->Document 
				= $this->getDocument(basename($transactionsInfo['echange_prefecture'][$num_echange]),$contentType,false,$this->getRelatedTransactionType($type),false,$transactionsInfo['decision_date']);
	
			$nb_contains_contains  = 1 ;
			
			if(! empty($transactionsInfo['echange_prefecture_ar'][$num_echange]) && (basename($transactionsInfo['echange_prefecture_ar'][$num_echange]) != 'empty')){
					$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[$nb_contains_contains] 
						= $this->getDL("Contains",$this->getARName($type));
					$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[$nb_contains_contains]->Document 
						= $this->getDocument(basename($transactionsInfo['echange_prefecture_ar'][$num_echange]),"text/xml",false,"Accusé de réception",false,false,false);
					$nb_contains_contains  = 2 ;
			}
			
			$num_echange ++ ;
			while(isset($transactionsInfo['echange_prefecture_type'][$num_echange]) && $transactionsInfo['echange_prefecture_type'][$num_echange][1] != 'A'){
				$nb_contains_contains++;	
				$reponse_type = $transactionsInfo['echange_prefecture_type'][$num_echange];
				$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[$nb_contains_contains] 
						= $this->getDL("Contains",$this->getReponseName($reponse_type));

				$file_nb = 1;
				$contentType = $this->getContentType($transactionsInfo['echange_prefecture'][$num_echange]);
				$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[$nb_contains_contains]->Document[$file_nb] 
							= $this->getDocument(basename($transactionsInfo['echange_prefecture'][$num_echange]),$contentType,false,$this->getReponseDocumentName($reponse_type),false,$transactionsInfo['echange_prefecture_original_filename'][$num_contains],$transactionsInfo['decision_date']);
						
				$num_echange_ar = $num_echange;
				$num_echange++;
				
				while(isset($transactionsInfo['echange_prefecture_type'][$num_echange][2]) && $transactionsInfo['echange_prefecture_type'][$num_echange][2] == 'B'){
					$file_nb++;
					$contentType = $this->getContentType($transactionsInfo['echange_prefecture'][$num_echange]);
					$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[$nb_contains_contains]->Document[$file_nb] 
							= $this->getDocument(basename($transactionsInfo['echange_prefecture'][$num_echange]),$contentType,false,$this->getReponseDocumentName($reponse_type),false,$transactionsInfo['echange_prefecture_original_filename'][$num_contains],$transactionsInfo['decision_date']);
						
			
		
					$num_echange++;
				}
				if(! empty($transactionsInfo['echange_prefecture_ar'][$num_echange_ar]) && (basename($transactionsInfo['echange_prefecture_ar'][$num_echange_ar]) != 'empty')){
						$nb_contains_contains++;
						$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[$nb_contains_contains] 
							= $this->getDL("Contains",$this->getARRecuType($reponse_type));
						$archiveTransfer->Contains->Contains[$num_contains+1]->Contains[$nb_contains_contains]->Document 
							= $this->getDocument(basename($transactionsInfo['echange_prefecture_ar'][$num_echange_ar]),"text/xml",false,"Accusé de réception",false,false,false);
				}	
			}
			
			$num_contains++;
		}
		
		$xml_string =  $archiveTransfer->asXML();
		$xml_string = str_replace("####SAE_ID_VERSANT####", $this->authorityInfo['identifiant_versant'], $xml_string);
		$xml_string = str_replace("####SAE_ID_ARCHIVE####", $this->authorityInfo['identifiant_archive'], $xml_string);
		$xml_string = str_replace("####SAE_ORIGINATING_AGENCY####", $this->authorityInfo['identifiant_producteur'], $xml_string);
		$xml_string = str_replace("&#039;", "'", $xml_string);
	
		return $xml_string;
	}
	
	private function getARRecuType($type){
		$array = array(	
				'3R'=>"Accusé de réception d'une réponse à une demande de pièces complémentaires",
				'4R'=>"Accusé de réception d'une réponse à une lettre d'observations",
		);
		if (empty($array[$type])){
			throw new Exception("Accusé de réception non autorisé sur ce type de message (message $type)");
		}
		return $array[$type];
	}
		

	
	private function getARName($type){
		$array = array(	
				'3A'=>"Accusé de réception d'une demande de pièces complémentaires",
				'4A'=>"Accusé de réception d'une lettre d'observations",
		);
		if (empty($array[$type])){
			throw new Exception("Accusé de réception non autorisé sur ce type de message (message $type)");
		}
		return $array[$type];
		
	}
	
	private function getReponseDocumentName($type){
			$array = array(	
						'2R'=>"Réponse à un courrier simple",
						'3R'=>"Réponse",
						'4R'=>"Réponse",
						);
		return $array[$type];
	}
	
	private function getReponseName($type){
		$array = array(	
						'2R'=>"Réponse à un courrier simple",
						'3R'=>"Réponse à une demande de pièces complémentaires",
						'4R'=>"Réponse à une lettre d'observations",
						);
		return $array[$type];
	}
	
	private function getRelatedTransactionName($type){
		$array = array(	
						'2A'=>"Envoi d'un courrier simple",
						'3A'=>"Envoi d'une demande de pièces complémentaires",
						'4A'=>"Envoi d'une lettre d'observations",
						'5A'=>"Déféré au tribunal administratif");
		return $array[$type];
	}
	
	private function getRelatedTransactionType($type){
		$array = array(	
						'2A'=>"Courrier simple",
						'3A'=>"Demande de pièces complémentaires",
						'4A'=>"Lettre d'observations",
						'5A'=>"Déféré au tribunal administratif");
		return $array[$type];
	}
	
	
	private function getDL($node_name,$name,$id = false){
		$node = new ZenXML($node_name);
		$node->DescriptionLevel = "file"; 
		$node->DescriptionLevel['listVersionID'] = "edition 2009";
		$node->Name =$name;
		if ($id !== false ){
			$node->TransferringAgencyObjectIdentifier = "$id";
			$node->TransferringAgencyObjectIdentifier['schemeAgencyName'] = "Ministère de l'intérieur, de l'outre-mer et des collectivités territoriales";
		}
		return $node;
	}
	
	private function getDocument($filename,$mimetype,$receipt = false,$description = false,$is_original = true,$receipt_submission=false,$response=false){
		$document = new ZenXML("Document");
		$document->Attachment['mimeCode'] = $mimetype;
		$document->Attachment['filename'] = $filename;
		$document->Control = "false";
		$document->Copy = $is_original?"false":"true";
		if ($description !== false){
			$document->Description = $description;
		}		
		if ($receipt){
			$document->Receipt = date("c",strtotime($receipt));
		}
		if ($receipt_submission){
			$document->Receipt = date("c",strtotime($receipt_submission));
		}
		if ($response){
			$document->Response = date("c",strtotime($response));
		}
		$document->Type = "CDO";
		$document->Type["listVersionID"] = "edition 2009";
		
		return $document;
	}
	
	private function getContainsElement($description){
		$contains = new ZenXML("Contains");		
		$contains->DescriptionLevel = "file";
		$contains->DescriptionLevel['listVersionID'] = "edition 2009";
		$contains->Name = $description;
		return $contains;
	}
	
	private function getSujetActes($classification){

		$info = array(  "1" => "Commande publique",
				"2" => "Urbanisme",
				"3" => "Propriété publique",
				"3.4" => "Circonscription territoriale",
				"4" => "Personnel",
				"5" => "Election politique, Collectivité locale",
				"5.2" => "Organe délibérant",
				"5.6" => "Elu",
				"5.7" => "Etablissement public de coopération intercommunale",
				"5.8" => "Justice",
				"6" => "Police, Protection civile",
				"7" => "Finances locales",
				"7.1" => "Comptabilité publique",
				"7.2" => "Fiscalité",
				"7.3" => "Dette publique",
				"7.6" => "Comptabilité publique",
				"8.1" => "Education",
				"8.2" => "Protection sociale",
				"8.3" => "Réseau routier",
				"8.4" => "Amenagement du territoire",
				"8.5" => "Politique de la ville, Immobilier",
				"8.6" => "Emploi",
				"8.7" => "Transport",
				"8.8" => "Environnement",
				"8.9" => "Culture");
		$debut = substr($classification,0,3);
		
		if (isset($info[$debut])){
			return $info[$debut];
		}
		
		$debut = substr($classification,0,1);
		if (isset($info[$debut])){
			return $info[$debut];
		}
		
		throw new Exception("Bordereau CG86 : impossible de déterminer le sujet de l'acte : la classification de cet actes est inconnu ");
		
	}
	
	private function getAccessRestriction($classification,$nature){
		if(!is_array($classification)){
			$classification = explode('.',$classification);
		}
		if ($classification[0] == 4 && in_array($nature,array(3,4))){
			return "AR048";
		}
                if($classification[0] == 8 && $classification[1] == 2 && $nature == 3){
			return "AR048";
		}
		return "AR038";
	}
	
	private function getDuration($nature){
		switch($nature){
			case 4 :
				return "P10Y";
			case 2:
			case 3:	
			case 5: 
				return "P5Y";
			case 1:  
		default: 
				return "P1Y";
		}
	}
}
