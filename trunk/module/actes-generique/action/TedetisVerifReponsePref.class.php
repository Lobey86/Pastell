<?php 
class TedetisVerifReponsePref extends ActionExecutor {

	private $many_same_message;
	
	public function go(){
	
		$tdT = $this->getConnecteur("TdT"); 
		
		if (!$tdT){
			throw new Exception("Aucun Tdt disponible");
		}
		$tedetis_transaction_id = $this->getDonneesFormulaire()->get('tedetis_transaction_id');
		
		
		$all_response = $tdT->getListReponsePrefecture($tedetis_transaction_id);
		
		if (!$all_response)  {
			$this->setLastMessage("Aucune réponse disponible");
			return true;
		}
		foreach($all_response as $response){
			$this->saveAutreDocument($response);
		}
		
		$last_action = $this->getDocumentActionEntite()->getLastActionNotModif($this->id_e,$this->id_d);
		$this->verifReponseAttendu($last_action);
		
		if ($this->many_same_message){
			$this->setLastMessage("Attention, il y a plusieurs messages de même type, cette situation n'est pas traitée par Pastell : ".implode(",",$this->many_same_message));
			return false;
		}
		
		$this->setLastMessage("Réponses récupérées");
		return true;
		
	}
	
	private function verifReponseAttendu($last_action){
		if ($last_action == 'attente-reponse-prefecture' || $last_action == 'envoie-reponse-prefecture'){
			return;
		}
		foreach(array(2,3,4) as $id_type) {
			$libelle = $this->getLibelleType($id_type);
			if($this->getDonneesFormulaire()->get("has_$libelle") == true){
				if ($this->getDonneesFormulaire()->get("has_reponse_$libelle") == false){
					$this->getActionCreator()->addAction($this->id_e,$this->id_u,'attente-reponse-prefecture',"Attente d'une réponse");
					return;	
				}
			}
		}
		
	}

	private function getLibelleType($id_type){
		$txt_message = array(TdTConnecteur::COURRIER_SIMPLE => 'courrier_simple',
							'demande_piece_complementaire',
							'lettre_observation',
							'defere_tribunal_administratif');
		return $txt_message[$id_type];
	}
	
	private function saveAutreDocument($response){
		if ($response['status'] == TdtConnecteur::STATUS_ACTES_MESSAGE_PREF_RECU){
			return $this->saveReponse($response);
		}
		if ($response['status'] == TdtConnecteur::STATUS_ACTES_MESSAGE_PREF_ACQUITTEMENT_RECU) {
			return $this->saveAcquittement($response);
		}
	}
	
	private function saveAcquittement($response){
		$tdT = $this->getConnecteur("TdT");
		
		$type = $this->getLibelleType($response['type']);
		$has_acquittement = $this->getDonneesFormulaire()->get("{$type}_has_acquittement");
		if ($has_acquittement){
			return false;
		}
		$this->getDonneesFormulaire()->setData("{$type}_has_acquittement",true);
		
	}
	
	private function saveReponse($response){
		$tdT = $this->getConnecteur("TdT");
		
		$type = $this->getLibelleType($response['type']);
		$type_id = $this->getDonneesFormulaire()->get("{$type}_id");
		if ($type_id){
			if ($type_id != $response['id']){
				$this->many_same_message[] = $type;
			}
			return false;
		}
		
		$file_content = $tdT->getReponsePrefecture($response['id']);
		$this->getDonneesFormulaire()->setData("has_{$type}",true);
		$this->getDonneesFormulaire()->setData("{$type}_id",$response['id']);
		$this->getDonneesFormulaire()->setData("{$type}_date",date("Y-m-d H:i:m"));
		$this->getDonneesFormulaire()->addFileFromData("{$type}","{$type}.tar.gz", $file_content);
		$message = "Réception d'un message ($type) de la préfecture";
		$this->addActionOK($message);
		$this->notify('verif-reponse-tdt', $this->type, $message);
		return true;
	}
	
	
}