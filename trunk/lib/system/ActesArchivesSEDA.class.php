<?php 

class ActesArchiveSEDA {
	
	private $lastError;
	
	private $tmpFolder;
	private $file2Add;
	
	private $authorityInfo;
	private $actesTransactionsStatusInfo;
	
	private $actesFilePath;
	private $annexe;
	private $arActes;
	
	
	public function __construct($tmpFolder){
		$this->tmpFolder = $tmpFolder;
		$this->file2Add = array();
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function setAuthorityInfo(array $authorityInfo){
		assert('$authorityInfo["sae_id_versant"]');
		assert('$authorityInfo["sae_id_archive"]');
		assert('$authorityInfo["sae_numero_aggrement"]');
		assert('$authorityInfo["name"]');
		assert('$authorityInfo["sae_originating_agency"]');
		assert('$authorityInfo["siren"]');
		
		$this->authorityInfo = $authorityInfo;
	}
	
	public function setActesFileName($actesFileName){
		$this->actesFileName =  $actesFileName;
		$this->file2Add[] = $actesFileName;
	}
	
	public function setTransactionStatusInfo(array $actesTransactionsStatusInfo){
		assert('$actesTransactionsStatusInfo["transaction_id"]');
		assert('$actesTransactionsStatusInfo["flux_retour"]');
		assert('$actesTransactionsStatusInfo["date"]');
		
		$this->actesTransactionsStatusInfo = $actesTransactionsStatusInfo;
		$this->arActes = "ARActes-{$actesTransactionsStatusInfo['transaction_id']}.xml";
		file_put_contents($this->tmpFolder . $this->arActes,$actesTransactionsStatusInfo['flux_retour']);
		$this->file2Add[] = $this->arActes;
	}
	
	public function addAnnexe($annexeFileName,$annexeFileType){
		$this->annexe[] = array($annexeFileName,$annexeFileType);
		$this->file2Add[] = $annexeFileName;
	}

	public function getArchive(){
		$fileName = uniqid().".tar.gz";
		$command = "tar cvzf {$this->tmpFolder}/$fileName --directory {$this->tmpFolder} " . implode(" ",$this->file2Add);
		$status = exec($command );
		if (! $status){
			$this->lastError = "Impossible de créer le fichier d'archive $fileName";
			return false;
		}
		return $this->tmpFolder."$fileName";
	}
	
	public function getBordereau($transactionsInfo){
		$archiveTransfer = new ZenXML('ArchiveTransfer');
		$archiveTransfer['xmlns'] = "fr:gouv:ae:archive:draft:standard_echange_v0.2";
		$archiveTransfer->Comment = "Transfert d'un acte soumis au contrôle de légalité";
		$archiveTransfer->Date = date('c');//"2011-08-12T11:03:32+02:00";
		$archiveTransfer->TransferIdentifier = $transactionsInfo['unique_id'];
		$archiveTransfer->TransferIdentifier['schemeName'] = "Codification interne";
		
		$archiveTransfer->TransferringAgency->Identification = $this->authorityInfo['sae_id_versant'];
		$archiveTransfer->ArchivalAgency->Identification = $this->authorityInfo['sae_id_archive'];
		
		foreach($this->file2Add as $i => $fileName){
			$archiveTransfer->Integrity[$i]->Contains = sha1_file($this->tmpFolder.$fileName);
			$archiveTransfer->Integrity[$i]->UnitIdentifier = $fileName;
		}
		
		$archiveTransfer->Contains->ArchivalAgreement = $this->authorityInfo['sae_numero_aggrement'];
		$archiveTransfer->Contains->ArchivalAgreement['schemeName'] = "Convention de transfert";
		$archiveTransfer->Contains->ArchivalAgreement['schemeAgencyName'] = $this->authorityInfo['name'];
		
		$archiveTransfer->Contains->ArchivalProfile = "ACTES 1.4";
		$archiveTransfer->Contains->ArchivalProfile['schemeName'] = "Profil de données";
		
		$archiveTransfer->Contains->DescriptionLanguage = "fr";
		$archiveTransfer->Contains->DescriptionLanguage['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->DescriptionLevel = "file";
		$archiveTransfer->Contains->DescriptionLevel['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Name = $transactionsInfo['unique_id'];
		
		$archiveTransfer->Contains->ContentDescription->CustodialHistory = "Actes dématérialisés soumis au contrôle de légalité, les données archivées sont structurées selon le schéma Actes (Aide au contrôle de légalité dématérialisé) établi par le ministère de l'intérieur, de l'outre mer et des collectivités territoriales. La description a été établie selon les règles du standard d'échange de données pour l'archivage version 0.2";
			
		$archiveTransfer->Contains->ContentDescription->Description = $transactionsInfo['subject'];
		
		$archiveTransfer->Contains->ContentDescription->Language = "fr";
		$archiveTransfer->Contains->ContentDescription->Language['listVersionID'] = "edition 2009";
		
		$archiveTransfer->Contains->ContentDescription->LatestDate = date('Y-m-d',strtotime($this->actesTransactionsStatusInfo['date'] ." + 2 month"));
		$archiveTransfer->Contains->ContentDescription->OldestDate = date('Y-m-d',strtotime($transactionsInfo['decision_date']));
		
		$archiveTransfer->Contains->ContentDescription->OriginatingAgency->Identification = $this->authorityInfo['sae_originating_agency'];
	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordContent = $this->authorityInfo['name'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference = $this->authorityInfo['siren'];
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeName'] = "SIRENE";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordReference['schemeAgencyName'] = "INSEE";	
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType = "corpname";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[0]->KeywordType["listVersionID"] = "edition 2009";
		
		
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordContent = "Contrôle de légalité";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeName'] = "Thésaurus pour la description et l'indexation des archives locales anciennes, modernes et contemporaines_liste d'autorité Actions";
		$archiveTransfer->Contains->ContentDescription->ContentDescriptive[1]->KeywordReference['schemeAgencyName'] = "Direction des archives de france";	
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
		
		if ($transactionsInfo['classification'][0] != 9 ){
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordContent = $this->getSujetActes($transactionsInfo['classification']);
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeName'] = "Thésaurus pour la description et l'indexation des archives locales anciennes, modernes et contemporaines_liste d'autorité Actions";
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeAgencyName'] = "Direction des archives de france";	
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeDataURI'] = "http://www.archivesdefrance.culture.gouv.fr/gerer/classement/normesoutils/thesaurus/";	
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordReference['schemeVersionID'] = "version 2009";			
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType = "subject";
			$archiveTransfer->Contains->ContentDescription->ContentDescriptive[3]->KeywordType["listVersionID"] = "edition 2009";	
		}
		
		$archiveTransfer->Contains->Appraisal->Code = "detruire";
		$archiveTransfer->Contains->Appraisal->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->Appraisal->Duration = $this->getDuration($transactionsInfo['nature_code']);
		$archiveTransfer->Contains->Appraisal->StartDate = date('Y-m-d',strtotime($this->actesTransactionsStatusInfo['date'] ." + 2 month"));
	
		
		$archiveTransfer->Contains->AccessRestriction->Code = $this->getAccessRestriction($transactionsInfo['classification'],$transactionsInfo['nature_code']);
		$archiveTransfer->Contains->AccessRestriction->Code['listVersionID'] = "edition 2009";
		$archiveTransfer->Contains->AccessRestriction->StartDate = date('Y-m-d',strtotime($this->actesTransactionsStatusInfo['date'] ." + 2 month"));
		
			
		$archiveTransfer->Contains->Contains[0] = $this->getContainsElement("Transmission d'un acte soumis au contrôle de légalité");
		$archiveTransfer->Contains->Contains[0]->Contains[0] = $this->getContainsElementWithDocument("Actes",array($this->actesFileName));
		
		if($this->annexe){
			$archiveTransfer->Contains->Contains[0]->Contains[] = $this->getContainsElementWithDocument("Annexe(s) d'un acte soumis au contrôle de légalité",$this->annexe);
		}

		$arActes = $this->getContainsElementWithDocument("Accusé de réception d'un acte soumis au contrôle de légalité",
															array($this->arActes),
															$this->actesTransactionsStatusInfo['date']
															);
		
		unset($arActes->Document[0]->Attachment['mimeCode']);
		$archiveTransfer->Contains->Contains[0]->Contains[] = $arActes;
		
		return $archiveTransfer->asXML();
	}
	
	private function getContainsElementWithDocument($description,array $allFileInfo,$receiptDate = false){
		$contains = new ZenXML("Contains");
		$contains->DescriptionLevel = "item";
		$contains->DescriptionLevel['listVersionID'] = "edition 2009";
		$contains->Name =  $description ;
		foreach($allFileInfo as $i => $fileInfo){
			if (is_array($fileInfo)){
				$fileName = $fileInfo[0];
				$fileType = $fileInfo[1];
			} else  {
				$fileName = $fileInfo;
				$fileType = "application/pdf";
			}
			$contains->Document[$i]->Attachment['mimeCode'] = $fileType;
			$contains->Document[$i]->Attachment['filename'] = $fileName;
			$contains->Document[$i]->Control = "false";
			$contains->Document[$i]->Copy = "true";
			$contains->Document[$i]->Description = "Acte";
			if ($receiptDate) {
				$contains->Document[$i]->Receipt = date('c',strtotime($receiptDate));
			}
			$contains->Document[$i]->Type = "CDO";
			$contains->Document[$i]->Type["listVersionID"] = "edition 2009";
		}
		return $contains;
	}
	
	public function getContainsElement($description){
		$contains = new ZenXML("Contains");		
		$contains->DescriptionLevel = "file";
		$contains->DescriptionLevel['listVersionID'] = "edition 2009";
		$contains->Name = $description;
		return $contains;
	}
	
	private function getSujetActes($classification){

		$info = array(	"1" => "Commande publique" ,
						"2" => "Urbanisme", 
						"3" => "Domaine et patrimoine",
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
						"8" => "Education",
						"8.2" => "Protection sociale",
						"8.3" => "Réseau routier",
						"8.4" => "Aménagement du territoire",
						"8.5" => "Politique de la ville, Immobilier",
						"8.6" => "Emploi",
						"8.7" => "Transport",
						"8.8" => "Environnement",
						"8.9" => "Culture",);
		$debut = substr($classification,0,3);
		
		if (isset($info[$debut])){
			return $info[$debut];
		}
		
		$debut = substr($classification,0,1);
		if (isset($info[$debut])){
			return $info[$debut];
		}
		
		throw new Exception("La classification de cet actes est inconnu");
		
	}
	
	public function getAccessRestriction($classification,$nature){
		if ($classification[0] == 4 && in_array($nature,array(3,4))){
			return "AR048";
		}
		return "AR038";
	}
	
	public function getDuration($nature){
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