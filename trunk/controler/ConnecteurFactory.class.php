<?php
class ConnecteurFactory {
	
	private $objectInstancier;
	
	public function __construct(ObjectInstancier $objectInstancier){
		$this->objectInstancier = $objectInstancier;
	}
	
	public function getConnecteurById($id_ce){
		$connecteur_info = $this->objectInstancier->ConnecteurEntiteSQL->getInfo($id_ce);
		return $this->getConnecteurObjet($connecteur_info);
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
		$class_name = $this->objectInstancier->ConnecteurDefinitionFiles->getConnecteurClass($connecteur_info['id_connecteur']);
		$connecteurObject = $this->objectInstancier->newInstance($class_name);
		$connecteurObject->setConnecteurConfig($this->getConnecteurConfig($connecteur_info['id_ce']));		
		return $connecteurObject;
	}
	
	public function getConnecteurConfig($id_ce){
		return $this->objectInstancier->DonneesFormulaireFactory->getConnecteurEntiteFormulaire($id_ce);
	}
		
	
}