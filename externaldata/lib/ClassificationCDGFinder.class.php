<?php

class ClassificationCDGFinder {
	
	public function __construct(SQLQuery $sqlQuery, DonneesFormulaireFactory $donneesFormulaireFactory){
		$this->sqlQuery = $sqlQuery; 
		$this->donneesFormulaireFactory = $donneesFormulaireFactory;
	}
	
	public function get($id_e){
		
		$entite = new Entite($this->sqlQuery,$id_e);
		
		$file = $this->getFile($id_e,$entite);
		$infoCDG = $entite->getCDG();
		$donneesFormulaireCDG = $this->donneesFormulaireFactory->get($infoCDG,'collectivite-properties');
		$classifCDG = $donneesFormulaireCDG->get("classification_cdg");
		
		if (! $classifCDG){
			return false;
		}
		foreach($classifCDG as $i => $nom_file){
			if($nom_file == $file){
				return $donneesFormulaireCDG->getFilePath('classification_cdg',$i);
			}
		}
	
		if (! file_exists($file)){
			return false;
		}
	}
	
	private function getFile($id_e,Entite $entite){
	
		$donneesFormulaire = $this->donneesFormulaireFactory->get($id_e,"collectivite-properties");
		$file = $donneesFormulaire->get('nomemclature_file');
		
		if ($file){
			return $file;
		}
		
		$allAncetre = $entite->getAncetre();
		array_pop($allAncetre);
		$allAncetre = array_reverse($allAncetre);

		
		foreach($allAncetre as $ancetre){
			$donneesFormulaireAncetre = $this->donneesFormulaireFactory->get($ancetre['id_e'],"collectivite-properties");
			$file = $donneesFormulaireAncetre->get('nomemclature_file');
			if ($file){
				return $file;
			}
		}

		
	}
	
}