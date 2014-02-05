<?php
class UpdateCertificate extends ActionExecutor {
	
	public function go(){
		$connecteur_properties = $this->getConnecteurProperties();
		
		$pkcs12 = new PKCS12();
		$p12_data = $pkcs12->getAll($connecteur_properties->getFilePath('user_certificat'),
										$connecteur_properties->get('user_certificat_password'));
		
		if ($p12_data){
			$connecteur_properties->addFileFromData("user_certificat_pem","user_certificat_pem",$p12_data['cert']); 
			$connecteur_properties->addFileFromData("user_key_pem","user_key_pem",$p12_data['pkey']); 
		}
		
		$this->setLastMessage("Certificat à jour");		
		return true;

	}
	
}