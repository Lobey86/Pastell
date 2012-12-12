<?php
class TypeMessage extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_POST);
		
		$webGFC = $this->objectInstancier->ConnecteurFactory->getConnecteurByType(12,'citoyen-courrier','GFC');
		$message_type = $recuperateur->get('messagetype');
		$message_sous_type = $recuperateur->get('messagesoustype');
		
		if (! $message_sous_type){
			header("Location: external-data.php?id_d={$this->id_d}&id_e={$this->id_e}&page={$this->page}&field={$this->field}&messagetype=$message_type");
			exit;
		}
		
		$message_type = $webGFC->getInfo($message_type);
		$message_sous_type =  $webGFC->getInfo($message_sous_type);
		
		$info = $message_type[1].":".$message_sous_type[1];
		
		$donneesFormulaire = $this->getDonneesFormulaire();
		$donneesFormulaire->setData('messagetype',utf8_decode($info));
		$donneesFormulaire->setData('messageSousTypeId',$message_sous_type[0]);
		
		
	}
	
	public function displayAPI(){
		
	}
	
	public function display(){
		$recuperateur = new Recuperateur($_GET);
		
		$collectivite_id = 2;

		$webGFC = $this->objectInstancier->ConnecteurFactory->getConnecteurByType(12,'citoyen-courrier','GFC');
		
		$message_type = $webGFC->getInfo($recuperateur->get('messagetype'));
		
		$infoEntite = $this->objectInstancier->EntiteSQL->getInfo($this->id_e);
		
		$this->infoTypes = $webGFC->getTypes($collectivite_id);
		if ($message_type){
			$this->infoSousTypes = $webGFC->getSousTypes($collectivite_id,$message_type[0]);
		}
		$this->message_type = $message_type;
		$this->webGFC = $webGFC;
		$this->renderPage("Choix d'un type de message", "TypeMessage");
	}
	
	
}