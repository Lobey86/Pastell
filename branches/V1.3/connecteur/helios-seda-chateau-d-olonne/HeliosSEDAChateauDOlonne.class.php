<?php 
class HeliosSEDAChateauDOlonne extends SEDAConnecteur {
	
	private $authorityInfo;
	
	public function  setConnecteurConfig(DonneesFormulaire $seda_config){
		$this->authorityInfo = array(
				"sae_id_versant" =>  $seda_config->get("identifiant_versant"),
				"sae_id_archive" =>  $seda_config->get("identifiant_archive"),
				"sae_numero_aggrement" =>  $seda_config->get("numero_agrement"),
				"sae_originating_agency" =>  $seda_config->get("originating_agency"),
				"name" =>   $seda_config->get('nom'),
				"siren" =>  $seda_config->get('siren'),
				"sae_identifiant_producteur" => $seda_config->get("sae_identifiant_producteur"),
		);
	}
	
	public function checkInformation(array $information){
		$info = array('date','description','pes_description','pes_retour_description','pes_aller','pes_retour','pes_aller_content');		
		foreach($info as $key){
			if (empty($information[$key])){
				throw new Exception("Impossible de générer le bordereau : le paramètre $key est manquant. ");
			}
		}
		$info_sup = array('pes_aller_original_filename','pes_retour_original_filename','iparapheur_historique');
		
		foreach($info_sup as $key){
			if (empty($information[$key])){
				$information[$key] = false;
			}
		}
		return $information;
	}
	
	private function getTransferIdentifier($transactionsInfo) {
		$xml =  simplexml_load_string($transactionsInfo['pes_aller_content']);
		return strval($xml->Enveloppe->Parametres->NomFic['V']);
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
			$info['InfoDematerialisee'] = strval($xml->PES_DepenseAller->EnTeteDepense->InfoDematerialisee['V']);
		} else {
			$PES_XXXAller = $xml->PES_RecetteAller;
			$info['InfoDematerialisee'] = strval($xml->PES_RecetteAller->EnTeteRecette->InfoDematerialisee['V']);
		}
		$info['bordereau'] = array();
		foreach($PES_XXXAller->Bordereau as $i => $bordereau){
			$info['bordereau'][$i]['IdBord'] = strval($bordereau->BlocBordereau->IdBord['V']);
			$info['bordereau'][$i]['NbrPce'] = strval($bordereau->BlocBordereau->NbrPce['V']);
			foreach($bordereau->Piece as $j => $piece){
				$info['bordereau'][$i]['Piece'][$j]['NomPJ'] = strval($piece->BlocPiece->InfoPce->PJRef->NomPJ['V']);
				$info['bordereau'][$i]['Piece'][$j]['Support'] = strval($piece->BlocPiece->InfoPce->PJRef->Support['V']);
				$info['bordereau'][$i]['Piece'][$j]['IdUnique'] = strval($piece->BlocPiece->InfoPce->PJRef->IdUnique['V']);
			}
		}
		
