<?php 

class HeliosSEDALocarchive extends SEDAConnecteur {
	
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
		$info = array('unique_id','date','description','pes_description','pes_retour_description','pes_aller','pes_retour','pes_aller_content');		
		
		foreach($info as $key){
			if (empty($information[$key])){
				throw new Exception("Impossible de générer le bordereau : le paramètre $key est manquant. ");
			}
		}
	}

	private function getSubjectFromPESAller($pes_aller_content){
		$xml = simplexml_load_string($pes_aller_content);
		if (! $xml){
			throw new Exception("Impossible de lire le contenu du fichier PES aller");
		}
		return strval($xml->Enveloppe->Parametres->NomFic['V']);
	}
	
	public function getBordereau(array $transactionsInfo){
		$this->checkInformation($transactionsInfo);
				
		$archiveTransfer = new ZenXML('ArchiveTransfer');
		$archiveTransfer['xmlns:xsi']="http://www.w3.org/2001/XMLSchema-instance";
		$archiveTransfer['xsi:schemaLocation'] = "fr:gouv:ae:archive:draft:standard_echange_v0.2 archives_echanges_v0-2_archivetransfer.xsd";
		$archiveTransfer->Comment = "Transfert d'un fichier PES";
		$archiveTransfer->Date = date('c');//"2011-08-12T11:03:32+02:00";
		
		
		$archiveTransfer->TransferIdentifier = $transactionsInfo['unique_id'];
		
		
		
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
		//TODO
		$archiveTransfer->Contains->Name = "Flux comptable PES en date du {$transactionsInfo['date']} de la collectivité {$this->authorityInfo['nom_entite']}";
		
		
		
		$archiveTransfer->Contains->ContentDescription->CustodialHistory = "";
		$archiveTransfer->Contains->ContentDescription->Description = $transactionsInfo['description'];
		
		$archiveTransfer->Contains->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		
		$latestDate = date('Y-m-d',strtotime($transactionsInfo['date'] ));
		$oldestDate = date('Y-m-d',strtotime($transactionsInfo['date']));
		
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
		//TODO PES = Numéro de mandat présent dans le bordereau (obligatoire)
		$archiveTransfer->Contains->Contains[0]->Contains[0] = $this->getContainsElementWithDocument("PES",array(basename($transactionsInfo['pes_aller'])),$latestDate,$oldestDate);		
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->OtherMetadata->controlaccess->subject = $this->getSubjectFromPESAller($transactionsInfo['pes_aller_content']);
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->OtherMetadata->controlaccess->subject['altrender'] = "titre_pes";
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->OtherMetadata->controlaccess->subject['type'] = "numero";
		
		
		
		$archiveTransfer->Contains->Contains[0]->Contains[1] = $this->getContainsElementWithDocument($transactionsInfo['pes_retour_description'],
				array(basename($transactionsInfo['pes_retour'])),$latestDate,$oldestDate);
		
		
		
		return $archiveTransfer->asXML();
	}
	
	private function getContainsElementWithDocument($description,array $allFileInfo,$latestDate,$oldestDate){
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
	
			$contains->Document[$i]->Description = "$description";
		
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