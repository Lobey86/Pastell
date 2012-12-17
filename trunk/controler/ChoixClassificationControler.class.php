<?php

class ChoixClassificationControler {
	
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery; 
	}
	
	public function isEnabled($id_e){
		$file = $this->getFileClassificationCDG($id_e);
		if ( ! $file){
			return true;
		}
		$donneesFormulaireCDG = $this->getDonneedFormulaireCDG($id_e);
		$field_name = $this->getClassificationAJourFieldName($donneesFormulaireCDG,$file);
		if (! $field_name){
			return true;
		}
		return ! $donneesFormulaireCDG->get($field_name);				
	}
	
	public function disabledClassificationCDG($id_e){
		$file = $this->getFileClassificationCDG($id_e) ;
		if ( ! $file){
			return;
		}
		$donneesFormulaireCDG = $this->getDonneedFormulaireCDG($id_e);
		$field_name = $this->getClassificationAJourFieldName($donneesFormulaireCDG,$file);
		if (! $field_name){
			return ;
		}
		$donneesFormulaireCDG->setData($field_name,false);		
		echo "La classification du CDG a été marqué comme non a jour\n";
	}
	
	private function getFileClassificationCDG($id_e){
		global $objectInstancier;
		$donneesFormulaire = $objectInstancier->ConnecteurFactory->getConnecteurConfigByType($id_e,'actes','TdT');
		if (! $donneesFormulaire){
			return false;
		}
		return $donneesFormulaire->get('nomemclature_file');
	}
	
	private function getDonneedFormulaireCDG($id_e){
		$entite = new Entite($this->sqlQuery,$id_e);
		$infoCDG = $entite->getCDG();
		global $objectInstancier;
		return $objectInstancier->ConnecteurFactory->getConnecteurConfigByType($infoCDG['id_e'],'actes-cdg','classification-cdg');
	
	}
	
	private function getClassificationAJourFieldName($donneesFormulaireCDG,$file_classification_cdg){			
		$type = $donneesFormulaireCDG->get('classification_cdg');
		foreach($type as $i => $file_cdg){
			if ($file_classification_cdg == $file_cdg){
				return 'classification_a_jour'."_$i";
			}
		}
		return false;
	}
}