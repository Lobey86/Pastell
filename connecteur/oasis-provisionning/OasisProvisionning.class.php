<?php 

class OasisProvisionning extends Connecteur {
	
	private $donneesFormulaire;
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->donneesFormulaire = $donneesFormulaire;
	}
	
	
	public function addInstance($instance_raw_data,$x_hub_signature){
		$hmac = "sha1=".strtoupper((hash_hmac('sha1', $instance_raw_data, $this->donneesFormulaire->get('api_provisionning_secret'), false)));
		if ($hmac != $x_hub_signature){
			throw new Exception("Le HMAC ne correspond pas");
		}
		
		$instance_info = json_decode($instance_raw_data,true);
		if (! $instance_info){
			throw new Exception("Impossible de décoder les données de l'instance");
		}
		$organization_name = $instance_info['organization_name'];
		if (! $organization_name){
			throw new Exception("Le nom de l'organisation est vide");
		}
		$file_name = Field::Canonicalize($organization_name).".json";
		
		if (! $this->donneesFormulaire->get('instance_en_attente')){
			$num_field = 0;
		} else {
			$num_field = count($this->donneesFormulaire->get('instance_en_attente')) ;
		}
		
		$this->donneesFormulaire->addFileFromData('instance_en_attente', $file_name, $instance_raw_data,$num_field);
	}
	
	public function getNextInstance(){
		$instance_data = $this->donneesFormulaire->getFileContent('instance_en_attente');
		if (! $instance_data){
			throw new Exception("Il n'y a pas d'instance en attente");
		}
		return json_decode($instance_data,true);
	}
	
	
	
	
	
	
}