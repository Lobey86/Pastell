<?php 

class ActesSEDALocarchive extends SEDAConnecteur {
	
	private $authorityInfo;
	
	private $seda_config;
		
	public function  setConnecteurConfig(DonneesFormulaire $seda_config){
		$this->authorityInfo = array(
				"sae_id_versant" =>  $seda_config->get("sae_identifiant_versant"),
				"sae_id_archive" =>  $seda_config->get("sae_identifiant_archive"),
				"sae_numero_aggrement" =>  $seda_config->get("sae_numero_agrement"),
				"sae_originating_agency" =>  $seda_config->get("sae_originating_agency"),
				"nom_entite" =>   $seda_config->get('nom_entite'),
				"siren_entite" =>  $seda_config->get('siren_entite'),
		);
		$this->seda_config = $seda_config;
	}

	
	private function checkInformation(array $information){
		$info = array('numero_acte_collectivite','subject','decision_date',
					'nature_descr','nature_code','classification',
					'latest_date','actes_file','ar_actes');		
		foreach($info as $key){
			if (empty($information[$key])){
				throw new Exception("Impossible de générer le bordereau : le paramètre $key est manquant. ");
			}
		}
		$info_sup = array('actes_file_orginal_filename','annexe_original_filename');
		
		foreach($info_sup as $key){
			if (empty($information[$key])){
				$information[$key] = false;
			}
		}
		
		return $information;
	}

