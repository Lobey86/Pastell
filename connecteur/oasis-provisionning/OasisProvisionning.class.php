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
	
	public function deleteNextInstance(){
		$thisdonneesFormulaire->removeFile('instance_en_attente',0);
	}
	
	
	public function aknowledge(array $instance_info,$id_e){
		$url = $instance_info['instance_registration_uri'];
		$client_id = $instance_info['client_id'];
		$client_secret = $instance_info['client_secret'];
		$instance_id = $instance_info['instance_id'];
		
		$data = array(
				"services"=>array(array('local_id'=>'pastell',
						"service_uri" => SITE_BASE."/oasis/connexion.php?id_e=$id_e",
						"visible" => true,
						"name" => "Pastell",
						"description" => false,
						"tos_uri"=>SITE_BASE,
						"policy_uri"=>SITE_BASE,
						"icon"=>SITE_BASE,
						"contacts" => array(SITE_BASE),
						"payment_option"=>"FREE",
						"target_audience" => array("PUBLIC_BODIES"),
						"redirect_uris" => array(SITE_BASE),
				)),
				"instance_id"=>$instance_id,
		);
		
		$ch = curl_init();
		
		curl_setopt($ch, CURLOPT_USERPWD, "$client_id:$client_secret");
		curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST,true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER , 1);
		
		$curlHttpHeader[] = "Content-Type: application/json";
		
		curl_setopt($ch, CURLOPT_HTTPHEADER, $curlHttpHeader);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
		
		$output = curl_exec($ch);
		
		if (curl_getinfo($ch,CURLINFO_HTTP_CODE) != "201"){
			throw new Exception("Erreur lors de la création de l'instance : $output");
		}
		
		return true;
	}
	
	
	
}