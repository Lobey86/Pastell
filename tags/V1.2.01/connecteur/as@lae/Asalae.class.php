<?php
class Asalae extends SAEConnecteur {
	
	private $WDSL;
	private $login;
	private $password;
	
	private $lastErrorCode;
	
	public function  setConnecteurConfig(DonneesFormulaire $collectiviteProperties){
		$this->WSDL = $collectiviteProperties->get('sae_wsdl');
		$this->login = $collectiviteProperties->get('sae_login');
		$this->password = $collectiviteProperties->get('sae_password');
	}

	public function getLastErrorCode(){
		return $this->lastErrorCode;
	}
	
	public function sendArchive($bordereauSEDA,$archivePath,$file_type="TARGZ",$archive_file_name="archive.tar.gz"){
		$client = new SoapClient($this->WSDL);
		$document_content  = file_get_contents($archivePath);
		
		$retour  = @ $client->__soapCall("wsDepot", array(	"bordereau.xml", 
															$bordereauSEDA,
															$archive_file_name, 
															$document_content, 
															$file_type,
															$this->login,
															$this->password));
		if ($retour !== "0"){
			$this->lastError = "Erreur lors du dépot : le service d'archive a retourné :  $retour";
			return false;	
		}
		return true;
		
	}
	
	public function getAcuseReception($id_transfert){
		return $this->getMessage($id_transfert,'ArchiveTransferAcknowledgement');
	}
	
	public function getReply($id_transfer){
		return $this->getMessage($id_transfer,'ArchiveTransferReply');
	}
	
	private function getMessage($id_transfer, $type_message){
		$client = new SoapClient($this->WSDL);
		$resultat = $client->wsGetMessage('ArchiveTransfer', 
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
		$this->lastErrorCode = $resultat;
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
	
	public function getURL($cote){
		$tab = parse_url($this->WSDL);
		return "{$tab['scheme']}://{$tab['host']}/archives/viewByArchiveIdentifier/$cote";
	}
}