	public function getBordereau(array $transactionsInfo){
		$transactionsInfo = $this->checkInformation($transactionsInfo);
		$archiveTransfer = new ZenXML('ArchiveTransfer');
		$archiveTransfer['xmlns:xsi']="http://www.w3.org/2001/XMLSchema-instance";
		$archiveTransfer['xsi:schemaLocation'] = "fr:gouv:ae:archive:draft:standard_echange_v0.2 archives_echanges_v0-2_archivetransfer.xsd";
		$archiveTransfer->Comment = "Transfert d'un acte soumis au contrôle de légalité";
		$archiveTransfer->Date = date('c');//"2011-08-12T11:03:32+02:00";
		$archiveTransfer->TransferIdentifier = $transactionsInfo['numero_acte_collectivite'];
		$archiveTransfer->TransferIdentifier['schemeName'] = "Codification interne";
		
		$archiveTransfer->TransferringAgency->Description = $this->seda_config->get('transferring_agency_description');
		$archiveTransfer->TransferringAgency->Identification = $this->seda_config->get('transferring_agency_identification');
		$archiveTransfer->TransferringAgency->Contact->DepatmentName = $this->seda_config->get('transferring_agency_description');
		$archiveTransfer->TransferringAgency->Contact->PersonName = $this->seda_config->get('transferring_agency_person_name');
		$archiveTransfer->TransferringAgency->Contact->Responsability = $this->seda_config->get('transferring_agency_responsability');
		$archiveTransfer->TransferringAgency->Address->BuildingNumber = $this->seda_config->get('transferring_agency_building_number');
		$archiveTransfer->TransferringAgency->Address->StreetName = $this->seda_config->get('transferring_agency_street_name');
		$archiveTransfer->TransferringAgency->Address->CityName = $this->seda_config->get('transferring_agency_city_name');
		$archiveTransfer->TransferringAgency->Address->Country = $this->seda_config->get('transferring_agency_country');
		$archiveTransfer->TransferringAgency->Address->Country['listVersionID'] = "second edition 2006";
		$archiveTransfer->TransferringAgency->Address->Postcode = $this->seda_config->get('transferring_agency_postcode');

		$archiveTransfer->ArchivalAgency->Description = $this->seda_config->get('archival_agency_description');
		$archiveTransfer->ArchivalAgency->Identification = $this->seda_config->get('archival_agency_identification');
		$archiveTransfer->ArchivalAgency->Contact->PersonName = $this->seda_config->get('archival_agency_person_name');
		$archiveTransfer->ArchivalAgency->Contact->Responsability = $this->seda_config->get('archival_agency_responsability');
		$archiveTransfer->ArchivalAgency->Address->BuildingNumber = $this->seda_config->get('archival_agency_building_number');
		$archiveTransfer->ArchivalAgency->Address->StreetName = $this->seda_config->get('archival_agency_street_name');
		$archiveTransfer->ArchivalAgency->Address->CityName = $this->seda_config->get('archival_agency_city_name');
		$archiveTransfer->ArchivalAgency->Address->Country = $this->seda_config->get('archival_agency_country');
		$archiveTransfer->ArchivalAgency->Address->Country['listVersionID'] = "second edition 2006";
		$archiveTransfer->ArchivalAgency->Address->Postcode = $this->seda_config->get('archival_agency_postcode');
		
		$archiveTransfer->Contains->ArchivalAgencyArchiveIdentifier = $this->seda_config->get('archival_agency_archive_identifier'); 
		$archiveTransfer->Contains->ArchivalAgreement = $this->seda_config->get('archival_agreement');
		$archiveTransfer->Contains->ArchivalProfile = $this->seda_config->get('archival_profil');;
		
		$archiveTransfer->Contains->DescriptionLanguage = "fr";
		$archiveTransfer->Contains->DescriptionLanguage['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->DescriptionLevel = "series";
		$archiveTransfer->Contains->DescriptionLevel['listVersionID'] = "edition 2009";
		
		
		$col_name = $this->seda_config->get('originating_agency_identification');
		$archiveTransfer->Contains->Name = "$col_name : Actes ({$transactionsInfo['nature_descr']}) n°{$transactionsInfo['numero_acte_collectivite']} ";
		
		
		
		$archiveTransfer->Contains->ContentDescription->CustodialHistory = "";
		$archiveTransfer->Contains->ContentDescription->Description = $transactionsInfo['subject'];
		
		$archiveTransfer->Contains->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		
		$latestDate = date('Y-m-d',strtotime($transactionsInfo['latest_date'] ." + 2 month"));
		$oldestDate = date('Y-m-d',strtotime($transactionsInfo['decision_date']));
		
		$archiveTransfer->Contains->ContentDescription->LatestDate = $latestDate;
		$archiveTransfer->Contains->ContentDescription->OldestDate = $oldestDate; 

		$archiveTransfer->Contains->ContentDescription->Size['unitCode'] = "2P";
		
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Description = $this->seda_config->get('originating_agency_description');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Identification = $this->seda_config->get('originating_agency_identification');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Contact->DepatmentName = $this->seda_config->get('originating_agency_description');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Contact->PersonName = $this->seda_config->get('originating_agency_person_name');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Contact->Responsability = $this->seda_config->get('originating_agency_responsability');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->BuildingNumber = $this->seda_config->get('originating_agency_building_number');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->StreetName = $this->seda_config->get('originating_agency_street_name');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->CityName = $this->seda_config->get('originating_agency_city_name');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->Country = $this->seda_config->get('originating_agency_country');
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->Country['listVersionID'] = "second edition 2006";
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Address->Postcode = $this->seda_config->get('originating_agency_postcode');
			
		$archiveTransfer->Contains->Contains[0] = $this->getContainsElement("Transmission d'un acte soumis au contrôle de légalité",$latestDate,$oldestDate);
		$archiveTransfer->Contains->Contains[0]->Contains[0] = $this->getContainsElementWithDocument("Actes",array(basename($transactionsInfo['actes_file'])),$latestDate,$oldestDate,array($transactionsInfo['actes_file_orginal_filename']));
		
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->OtherMetadata->controlaccess->genreform = $transactionsInfo['nature_descr'];
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->OtherMetadata->controlaccess->subject = $transactionsInfo['nature_code'];
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->OtherMetadata->controlaccess->subject['altrender'] = "acte";
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->OtherMetadata->controlaccess->subject['type'] = "numero";
		
		foreach($transactionsInfo['annexe'] as $i => $document_annexe){
			$num_annexe = $i + 1;
			$archiveTransfer->Contains->Contains[0]->Contains[] = $this->getContainsElementWithDocument("Annexe n°$num_annexe d'un acte soumis au contrôle de légalité",
																							array($document_annexe),$latestDate,$oldestDate,isset($transactionsInfo['annexe_original_filename'][$i])?array($transactionsInfo['annexe_original_filename'][$i]):false);
		}

		$arActes = $this->getContainsElementWithDocument("Accusé de réception d'un acte soumis au contrôle de légalité",
															array(basename($transactionsInfo['ar_actes']))
															,$latestDate,$oldestDate
															);
		
		unset($arActes->Document[0]->Attachment['mimeCode']);
		$archiveTransfer->Contains->Contains[0]->Contains[] = $arActes;
		
		return $archiveTransfer->asXML();
	}
	
	private function getContainsElementWithDocument($description,array $allFileInfo,$latestDate,$oldestDate,$original_filename = false){
		$contains = new ZenXML("Contains");
		$contains->DescriptionLevel = "item";
		$contains->DescriptionLevel['listVersionID'] = "edition 2009";
		$contains->Name =  $description ;
		$contains->ContentDescription->Description = $description;
		$contains->ContentDescription->Language = "FR";
		$contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		$contains->ContentDescription->LatestDate = $latestDate;
		$contains->ContentDescription->OldestDate = $oldestDate;
		
		foreach($allFileInfo as $i => $fileInfo){
			if (is_array($fileInfo)){
				$fileName = $fileInfo[0];
			} else  {
				$fileName = $fileInfo;
			}
			$contains->Document[$i]->Attachment['filename'] = basename($fileName);
	
			if ($original_filename && isset($original_filename[$i])){
				$contains->Document[$i]->Description = "$description : $original_filename[$i]";
			} else {
				$contains->Document[$i]->Description = "$description";
			}
		
			$contains->Document[$i]->Type = "CDO";
			$contains->Document[$i]->Type["listVersionID"] = "edition 2009";
		}
		return $contains;
	}
	
	private function getContainsElement($description,$latestDate,$oldestDate){
		$contains = new ZenXML("Contains");		
		$contains->DescriptionLevel = "file";
		$contains->DescriptionLevel['listVersionID'] = "edition 2009";
		$contains->Name = $description;
		$contains->ContentDescription->Description = $description;
		$contains->ContentDescription->Language = "FR";
		$contains->ContentDescription->Language['listVersionId'] = "edition 2009";
		$contains->ContentDescription->LatestDate = $latestDate;
		$contains->ContentDescription->OldestDate = $oldestDate;
		return $contains;
	}
	
	
	
}