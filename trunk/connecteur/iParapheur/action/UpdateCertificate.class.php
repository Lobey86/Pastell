<?php
class UpdateCertificate extends ActionExecutor {
	
	public function go(){
		$connecteur_properties = $this->getConnecteurProperties();
		
		$pkcs12 = new PKCS12();
		$p12_data = $pkcs12->getAll($connecteur_properties->getFilePath('iparapheur_user_certificat'),
										$connecteur_properties->get('iparapheur_user_certificat_password'));
		
		if ($p12_data){
			$connecteur_properties->addFileFromData("iparapheur_user_key_pem","iparapheur_user_key_pem",$p12_data['pkey'].$p12_data['cert']); 
		}
		
		$this->setLastMessage("Certificat à jour");		
		return true;

	}
	
}