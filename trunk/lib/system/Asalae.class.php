<?php


class Asalae {
	
	private $lastError;
	
	private $collectiviteProperties;
	private $WDSL;
	private $login;
	private $password;
	private $identifiantVersant;
	
	private $identifiantArchive;
	private $numeroAgrement;
	
	public function __construct(DonneesFormulaire $collectiviteProperties){
		$this->collectiviteProperties = $collectiviteProperties;
		$this->WSDL = $collectiviteProperties->get('sae_wsdl');
		$this->login = $collectiviteProperties->get('sae_login');
		$this->password = $collectiviteProperties->get('sae_password');
		$this->identifiantVersant = $collectiviteProperties->get('sae_identifiant_versant');
		$this->identifiantArchive = $collectiviteProperties->get('sae_identifiant_archive');
		$this->numeroAgrement = $collectiviteProperties->get('sae_numero_agrement');
	}

	public function getLastError(){
		return $this->lastError;
	}
	
	public function testSEDA(){
		return $this->sendArchive("test.txt","Ceci est un test","test de dépot");
	}
	
	public function sendArchive($filename,$content,$description){
		$document = $this->getDocumentInfo($description);
		$seda = $this->generateSEDA($document);
		if (! $seda){
			return false;
		}
		$client = new SoapClient($this->WSDL);

		$document  = "xyz";

		$retour  = $client->__soapCall("wsDepot", array("bordereau.xml", $seda, "versement.tgz", $document, "",$this->login,$this->password));
		if (intval($seda) ){			
			$this->lastError = "Erreur lors du dépot :" . $this->getErrorString($seda);
			return false;
		}
		return true;
	}
	
	
	
	public function getDocumentInfo($description){
		
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
	
	public function generateSEDA($document){
		
		
		$num_archive = mt_rand();
		$objet_archive = "Test génération SEDA";
		$provenance_archive = $this->identifiantVersant;
		$identifiant_producteur = $this->identifiantArchive; 
		
		$options = array( 'TransferIdentifier' => $num_archive,
                      'Comment'            => utf8_encode($objet_archive),
                      'TransferringAgency' => array('Identification' => $this->identifiantVersant),
                      'ArchivalAgency'     => array('Identification' => $this->identifiantArchive),
                      'Contains'           => array( 'ArchivalAgreement' => $this->numeroAgrement,
                                                     'DescriptionLanguage' => array(
                                                     	'@attributes' => array('listVersionID' => 'edition 2009'),
                                                     	'@value' => 'fr'),
                                                     'DescriptionLevel' => array(
                                                     	'@attributes' => array('listVersionID' => 'edition 2009'),
                                                     	'@value' => 'file'),
                                                     'Name'=> utf8_encode($objet_archive),
                                                     'ContentDescription' => array('CustodialHistory' => utf8_encode($provenance_archive),
                                                                                   'Description' => utf8_encode($objet_archive),
                                                                                   'DescriptionAudience'  => 'external',
                                                                                   'Language' => array(
                                                                                      '@attributes' => array('listVersionID' => 'edition 2009'),
                                                                                      '@value' => 'fr'),
                                                                                   'OriginatingAgency'     => array('Identification'=>$identifiant_producteur),
                                                                                                                    'ContentDescriptive' => array( 'KeywordAudience'=>'external',
                                                                                                                                               'KeywordContent' =>'Deliberation',
                                                                                                                                               'KeywordReference' =>'1',
                                                                                                                                               'KeywordType' => 'genreform'),
                                                                                                                    'Appraisal'=>array('Code'=>'001C',
                                                                                                                                       'StartDate'=>date('c'))),
                                                                                   'Document' => $document)
                                                   );
        
		$client = new SoapClient($this->WSDL);

		$seda = $client->wsGSeda($options,$this->login,$this->password);
		if (intval($seda) ){			
			$this->lastError = "Erreur lors de la génération du bordereau : " . $this->getErrorString($seda);
			return false;
		}
		return $seda;
	}
	
	public function getErrorString($number){
		$error = array("connexion réussie","identifiant de connexion inconnu","mot de passe incorrect","connecteur non actif");
		if (empty($error[$number])){
			return "erreur As@lae inconnu ($number)";
		}
		return $error[$number];
	}

}
