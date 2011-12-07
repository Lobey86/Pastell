<?php
require_once( PASTELL_PATH . "/externaldata/lib/TypeActes.class.php");
require_once( PASTELL_PATH . "/externaldata/lib/TypeActes.class.php");
require_once( PASTELL_PATH . "/externaldata/lib/ClassificationActes.class.php");

class ChoixTypeActesControler {
	
	private $lastError;
	
	public function __construct(SQLQuery $sqlQuery, DonneesFormulaireFactory $donneesFormulaireFactory){
		$this->sqlQuery = $sqlQuery; 
		$this->donneesFormulaireFactory = $donneesFormulaireFactory;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function getData($id_e){
		$file = $this->get($id_e);

		if (!$file){
			$lastError->setLastError("La nomenclature du CDG n'est pas disponible - Veuillez utiliser la classification Actes");
			header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
			exit;
		}
		
		$typeActes = new TypeActes($file);
		$data = $typeActes->getData($file);
		return $data;
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
	
	public function set($id_e,$id_d,$type,Recuperateur $recuperateur){
		
		$classif = $recuperateur->get('type');
		$file = $this->get($id_e);

		if (!$file){
			$this->lastError = "La nomenclature du CDG n'est pas disponible - Veuillez utiliser la classification Actes";
			return false;
		}


		$typeActes = new TypeActes($file);
		$info = $typeActes->getInfo($classif);

		$info_classification = "";
		if ($info['transmission_actes']){
			$entite = new Entite($this->sqlQuery,$id_e);
			$id_e_col = $entite->getCollectiviteAncetre();
			$donneesFormulaire = $this->donneesFormulaireFactory->get($id_e_col,$type);	
			$file = $donneesFormulaire->getFilePath('classification_file');
		
			if (! file_exists($file)){
				$info_classification = "La classification en matière et sous-matière n'est pas disponible";
			} else {
				$classificationActes= new ClassificationActes($file);
				$info_classification = $classificationActes->getInfo($info['code_actes']);
				if (! $info_classification){
					$info_classification = "Cette classification (".$info['code_actes'].") n'existe pas sur le Tétédis";
				}
			}
			
		}
		$donneesFormulaire = $this->donneesFormulaireFactory->get($id_d,$type);
		$donneesFormulaire->setData('type',$classif." ".$info['nom']);
		$donneesFormulaire->setData('classification',$info_classification);
		$donneesFormulaire->setData('envoi_tdt',$info['transmission_actes']);
		$donneesFormulaire->setData('envoi_tdt_obligatoire',$info['transmission_actes']);
		$donneesFormulaire->setData('envoi_cdg',$info['transmission_cdg']);
		$donneesFormulaire->setData('archivage',$info['archivage']);
		return true;
	}
	

	
	
}