<?php 
class HeliosSEDAStandard extends SEDAConnecteur {
	
	private $authorityInfo;
	
	public function  setConnecteurConfig(DonneesFormulaire $seda_config){
		$this->authorityInfo = array(
				"identifiant_versant" =>  utf8_encode($seda_config->get("identifiant_versant")),
				"identifiant_archive" =>  utf8_encode($seda_config->get("identifiant_archive")),
				"sae_numero_aggrement" =>  utf8_encode($seda_config->get("numero_agrement")),
				"sae_numero_aggrement_schemeName" =>  utf8_encode($seda_config->get("sae_numero_aggrement_schemeName")),
				"sae_numero_aggrement_schemeAgencyName" =>  utf8_encode($seda_config->get("sae_numero_aggrement_schemeAgencyName")),
				"originating_agency" =>  utf8_encode($seda_config->get("originating_agency")),
				"name" =>   utf8_encode($seda_config->get('nom')),
				"siren" =>  utf8_encode($seda_config->get('siren')),
		);
	}
	
	public function checkInformation(array $information){
		$info = array('date','description','pes_description','pes_retour_description','pes_aller','pes_retour');		
		foreach($info as $key){
			if (empty($information[$key])){
				throw new Exception("Impossible de générer le bordereau : le paramètre $key est manquant. ");
			}
		}
	}
	
	private function getTransferIdentifier($transactionsInfo) {
		return sha1_file($transactionsInfo['pes_aller']) ."-". time();
	}
	
	private function extractInfoFromPESAller($pes_aller_content){
		$xml =  simplexml_load_string($pes_aller_content);
		$info = array();
		$info['nomFic'] =  strval($xml->Enveloppe->Parametres->NomFic['V']);
		$info['is_depense'] = isset($xml->PES_DepenseAller);
		$info['is_recette'] = isset($xml->PES_RecetteAller);
		if ($info['is_depense'] == $info['is_recette']){
			throw new Exception("Impossible de savoir si le PES est une recette ou une dépense");
		}
		foreach(array('IdPost','DteStr','IdColl','CodCol','CodBud') as $nodeName) {
			$node = $xml->EnTetePES->$nodeName;
			$info[$nodeName] = strval($node['V']);
		}
		if ($info['is_depense']){
			$PES_XXXAller = $xml->PES_DepenseAller;
		} else {
			$PES_XXXAller = $xml->PES_RecetteAller;
		}
		$info['bordereau'] = array();
		$i = 0;
		foreach($PES_XXXAller->Bordereau as $bordereau){
			$info['bordereau'][$i]['IdBord'] = strval($bordereau->BlocBordereau->IdBord['V']);
			$info['bordereau'][$i]['TypBord'] = strval($bordereau->BlocBordereau->TypBord['V']);
			$j = 0;
			foreach($bordereau->Piece as $piece){
				if($piece->BlocPiece->InfoPce->IdPce['V'] != null)
					$info['bordereau'][$i]['Piece'][$j]['IdPce'] = strval($piece->BlocPiece->InfoPce->IdPce['V']);
				else
					$info['bordereau'][$i]['Piece'][$j]['IdPce'] = strval($piece->BlocPiece->IdPce['V']);

				if($piece->BlocPiece->InfoPce->TypPce['V'] != null)
					$info['bordereau'][$i]['Piece'][$j]['TypPce'] = strval($piece->BlocPiece->InfoPce->TypPce['V']);
				else
					$info['bordereau'][$i]['Piece'][$j]['TypPce'] = strval($piece->BlocPiece->TypPce['V']);

				if($piece->BlocPiece->InfoPce->NatPce['V'] != null)
					$info['bordereau'][$i]['Piece'][$j]['NatPce'] = strval($piece->BlocPiece->InfoPce->NatPce['V']);
				else
					$info['bordereau'][$i]['Piece'][$j]['NatPce'] = strval($piece->BlocPiece->NatPce['V']);
				
				$k = 0;
				$info['bordereau'][$i]['Piece'][$j]['PJ'] = array();
				if (! empty($piece->BlocPiece->InfoPce->PJRef)){
					foreach($piece->BlocPiece->InfoPce->PJRef as $pj){
						$info['bordereau'][$i]['Piece'][$j]['PJ'][] = strval($pj->NomPJ['V']); 
						$k++;
					}
				}
				
				if (! empty($piece->BlocPiece->PJRef)){
					foreach($piece->BlocPiece->PJRef as $pj){
						$info['bordereau'][$i]['Piece'][$j]['PJ'][] = strval($pj->NomPJ['V']);
						$k++;
					}
				}
				$j++;
			}
			$i++;
		}		
		return $info;
	}
	private function extractInfoFromPESRetour($pes_retour_content){
		$xml =  simplexml_load_string($pes_retour_content);
		$info = array();
		$info['root'] =  strval($xml->getName());
	
		return $info;
	}
	
