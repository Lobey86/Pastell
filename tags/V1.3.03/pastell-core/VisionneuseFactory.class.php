<?php
class VisionneuseFactory {
	
	const VISIONNEUSE_FOLDERNAME = 'visionneuse';
	
	private $extensions;
	private $objectInstancier;
	
	public function __construct(Extensions $extensions, ObjectInstancier $objectInstancier){
		$this->extensions = $extensions;
		$this->objectInstancier = $objectInstancier;
	}
	
	
	public function display($id_d,$field,$num=0){
		$document_info = $this->objectInstancier->Document->getInfo($id_d);
		$type = $document_info['type'];
		
		$donneesFormulaire = $this->getDonnesFormulaire($id_d);
		
		$visionneuse_class_name = $donneesFormulaire->getFormulaire()->getField($field)->getVisionneuse();
		if (! $visionneuse_class_name){
			throw new Exception("Le champs ne dispose pas d'une visionneuse");
		}
		
		$filename = $donneesFormulaire->getFileName($field,$num);
		$filepath = $donneesFormulaire->getFilePath($field,$num);
		
		$visionneuse_class_path  = $this->getVisionnneuseClassPath($type, $visionneuse_class_name);
		require_once($visionneuse_class_path);
		$visionneuse = $this->objectInstancier->newInstance($visionneuse_class_name);
		
		$visionneuse->display($filename,$filepath);
	}
	
	/**
	 *
	 * @param string $id_d
	 * @return DonneesFormulaire
	 */
	private function getDonnesFormulaire($id_d){
		return $this->objectInstancier->DonneesFormulaireFactory->get($id_d);
	}
	
	private function getVisionnneuseClassPath($flux,$class_name){
	
		$module_path = $this->extensions->getModulePath($flux);
		$action_class_file = "$module_path/".self::VISIONNEUSE_FOLDERNAME."/$class_name.class.php";
	
		if (file_exists($action_class_file)){
			return $action_class_file;
		}
		
		//Note : pour le moment, il n'y a pas de visionneuse défini au niveau global de Pastell
		$action_class_file = PASTELL_PATH."/".self::VISIONNEUSE_FOLDERNAME."/$class_name.class.php";
		if (file_exists($action_class_file )){
			return $action_class_file;
		}
		
		foreach ($this->extensions->getAllModule() as $module_id => $module_path){
			$action_path = "$module_path/".self::VISIONNEUSE_FOLDERNAME."/$class_name.class.php";
			if (file_exists($action_path)){
				return $action_path;
			}
		}
		return false;
	}
	
}