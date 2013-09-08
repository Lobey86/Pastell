<?php
require_once( __DIR__."/../lib/TypeActes.class.php");
require_once( __DIR__ ."/../../actes-generique/lib/ClassificationActes.class.php");

class Nomemclature extends ChoiceActionExecutor {

	public function isEnabled(){
		return $this->getNomemclatureContent()?1:0;
	}
	
	public function go(){
		$recuperateur = new Recuperateur($_GET);
		
		$classif = $recuperateur->get('type');
		$file = $this->getNomemclatureContent();

		if (!$file){
			throw new Exception("La nomenclature du CDG n'est pas disponible - Veuillez utiliser la classification Actes");
		}
		
		$typeActes = new TypeActes($file);
		$info = $typeActes->getInfo($classif);

		$info_classification = "";
		if ($info['transmission_actes']){
			$id_e_col = $this->objectInstancier->EntiteSQL->getCollectiviteAncetre($this->id_e);
			$donneesFormulaire = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($this->id_e,'actes','TdT');	
				
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
		$donneesFormulaire = $this->getDonneesFormulaire();
		$donneesFormulaire->setData('type',$classif." ".$info['nom']);
		$donneesFormulaire->setData('classification',$info_classification);
		$donneesFormulaire->setData('envoi_tdt',$info['transmission_actes']);
		$donneesFormulaire->setData('envoi_tdt_obligatoire',$info['transmission_actes']);
		$donneesFormulaire->setData('envoi_cdg',$info['transmission_cdg']);
		$donneesFormulaire->setData('archivage',$info['archivage']);
		$donneesFormulaire->setData('envoi_iparapheur',$info['signature']);
	}
	
	public function displayAPI(){
		$nomemclature = $this->getNomemclatureContent();
		$typeActes = new TypeActes($nomemclature);
		$data = $typeActes->getData($nomemclature);
		return $data;
		
	}
	
	public function display(){
		$nomemclature = $this->getNomemclatureContent();
		$this->typeActes = new TypeActes($nomemclature);
		$this->renderPage("Choix du type d'Actes","NomemclatureList");
	}
	
	private function getNomemclatureContent(){
		$infoCDG = $this->objectInstancier->EntiteSQL->getCDG($this->id_e);
		if (! $infoCDG){
			return false;
		}
				
		$file = $this->getFile($this->id_e);
		
		
		$donneesFormulaireCDG = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($infoCDG['id_e'],'actes-cdg','classification-cdg');
		if (!$donneesFormulaireCDG){
			throw new Exception("Le CDG ne présente pas de connecteur actes-cdg");
		}		
		$classifCDG = $donneesFormulaireCDG->get("classification_cdg");
		
		if (! $classifCDG){
			return false;
		}
		$file_name = false;
		foreach($classifCDG as $i => $nom_file){
			if($nom_file == $file){
				$file_name =  $donneesFormulaireCDG->getFilePath('classification_cdg',$i);
			}
		}
		
		if (! $file_name || ! file_exists($file_name)){
			return false;
		}
		return $file_name;
	}
	
	
	
	
	private function getFile($id_e){
		$donneesFormulaire = $this->objectInstancier->ConnecteurFactory->getConnecteurConfigByType($id_e,'actes','TdT');
		
		if (! $donneesFormulaire){
			throw new Exception("Aucun fichier de nomemclature disponible");
		}
		
		$file = $donneesFormulaire->get('nomemclature_file');
		
		if ($file){
			return $file;
		}
		
		$allAncetre = $this->objectInstancier->EntiteSQL->getAncetre($id_e);
		array_pop($allAncetre);
		$allAncetre = array_reverse($allAncetre);

		
		foreach($allAncetre as $ancetre){
			$donneesFormulaire = $objectInstancier->ConnecteurFactory->getConnecteurConfigByType($ancetre['id_e'],'actes','TdT');
			
			$file = $donneesFormulaireAncetre->get('nomemclature_file');
			if ($file){
				return $file;
			}
		}
	}
	
	
}
