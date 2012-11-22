<?php

class BordereauSEDA {
	
	private $identifiantVersant;
	
	public function __construct($identifiantVersant, $identifiantArchive,$numeroAgrement,$originatingAgency){
		$this->identifiantVersant = $identifiantVersant;
		$this->identifiantArchive = $identifiantArchive;
		$this->numeroAggrement = $numeroAgrement;		
		$this->originatingAgency = $originatingAgency;
	}

	public function getBordereau($objet_archive,$description){
		
		$bordereau['TransferIdentifier'] = mt_rand();
		$bordereau['Comment'] = utf8_encode($objet_archive);
		$bordereau['Date'] = date("c");
		$bordereau['TransferringAgency']['Identification'] = $this->identifiantVersant;
		$bordereau['ArchivalAgency']['Identification'] =  $this->identifiantArchive;
		$bordereau['Contains']['ArchivalAgreement'] = $this->numeroAggrement;
		$bordereau['Contains']['DescriptionLanguage']['@attributes']['listVersionID']='edition 2009';
		$bordereau['Contains']['DescriptionLanguage']['@value'] = 'fr';
		$bordereau['Contains']['DescriptionLevel']['@attributes']['listVersionID']='edition 2009';
		$bordereau['Contains']['DescriptionLevel']['@value'] = 'file';
		$bordereau['Contains']['Name'] =  utf8_encode($objet_archive);
		$bordereau['Contains']['ContentDescription'] = $this->getContentDescription($objet_archive);
		$bordereau['Contains']['Document']= $this->getDocumentInfo($description);
		return $bordereau;
	}
	
	private function getDocumentInfo($description){		
		$result['Description'] = utf8_encode($description);
		$result['Attachment'] = array();
		$result['Type']['@attributes']['listVersionID'] = "edition 2009";
		$result['Type']['@value'] = "CDO";
		$result['Attachment']['@attributes']['format'] = "fmt/18";
		$result['Attachment']['@attributes']['mimeCode'] = "application/pdf";
		$result['Attachment']['@attributes']['filename'] = "archive.pdf";
		$result['Attachment']['@value'] = "";
		return $result;
	}
	
	private function getContentDescription($objet_archive){
		$result['CustodialHistory'] = $this->identifiantVersant;
		$result['Description'] = utf8_encode($objet_archive);
		$result['DescriptionAudience'] = 'external';
		$result['Language']['@attributes']['listVersionID'] = 'edition 2009';
		$result['Language']['@value'] = 'fr';
		$result['OriginatingAgency']['Identification'] =  $this->originatingAgency;
		
		$result['ContentDescriptive']['KeywordAudience'] = 'external';
		$result['ContentDescriptive']['KeywordContent'] = 'Deliberation';
		$result['ContentDescriptive']['KeywordReference'] = '1';
		$result['ContentDescriptive']['KeywordType']['@attributes']['listVersionID'] = 'edition 2009';
		$result['ContentDescriptive']['KeywordType']['@value'] = 'genreform';
		
		$result['Appraisal']['Code'] =  '001C'; 
		$result['Appraisal']['StartDate'] =  date('c'); 
															
		return $result;
	}
	
	
	
	
}