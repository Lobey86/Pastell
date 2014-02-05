<?php 

require_once(__DIR__."/../../actes-generique/action/Classification.class.php");

class ClassificationCDG extends Classification {
	
	public function isEnabled(){
		
		$infoCDG = $this->objectInstancier->EntiteSQL->getCDG($this->id_e);
		if (! $infoCDG){
			return true;
		}
		
		$file = $this->getFile($this->id_e);
		if (!$file){
			return true;
		}
		
		
		$donneesFormulaireCDG = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($infoCDG['id_e'],'actes-cdg','classification-cdg');
		
		if (!$donneesFormulaireCDG){
			return true;
		}
		
		$classifCDG = $donneesFormulaireCDG->get("classification_cdg");
		
		if (! $classifCDG){
			return true;
		}
		$file_name = false;
		foreach($classifCDG as $i => $nom_file){
			if($nom_file == $file){
				$file_name =  $donneesFormulaireCDG->getFilePath('classification_cdg',$i);
				$num_file = $i;
			}
		}
		
		if (! $file_name || ! file_exists($file_name)){
			return true;
		}
		
		if ($donneesFormulaireCDG->get("classification_a_jour_$num_file")){
			return false;
		} else {
			return true;
		}
		
	}
	
	
	
	private function getFile($id_e){
		$donneesFormulaire = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($id_e,$this->type,'TdT');
		
		if (! $donneesFormulaire){
			return false;
		}
		
		$file = $donneesFormulaire->get('nomemclature_file');
		
		if ($file){
			return $file;
		}
		
		$allAncetre = $this->objectInstancier->EntiteSQL->getAncetre($id_e);
		array_pop($allAncetre);
		$allAncetre = array_reverse($allAncetre);

		
		foreach($allAncetre as $ancetre){
			$donneesFormulaire = $objectInstancier->ConnecteurFactory->getConnecteurConfigByType($ancetre['id_e'],$this->type,'TdT');
			
			$file = $donneesFormulaireAncetre->get('nomemclature_file');
			if ($file){
				return $file;
			}
		}
		return false;
	}
	
}
