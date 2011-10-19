<?php
require_once( PASTELL_PATH . "/lib/action/ActionExecutor.class.php");

require_once (PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");


class EnvoieCDG  extends ActionExecutor {

	private $collectivite;
	private $cdgProperties;
	private $id_cdg;

	private function getId_cdg(){
		if ( ! $this->id_cdg){
			$infoEntite = $this->getEntite()->getInfo();
			$this->id_cdg = $this->getEntite()->getCDG();	
		}
		return $this->id_cdg;
	}
	
	public function setCDGProperties(EntiteProperties $cdgProperties) {
		$this->cdgProperties = $cdgProperties;	
	}
	
	public function getCDGProperties(){
		if ( ! $this->cdgProperties){
			$this->setCDGProperties(new EntiteProperties($this->getSQLQuery(),$this->getId_cdg()));
		}
		return $this->cdgProperties;
	}
	
	public function go(){
		
		$id_cdg = $this->getId_cdg();
		if (! $id_cdg){
			$this->setLastMessage("La collectivité n'a pas de centre de gestion");
			return false;
		}
		
		$this->getDocumentEntite()->addRole($this->id_d,$id_cdg,"lecteur");
		
		$actionCreator = $this->getActionCreator();
		
		$actionCreator->addAction($this->id_e,$this->id_u,'send-cdg',"Le document a été envoyé au centre de gestion");
		$actionCreator->addToEntite($id_cdg,"Le document a été envoyé par la collectivité");
		
		$actionCreator->addAction($id_cdg,0,'recu-cdg',"Le document a été reçu par le centre de gestion");
		$actionCreator->addToEntite($this->id_e,"Le document a été reçu par le centre de gestion");
		
		
		$infoDocument = $this->getDocument()->getInfo($this->id_d);

		$documentType = $this->getDocumentTypeFactory()->getDocumentType($infoDocument['type']);
				
		$theAction = $documentType->getAction();
		
		
		
		$message =  "La transaction ".$this->id_d." est passé dans l'état :  " . $theAction->getActionName('send-cdg');
		$message .= "\n\n";
		
		$notificationMail = $this->getNotificationMail();
		
		$notificationMail->notify($id_cdg,$this->id_d,'recu-cdg', 'actes',$message);		
		
		$entiteProperties = new EntiteProperties($this->getSQLQuery(),$id_cdg);
		
		$has_ged = $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_ged');
		if ($has_ged == 'auto'){	
			$actionCreator->addAction($id_cdg,0,'send-ged',"Le document a été déposé dans la GED");
		}
		
		$has_archivage = $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_archivage');
		if ($has_archivage == 'auto'){	
			$actionCreator->addAction($id_cdg,0,'send-archive',"Le document a été archivé");
		}
		
		
		$this->setLastMessage("Le document a été envoyé à votre centre de gestion");
		return true;
	}
}