		return $info;
	}
	
	public function getBordereau(array $transactionsInfo){
		
		$infoPESAller = $this->extractInfoFromPESAller($transactionsInfo['pes_aller_content']);
				
		$transactionsInfo = $this->checkInformation($transactionsInfo);
		$archiveTransfer = new ZenXML('ArchiveTransfer');
		$archiveTransfer['xmlns'] = "fr:gouv:ae:archive:draft:standard_echange_v0.2";
		$archiveTransfer->Comment = "Transfert d'un flux comptable conforme au PES V2";
		$archiveTransfer->Date = date('c');//"2011-08-12T11:03:32+02:00";		
		$archiveTransfer->TransferIdentifier = $infoPESAller['nomFic'] ; 
		
		$archiveTransfer->TransferringAgency->Address->BuildingName = "Mairie de Château d'Olonne";
		$archiveTransfer->TransferringAgency->Address->BuildingNumber = "53";
		$archiveTransfer->TransferringAgency->Address->CityName = "Le Château d'Olonne";
		$archiveTransfer->TransferringAgency->Address->Postcode = "85180";
		$archiveTransfer->TransferringAgency->Address->StreetName = "Rue Séraphin Buton";
		
		$archiveTransfer->ArchivalAgency->BusinessType = "FRAM85060";
		$archiveTransfer->ArchivalAgency->Identification = "FRAM85060";
		$archiveTransfer->ArchivalAgency->Identification['schemeName'] = "SIRENE";
		$archiveTransfer->ArchivalAgency->Identification['schemeAgencyName'] = "INSEE";
		$archiveTransfer->ArchivalAgency->Name = "Archives municipales du Château-d'Olonne";
		
		$archiveTransfer->ArchivalAgency->Contact->DepartementName = "Direction des Services à la Population";
		$archiveTransfer->ArchivalAgency->Contact->PersonName = "Archives Municipales du Château d'Olonne";
		
		$i = 0;
		foreach(array('pes_aller','pes_retour','iparapheur_historique') as $file_to_add){
			$file_path = $transactionsInfo[$file_to_add];
			if (! $file_path){
				continue;
			}			
			$archiveTransfer->Integrity[$i]->Contains = sha1_file($file_path);
			$archiveTransfer->Integrity[$i]->Contains['encodingCode'] = 'http://www.w3.org/2000/09/xmldsig#sha1';
			$archiveTransfer->Integrity[$i]->UnitIdentifier = basename($file_path);
			$i++;
		}
		
		$archiveTransfer->Contains->ArchivalAgreement = "acc01";
		$archiveTransfer->Contains->ArchivalAgreement['schemeName'] = "Identification_accords_d'archivage";
		$archiveTransfer->Contains->ArchivalAgreement['schemeAgencyName'] = "Archives_municipales_Chateau_d_Olonne";
		
		$archiveTransfer->Contains->ArchivalProfile = "Profil_flux_comptable_PES_V2 ";
		$archiveTransfer->Contains->ArchivalProfile['schemeAgencyName'] = "Archives_municipales_Chateau_d_Olonne ";		

		$archiveTransfer->Contains->DescriptionLanguage = "fr";
		$archiveTransfer->Contains->DescriptionLanguage['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->DescriptionLevel = "file";
		$archiveTransfer->Contains->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Name = "Flux PES « {$infoPESAller['nomFic']} »";

		$archiveTransfer->Contains->ContentDescription->CustodialHistory = "Les pièces soumises au contrôle du comptable public sont intégrées au flux comptable PES V2 défini par le programme Helios et sont transférées pour archivage depuis le tiers de télétransmission SLOW ou la passerelle Helios puis depuis Pastel ( Adullact) pour le compte de la ville du Château-d'Olonne. \"La description a été établie selon les règles du standard d'échange de données pour l'archivage électronique version0.2, publié dans le référentiel général d'interopérabilité.";
		$archiveTransfer->Contains->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->ContentDescription->LatestDate = date('Y-m-d',strtotime($transactionsInfo['date']));		
		$archiveTransfer->Contains->ContentDescription->OldestDate = date('Y-m-d',strtotime($transactionsInfo['date']));
		
		$archiveTransfer->Contains->ContentDescription->Size = 
			ceil((filesize($transactionsInfo['pes_aller']) + filesize($transactionsInfo['pes_retour'])) / 1024 / 1024); 
		$archiveTransfer->Contains->ContentDescription->Size['unitCode'] = "4L";
		
		$archiveTransfer->Contains->AccessRestriction->Code = $infoPESAller['is_depense']?"AR038":"AR048";
		$archiveTransfer->Contains->AccessRestriction->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->AccessRestriction->StartDate = date('Y-m-d',strtotime($transactionsInfo['date']));
		
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Identification = "218500601_01";
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Name="Direction des finances et du contrôle de gestion";
		
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->BuildingName = "Mairie de Château d'Olonne";
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->BuildingNumber = "53";
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->CityName = "Le Château d'Olonne";
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->Postcode = "85180";
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->StreetName = "Rue Séraphin Buton";
		

		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordContent = "Ville du Chateau d'Olonne ";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference = "218500601";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['SchemeName'] = "SIRENE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['SchemeAgencyName'] = "INSEE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType = "corpname";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType["listVersionID"] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordContent = "livre comptable";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference = "T3-118";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['SchemeName'] = "Liste d'autorité typologie documentaire du Service Interministériel de France";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeVersionID'] = "version 2009";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['SchemeAgencyName'] = "Direction des Archives de France";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType = "genreform";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType["listVersionID"] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordContent = "COMPTABILITE PUBLIQUE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference = "T1-747";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['SchemeName'] = "Thesaurus_matiere";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeVersionID'] = "version 2009";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['SchemeAgencyName'] = "Direction des Archives de France";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType = "subject";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType["listVersionID"] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordContent = "PIECE COMPTABLE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference = "T3-160";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['SchemeName'] = "Liste d'autorité_typologie documentaire";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeVersionID'] = "version 2009";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['SchemeAgencyName'] = "Direction des Archives de France";		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType = "subject";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType["listVersionID"] = "edition 2009";
			
		$archiveTransfer->Contains->Appraisal->Code = "detruire";
		$archiveTransfer->Contains->Appraisal->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Appraisal->Duration = "P10Y";
		$archiveTransfer->Contains->Appraisal->StartDate =  date('Y-m-d',strtotime($transactionsInfo['date']));
	
		$num_contains_contains = 0;
		
		if ($transactionsInfo['iparapheur_historique']){
			$archiveTransfer->Contains->Contains[$num_contains_contains]->DescriptionLevel = "item";
			$archiveTransfer->Contains->Contains[$num_contains_contains]->DescriptionLevel['listVersionID'] = "edition 2009";
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Name = "Journal des transmissions";
			
			$archiveTransfer->Contains->Contains[$num_contains_contains]->ContentDescription->Language='fr';
			$archiveTransfer->Contains->Contains[$num_contains_contains]->ContentDescription->Language['listVersionID'] = "edition 2009";
	
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['format'] = 'fmt/101';
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['mimeCode'] = 'text/xml';
			
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['filename'] = basename($transactionsInfo['iparapheur_historique']);
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Description="Journal des transmissions";
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Type = "CDO";
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Type['listVersionID'] = "edition 2009";
			$num_contains_contains++;
		}
		
		$archiveTransfer->Contains->Contains[$num_contains_contains]->DescriptionLevel = "item";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Name = "PES";
		
		$archiveTransfer->Contains->Contains[$num_contains_contains]->ContentDescription->Description= "Identifiant du payeur : {$infoPESAller['IdPost']}, Date de génération du flux : {$infoPESAller['DteStr']}, SIREN du budget principal de la collectivité : {$infoPESAller['IdColl']}, Code de la collectivité : {$infoPESAller['CodCol']}, Code du budget : {$infoPESAller['CodBud']}.";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->ContentDescription->Language='fr';
		$archiveTransfer->Contains->Contains[$num_contains_contains]->ContentDescription->Language['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['format'] = 'fmt/101';
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['mimeCode'] = 'text/xml';
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['filename'] = basename($transactionsInfo['pes_aller']);
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Description="PES";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Type = "CDO";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Type['listVersionID'] = "edition 2009";
		
		$num_contains = 0;
		foreach($infoPESAller['bordereau'] as $i => $bordereauInfo) {
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->DescriptionLevel = "item";
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->DescriptionLevel['listVersionID'] = "edition 2009";
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->Name = "Bordereau";
			
			$entete = $infoPESAller['is_depense']?"EnteteDepense":"EnteteRecette";
			
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->ContentDescription->Description= "$entete, bordereau dématérialisé : {$infoPESAller['InfoDematerialisee']}, Identifiant du bordereau : {$bordereauInfo['IdBord']}, nombre de mouvements comptables : {$bordereauInfo['NbrPce']}";
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->ContentDescription->Language='fr';
			$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->ContentDescription->Language['listVersionID'] = "edition 2009";
			
			$num_contains++;
			foreach($bordereauInfo['Piece'] as $j => $piece){
				$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->DescriptionLevel = "file";
				$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->DescriptionLevel['listVersionID'] = "edition 2009";
				$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->Name = "Pièces justificatives";
				
				$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->ContentDescription->Description="identifiant de la pièce justificative : {$piece['NomPJ']}, type de support : {$piece['Support']}, identifiant attribué par éditeur : {$piece['IdUnique']}";
				$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->ContentDescription->Language='fr';
				$archiveTransfer->Contains->Contains[$num_contains_contains]->Contains[$num_contains]->ContentDescription->Language['listVersionID'] = "edition 2009";
				$num_contains++;
			}
		}
		
		$num_contains_contains++;
		
		$archiveTransfer->Contains->Contains[$num_contains_contains]->DescriptionLevel = "item";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Name = "Accusé de réception : ACK / ACQUIT / NACK";
		
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['format'] = 'fmt/101';
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['mimeCode'] = 'text/xml';
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Attachment['filename'] = basename($transactionsInfo['pes_retour']);
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Description="PES";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Type = "CDO";
		$archiveTransfer->Contains->Contains[$num_contains_contains]->Document->Type['listVersionID'] = "edition 2009";

		return $archiveTransfer->asXML();
	}
	
}