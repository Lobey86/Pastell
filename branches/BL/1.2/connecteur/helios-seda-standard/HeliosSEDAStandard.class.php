<?php 
class HeliosSEDAStandard extends Connecteur {
	
	private $authorityInfo;
	
	public function  setConnecteurConfig(DonneesFormulaire $seda_config){
		$this->authorityInfo = array(
				"sae_id_versant" =>  $seda_config->get("sae_identifiant_versant"),
				"sae_id_archive" =>  $seda_config->get("sae_identifiant_archive"),
				"sae_numero_aggrement" =>  $seda_config->get("sae_numero_agrement"),
				"sae_originating_agency" =>  $seda_config->get("sae_originating_agency"),
				"name" =>   $seda_config->get('denomination'),
				"siren" =>  $seda_config->get('siren'),
		);
	}
	
	public function checkInformation(array $information){
		$info = array('unique_id','date','description','pes_description','pes_retour_description','pes_aller','pes_retour');		
		foreach($info as $key){
			if (empty($information[$key])){
				throw new Exception("Impossible de générer le bordereau : le paramètre $key est manquant. ");
			}
		}
	}
	
	public function getBordereau($transactionsInfo){
		$this->checkInformation($transactionsInfo);
		$archiveTransfer = new ZenXML('ArchiveTransfer');
		$archiveTransfer['xmlns'] = "fr:gouv:ae:archive:draft:standard_echange_v0.2";
		$archiveTransfer->Comment = "Transfert d'un flux comptable conforme au PES V2";
		$archiveTransfer->Date = date('c');//"2011-08-12T11:03:32+02:00";
		
		$archiveTransfer->TransferIdentifier = $transactionsInfo['unique_id'];
		$archiveTransfer->TransferIdentifier['schemeName'] = "Adullact Projet";
		
		$archiveTransfer->TransferringAgency->Identification = $this->authorityInfo['siren'];
		$archiveTransfer->TransferringAgency->Identification['schemeName'] = "SIRENE";
		$archiveTransfer->TransferringAgency->Identification['schemeAgencyName'] = "INSEE";
		$archiveTransfer->TransferringAgency->Name = $this->authorityInfo['name'];
		$archiveTransfer->ArchivalAgency->Identification = $this->authorityInfo['sae_id_archive'];		
		
		$i = 0;
		foreach(array('pes_aller','pes_retour') as $file_to_add){
			$file_path = $transactionsInfo[$file_to_add];
			$archiveTransfer->Integrity[$i]->Contains = sha1_file($file_path);
			$archiveTransfer->Integrity[$i]->UnitIdentifier = basename($file_path);
			$i++;
		}
		
		$archiveTransfer->Contains->ArchivalAgreement = $this->authorityInfo['sae_numero_aggrement'];
		$archiveTransfer->Contains->ArchivalAgreement['schemeName'] = "Convention de transfert";
		$archiveTransfer->Contains->ArchivalAgreement['schemeAgencyName'] = $this->authorityInfo['name'];
		
		$archiveTransfer->Contains->ArchivalProfile = "Profil flux comptable PES V2";
		$archiveTransfer->Contains->ArchivalProfile['schemeAgencyName'] = "Adullact Projet";
				
		$archiveTransfer->Contains->DescriptionLanguage = "fr";
		$archiveTransfer->Contains->DescriptionLanguage['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->DescriptionLevel = "file";
		$archiveTransfer->Contains->DescriptionLevel['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->Name = "Flux comptable PES en date du {$transactionsInfo['date']} de la collectivité {$this->authorityInfo['name']}";
		
		$archiveTransfer->Contains->ContentDescription->CustodialHistory = "Les pièces transférées au comptable public, sont intégrées au flux comptable PES V2 défini par le programme Helios et sont transférées pour archivage depuis le tiers de télétransmission ou la passerelle Helios pour le compte de {$this->authorityInfo['name']}. La description a été établie selon les règles du standard d'échange de données pour l'archivage électronique version 0.2, publié dans le référentiel général d'interopérabilité.";
		$archiveTransfer->Contains->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->ContentDescription->LatestDate = date('Y-m-d',strtotime($transactionsInfo['date']));		
		$archiveTransfer->Contains->ContentDescription->OldestDate = date('Y-m-d',strtotime($transactionsInfo['date']));		
		
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Identification = $this->authorityInfo['sae_originating_agency'];
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordContent = $this->authorityInfo['name'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference = $this->authorityInfo['siren'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeName'] = "SIRENE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeAgencyName'] = "INSEE";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType = "corpname";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType["listVersionID"] = "edition 2009";
	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordContent = "Comptabilité publique";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference = "COMPTABILITE PUBLIQUE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeName'] = "Thésaurus matière";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeAgencyName'] = "Direction des archives de france";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeDataURI'] = "http://www.archivesdefrance.culture.gouv.fr/gerer/classement/normesoutils/thesaurus/";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeVersionID'] = "version 2009";			
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType = "subject";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordType["listVersionID"] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordContent = "Pièce comptable";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference = "PIECE COMPTABLE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeName'] = "Liste d'autorité Typologie documentaire";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeAgencyName'] = "Direction des Archives de France";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeDataURI'] = "http://www.archivesdefrance.culture.gouv.fr/gerer/classement/normesoutils/thesaurus/";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordReference['schemeVersionID'] = "version 2009";			
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType = "genreform";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[2]->KeywordType["listVersionID"] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordContent = "Livre comptable";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference = "Livre comptable";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeName'] =  "Liste d'autorité Typologie documentaire";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeAgencyName'] = "Direction des archives de france";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeDataURI'] = "http://www.archivesdefrance.culture.gouv.fr/gerer/classement/normesoutils/thesaurus/";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeVersionID'] = "version 2009";			
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType = "subject";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType["listVersionID"] = "edition 2009";	
		
		$archiveTransfer->Contains->Appraisal->Code = "detruire";
		$archiveTransfer->Contains->Appraisal->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Appraisal->Duration = "P10Y";
		$archiveTransfer->Contains->Appraisal->StartDate =  date('Y-m-d',strtotime($transactionsInfo['date']));
	
		$archiveTransfer->Contains->AccessRestriction->Code = "AR038";
		$archiveTransfer->Contains->AccessRestriction->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->AccessRestriction->StartDate = date('Y-m-d',strtotime($transactionsInfo['date']));
		
		$archiveTransfer->Contains->Contains[0]->DescriptionLevel = "item";
		$archiveTransfer->Contains->Contains[0]->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Contains[0]->Name = "PES";
		
		$archiveTransfer->Contains->Contains[0]->ContentDescription->Description = $transactionsInfo['description'];
		$archiveTransfer->Contains->Contains[0]->ContentDescription->Language='fr';
		$archiveTransfer->Contains->Contains[0]->ContentDescription->Language['listVersionID'] = "edition 2009";

		$archiveTransfer->Contains->Contains[0]->Document->Attachment['format'] = 'fmt/101';
		$archiveTransfer->Contains->Contains[0]->Document->Attachment['mimeCode'] = 'text/xml';
		$archiveTransfer->Contains->Contains[0]->Document->Attachment['filename'] = basename($transactionsInfo['pes_aller']);
		$archiveTransfer->Contains->Contains[0]->Document->Description="PES";
		$archiveTransfer->Contains->Contains[0]->Document->Type = "CDO";
		$archiveTransfer->Contains->Contains[0]->Document->Type['listVersionID'] = "edition 2009";

		
		$archiveTransfer->Contains->Contains[0]->Contains[0]->DescriptionLevel = "item";
		$archiveTransfer->Contains->Contains[0]->Contains[0]->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Contains[0]->Contains[0]->Name = "Bordereau et pièces justificatives";
		
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->Description = $transactionsInfo['pes_retour_description'];
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->Contains[0]->Contains[0]->ContentDescription->Language['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->Contains[0]->Contains[1]->DescriptionLevel = "item";
		$archiveTransfer->Contains->Contains[0]->Contains[1]->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Contains[0]->Contains[1]->Name = "PES ACK/NACK";
		
		$archiveTransfer->Contains->Contains[0]->Contains[1]->Document->Attachment['format'] = 'fmt/101';
		$archiveTransfer->Contains->Contains[0]->Contains[1]->Document->Attachment['mimeCode'] = 'text/xml';
		$archiveTransfer->Contains->Contains[0]->Contains[1]->Document->Attachment['filename'] = basename($transactionsInfo['pes_retour']);
		$archiveTransfer->Contains->Contains[0]->Contains[1]->Document->Description="PES ACK/NACK";
		$archiveTransfer->Contains->Contains[0]->Contains[1]->Document->Type = "CDO";
		$archiveTransfer->Contains->Contains[0]->Contains[1]->Document->Type['listVersionID'] = "edition 2009";

		return $archiveTransfer->asXML();
	}
	
}