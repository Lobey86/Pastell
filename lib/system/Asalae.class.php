<?php
////mail : pastell@sigmalis.com AisohM6D

class Asalae {
	
	private $lastError;
	
	private $WDSL;
	private $login;
	private $password;
	private $identifiantVersant;
	private $originatingAgency;
	private $identifiantArchive;
	private $numeroAgrement;
	
	public function __construct(array $authorityInfo){
		$this->WSDL = $authorityInfo['sae_wsdl'];
		$this->login = $authorityInfo['sae_login'];
		$this->password = $authorityInfo['sae_password'];
		$this->numeroAgrement = $authorityInfo['sae_numero_aggrement'];
	}

	public function getLastError(){
		return $this->lastError;
	}
	
	public function sendArchive($bordereauSEDA,$archivePath,$file_type="TARGZ"){
		$seda = base64_encode($bordereauSEDA);
		if (! $seda){
			return false;
		}
		$client = new SoapClient($this->WSDL);

		$document_content  = base64_encode(file_get_contents($archivePath));
		
		$retour  = $client->__soapCall("wsDepot", array("bordereau.xml", $seda,basename($archivePath), $document_content, $file_type,$this->login,$this->password));
		if ($retour == 0){
			return true;
		}
		
		$this->lastError = "Erreur lors du dépot : le service d'archive a retourné :  $retour";
		return false;
	}
	
	public function getAcuseReception($id_transfert){
		return $this->getMessage($id_transfert,'ArchiveTransferAcknowledgement');
	}
	
	public function getReply($id_transfer){
		return $this->getMessage($id_transfer,'ArchiveTransferReply');
	}
	
	private function getMessage($id_transfer, $type_message){
		$client = new SoapClient($this->WSDL);
		$resultat = $client->wsGetMessage(	'ArchiveTransfer', 
											 $type_message,
											$id_transfer, 
											$this->login,
											$this->password);
		if ( intval($resultat) == 0){
			return $resultat;
		}
		
		$error = array( 1 => "identifiant de connexion non trouvé",
							"mot de passe incorrect",
							"connecteur non actif",
							"type de l'échange non reconnu",
							"type de message non reconnu",
							"acteur Seda (service) non lié au connecteur dans as@lae",
							"message origine non trouvé",
							"message demandé non trouvé",
		);
		$this->lastError  = "Code $resultat : {$error[$resultat]}";
		return false;
	}
	
	public function getErrorString($number){
		$error = array("connexion réussie","identifiant de connexion inconnu","mot de passe incorrect","connecteur non actif");
		if (empty($error[$number])){
			return "erreur As@lae inconnu ($number)";
		}
		return $error[$number];
	}
	
	public function getURL($wsdl,$cote){
		$tab = parse_url($wsdl);
		return "{$tab['scheme']}://{$tab['host']}/archives/viewByArchiveIdentifier/$cote";
	}
	

}
