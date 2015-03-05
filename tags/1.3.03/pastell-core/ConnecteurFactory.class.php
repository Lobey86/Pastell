<?php
class ConnecteurFactory {
	
	private $objectInstancier;
	
	public function __construct(ObjectInstancier $objectInstancier){
		$this->objectInstancier = $objectInstancier;
	}
	
	/**
	 * 
	 * @param int $id_ce
	 * @return Connecteur
	 */
	public function getConnecteurById($id_ce){
		$connecteur_info = $this->objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
		return $this->getConnecteurObjet($connecteur_info);
	}
	
	public function getConnecteurConfig($id_ce){
		return $this->objectInstancier->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
	}
	
	
	public function getConnecteurByType($id_e,$id_flux,$type_connecteur){
		$connecteur_info = $this->objectInstancier->FluxEntiteSQL->getConnecteur($id_e,$id_flux,$type_connecteur);
		return $this->getConnecteurObjet($connecteur_info);		
	}
	
	public function getConnecteurConfigByType($id_e,$id_flux,$type_connecteur){
		$connecteur_info = $this->objectInstancier->FluxEntiteSQL->getConnecteur($id_e,$id_flux,$type_connecteur);
		if (! $connecteur_info){
			return false;	
		}
		return $this->getConnecteurConfig($connecteur_info['id_ce']);		
	}
	
	private function getConnecteurObjet($connecteur_info){
		if (!$connecteur_info){
			return false;
		}
		$class_name = $this->objectInstancier->ConnecteurDefinitionFiles->getConnecteurClass($connecteur_info['id_connecteur']);
		$connecteurObject = $this->objectInstancier->newInstance($class_name);
		$connecteurObject->setConnecteurConfig($this->getConnecteurConfig($connecteur_info['id_ce']));
		$connecteurObject->setConnecteurInfo($connecteur_info);
		return $connecteurObject;
	}
		
	public function getGlobalConnecteur($type){
		return $this->getConnecteurByType(0,'global',$type);
	}
	
	public function getGlobalConnecteurConfig($type){
		return $this->getConnecteurConfigByType(0,'global',$type);
	}
	
	
	
}