	public function getBordereau(array $transactionsInfo){
		$this->checkInformation($transactionsInfo);
		
		$infoPESAller = $this->extractInfoFromPESAller(file_get_contents($transactionsInfo['pes_aller']));
		$infoPESRetour = $this->extractInfoFromPESRetour(file_get_contents($transactionsInfo['pes_retour']));
		
		$archiveTransfer = new ZenXML('ArchiveTransfer');
		$archiveTransfer['xmlns'] = "fr:gouv:ae:archive:draft:standard_echange_v0.2";
		$archiveTransfer->Comment = "Transfert d'un flux PES issu du tiers de télétransmission S2LOW : {$infoPESAller['nomFic']}";
		$archiveTransfer->Date = date('c');//"2011-08-12T11:03:32+02:00";
		
		$archiveTransfer->TransferIdentifier = $this->getTransferIdentifier($transactionsInfo);
		$archiveTransfer->TransferIdentifier['schemeName'] = "Adullact Projet";
		
		
		$archiveTransfer->TransferringAgency = "####SAE_ID_VERSANT####";
		$archiveTransfer->ArchivalAgency = "####SAE_ID_ARCHIVE####";
		
		$i = 0;
		foreach(array('pes_aller','pes_retour') as $file_to_add){
			$file_path = $transactionsInfo[$file_to_add];
			$archiveTransfer->Integrity[$i] = $this->getIntegrityMarkup($file_path);
			$i++;
		}
		
		$archiveTransfer->Contains->ArchivalAgreement = $this->authorityInfo['sae_numero_aggrement'];
		$archiveTransfer->Contains->ArchivalAgreement['schemeName'] = $this->authorityInfo['sae_numero_aggrement_schemeName'];
		$archiveTransfer->Contains->ArchivalAgreement['schemeAgencyName'] = $this->authorityInfo['sae_numero_aggrement_schemeAgencyName'];
		
		$archiveTransfer->Contains->ArchivalProfile = "Profil_PES_Adullact_v2";
		$archiveTransfer->Contains->ArchivalProfile['schemeAgencyName'] = "Adullact Projet";

		
		
		$archiveTransfer->Contains->DescriptionLanguage = "fr";
		$archiveTransfer->Contains->DescriptionLanguage['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->DescriptionLevel = "file";
		$archiveTransfer->Contains->DescriptionLevel['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->Name = "Flux comptable PES ({$infoPESAller['nomFic']}) en date du {$transactionsInfo['date']} de {$this->authorityInfo['name']}";
		
		$archiveTransfer->Contains->ContentDescription->CustodialHistory = "Les pièces transférées au comptable public, sont intégrées au flux comptable PES V2 défini par le programme Helios et sont transférées pour archivage depuis le tiers de télétransmission pour le compte de {$this->authorityInfo['name']}. La description a été établie selon les règles du standard d'échange de données pour l'archivage électronique version 0.2.";
		$archiveTransfer->Contains->ContentDescription->Description = "Identifiant du payeur : {$infoPESAller['IdPost']} ; Identifiant de l'ordonateur :  {$infoPESAller['IdColl']} ; Code du budget :  {$infoPESAller['CodBud']} ; Domaine :  ". ($infoPESAller['is_depense']?"PES_DepenseAller":"PES_RecetteAller");
		
		$archiveTransfer->Contains->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->ContentDescription->LatestDate = date('Y-m-d',strtotime($transactionsInfo['date']));		
		$archiveTransfer->Contains->ContentDescription->OldestDate = date('Y-m-d',strtotime($transactionsInfo['date']));
		$archiveTransfer->Contains->ContentDescription->Size =
		ceil((filesize($transactionsInfo['pes_aller']) + filesize($transactionsInfo['pes_retour'])) / 1024 / 1024);
		$archiveTransfer->Contains->ContentDescription->Size['unitCode'] = "4L";
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency = "####SAE_ORIGINATING_AGENCY####";
		
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordContent = $this->authorityInfo['name'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference = $this->authorityInfo['siren'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeName'] = "SIRENE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeAgencyName'] = "INSEE";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType = "corpname";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType["listVersionID"] = "edition 2009";
	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordContent = ($infoPESAller['is_depense']?"PES_DepenseAller":"PES_RecetteAller");
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType = "subject";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType["listVersionID"] = "edition 2009";
		
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordContent = "COMPTABILITE PUBLIQUE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference = "T1-747";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeName'] = "Thésaurus matière";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeAgencyName'] = "Direction des archives de france";		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeVersionID'] = "version 2009";			
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType = "subject";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType["listVersionID"] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordContent = "PIECE COMPTABLE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference = "T3-160";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeName'] = "Liste d'autorité Typologie documentaire";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeAgencyName'] = "Direction des Archives de France";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeVersionID'] = "version 2009";			
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType = "genreform";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType["listVersionID"] = "edition 2009";
		
		if ($infoPESAller['is_recette']) {
			$archiveTransfer->Contains->ContentDescription->AccessRestriction->Code = "AR038";
			$archiveTransfer->Contains->ContentDescription->AccessRestriction->Code['listVersionID'] = "edition 2009";
			$archiveTransfer->Contains->ContentDescription->AccessRestriction->StartDate = date('Y-m-d',strtotime($transactionsInfo['date']));
		}
		
		$archiveTransfer->Contains->Appraisal->Code = "detruire";
		$archiveTransfer->Contains->Appraisal->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Appraisal->Duration = "P10Y";
		$archiveTransfer->Contains->Appraisal->StartDate =  date('Y-m-d',strtotime($transactionsInfo['date']));
		
	
		$archiveTransfer->Contains->AccessRestriction->Code = $infoPESAller['is_recette'] ? "AR048" : "AR038";
		$archiveTransfer->Contains->AccessRestriction->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->AccessRestriction->StartDate = date('Y-m-d',strtotime($transactionsInfo['date']));
		
		$archiveTransfer->Contains->Document->Attachment['format'] = 'fmt/101';
		$archiveTransfer->Contains->Document->Attachment['mimeCode'] = 'application/xml';
		$archiveTransfer->Contains->Document->Attachment['filename'] = basename($transactionsInfo['pes_aller']);
		$archiveTransfer->Contains->Document->Description="PES";
		$archiveTransfer->Contains->Document->Type = "CDO";
		$archiveTransfer->Contains->Document->Type['listVersionID'] = "edition 2009";

		$num_contains = 0;
		
		foreach($infoPESAller['bordereau'] as $i=>$bordereau) {
			$archiveTransfer->Contains->Contains[$num_contains]->DescriptionLevel = "item";
			$archiveTransfer->Contains->Contains[$num_contains]->DescriptionLevel['listVersionID'] = "edition 2009";
			$archiveTransfer->Contains->Contains[$num_contains]->Name = $this->getTypeBordereau($infoPESAller['bordereau'][$i]['TypBord'], $infoPESAller['is_recette']) . " : ".$infoPESAller['bordereau'][$i]['IdBord'];
			
			$archiveTransfer->Contains->Contains[$num_contains]->ContentDescription->Language='fr';
			$archiveTransfer->Contains->Contains[$num_contains]->ContentDescription->Language['listVersionID'] = "edition 2009";
	
			if ($infoPESAller['is_recette']) {
				$archiveTransfer->Contains->Contains[$num_contains]->AccessRestriction->Code = "AR038";
				$archiveTransfer->Contains->Contains[$num_contains]->AccessRestriction->Code['listVersionID'] = "edition 2009";
				$archiveTransfer->Contains->Contains[$num_contains]->AccessRestriction->StartDate = date('Y-m-d',strtotime($transactionsInfo['date']));
			}
			
			foreach($bordereau['Piece'] as $j=>$piece){
				$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->DescriptionLevel = "item";
				$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->DescriptionLevel['listVersionID'] = "edition 2009";
				$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Name = $this->getTypePiece($piece['TypPce'],$infoPESAller['is_recette']) . ", ".$this->getNaturePiece($piece['NatPce'],$infoPESAller['is_recette']) ." : {$piece['IdPce']}";
			
				//$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->ContentDescription->Description = $transactionsInfo['pes_retour_description'];
				$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->ContentDescription->Language = "fr";
				$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->ContentDescription->Language['listVersionID'] = "edition 2009";
				$k = 0;
				if ($piece['PJ']){
					$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->DescriptionLevel = "file";
					$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->DescriptionLevel['listVersionID'] = "edition 2009";
					$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->Name = "Pièces justificatives";
					
					$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->ContentDescription->Description = implode(", ",$piece['PJ']);
					$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->ContentDescription->Language = "fr";
					$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->ContentDescription->Language['listVersionID'] = "edition 2009";
					
					/*if ($infoPESAller['is_recette']) {
						$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->AccessRestriction->Code = "AR038";
						$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->AccessRestriction->Code['listVersionID'] = "edition 2009";
						$archiveTransfer->Contains->Contains[$num_contains]->Contains[$j]->Contains[$k]->AccessRestriction->StartDate = date('Y-m-d',strtotime($transactionsInfo['date']));
					}*/
					
					$k++;
				}
			}
			$num_contains ++;
		}
		
		
		$archiveTransfer->Contains->Contains[$num_contains]->DescriptionLevel = "item";
		$archiveTransfer->Contains->Contains[$num_contains]->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Contains[$num_contains]->Name = "Accusé de réception : {$infoPESRetour['root']}";
		
		$archiveTransfer->Contains->Contains[$num_contains]->Document->Attachment['format'] = 'fmt/101';
		$archiveTransfer->Contains->Contains[$num_contains]->Document->Attachment['mimeCode'] = 'application/xml';
		$archiveTransfer->Contains->Contains[$num_contains]->Document->Attachment['filename'] = basename($transactionsInfo['pes_retour']);
		$archiveTransfer->Contains->Contains[$num_contains]->Document->Type = "CDO";
		$archiveTransfer->Contains->Contains[$num_contains]->Document->Type['listVersionID'] = "edition 2009";

		$xml_string = $archiveTransfer->asXML();
		$xml_string = str_replace("####SAE_ID_VERSANT####", $this->authorityInfo['identifiant_versant'], $xml_string);
		$xml_string = str_replace("####SAE_ID_ARCHIVE####", $this->authorityInfo['identifiant_archive'], $xml_string);
		$xml_string = str_replace("####SAE_ORIGINATING_AGENCY####", $this->authorityInfo['originating_agency'], $xml_string);
		
		return $xml_string;
	}
	
	public function getTypeBordereau($TypBord,$is_recette ){
		$tab = array (
			"01"	=> array(true=>"Bordereau ordinaire",false=>"Bordereau ordinaire"),
			"02"	=> array(true=>"Bordereau d'annulation/réduction",false=>"Bordereau d'annulation/réduction"),
			"03"	=> array(true=>"Bordereau d'ordre de recette",false=>"Bordereau d'ordres de paiement"),
			"04"	=> array(true=>"Bordereau de titre émis suite à décision juridictionnelle",false=>"Bordereau de régularisation"),
			"05"	=> array(true=>"Entête P503",false=>""),
			"06"	=> array(true=>"Bordereau Ordre de recette multi créanciers",false=>""),
				);
		if (empty($tab[$TypBord][$is_recette])){
			throw new Exception("Impossible de trouver le type de bordereau pour $TypBord et $is_recette");
		}
		return $tab[$TypBord][$is_recette];
	}
	
	public function getTypePiece($TypPiece,$is_recette){
		$tab = array (
		"01"	=> array(true=>"Titre ordinaire",false=>"Mandat ordinaire"),
		"02"	=> array(true=>"Titre correctif",false=>"Mandat correctif"),
		"03"	=> array(true=>"Titre d'ordre budgétaire",false=>"Mandat d'ordre budgétaire"),
		"04"	=> array(true=>"Titre d'ordre mixte",false=>"Mandat d'ordre mixte"),
		"05"	=> array(true=>"Titre émis après encaissement",false=>"Mandat émis après paiement"),
		"06"	=> array(true=>"Titre récapitulatif avec rôle",false=>"Mandat global"),
		"07"	=> array(true=>"Titre récapitulatif sans rôle",false=>"Mandat d'admission en non valeurs"),
		"08"	=> array(true=>"",false=>"Mandat collectif"),
		"09"	=> array(true=>"Titre de majoration",false=>"Mandat sur marché"),
		"10"	=> array(true=>"Titre en plusieurs années",false=>"Mandat de rattachement"),
		"11"	=> array(true=>"Titre de rattachement",false=>"Ordre de paiement"),
		"12"	=> array(true=>"Ordre de recette ordonnateur",false=>"Demande émission mandat"),
		"13"	=> array(true=>"Demande émission de titre (P503)",false=>"Charges constatées d'avance"),
		"14"	=> array(true=>"Produits constatés d'avance",false=>""),
		"15"	=> array(true=>"Titre ORMC",false=>""),
				);
		if (empty($tab[$TypPiece][$is_recette])){
			throw new Exception("Impossible de trouver le type de piece pour $TypPiece et $is_recette");
		}
		return $tab[$TypPiece][$is_recette];
	}
	
	public function getNaturePiece($natPiece,$is_recette){
		$tab = array (
		
		"01"	=> array(true=>"Fonctionnement",false=>"Fonctionnement"),
		"02"	=> array(true=>"Investissement",false=>"Investissement"),
		"03"	=> array(true=>"Inventaire",false=>"Inventaire"),
		"04"	=> array(true=>"Emprunt",false=>"Emprunt"),
		"05"	=> array(true=>"Régie",false=>"Régie"),
		"06"	=> array(true=>"Annulation/Réduction",false=>"Annulation/Réduction"),
		"07"	=> array(true=>"complémentaire",false=>"complémentaire"),
		"08"	=> array(true=>"réémis",false=>"réémis"),
		"09"	=> array(true=>"annulant un mandat",false=>"annulant un titre"),
		"10"	=> array(true=>"annulation du titre de rattachement",false=>"annulation du mandat de rattachement"),
		"11"	=> array(true=>"Marché",false=>"Paie"),
		"12"	=> array(true=>"",false=>"Retenue de garantie"),
		"13"	=> array(true=>"", false=>"Dernier acompte du marché"),
		"14"	=> array(true=>"",false=>"Avance forfaitaire"),
		"18"	=> array(true=>"opération d'ordre liée aux cessions",false=>"opération d'ordre liée aux cessions"),
				);
		
		if (empty($tab[$natPiece][$is_recette])){
			throw new Exception("Impossible de trouver la nature de piece pour $natPiece et $is_recette");
		}
		return $tab[$natPiece][$is_recette];
	}
	
}