<?php
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once (PASTELL_PATH . "/ext/spyc.php");
require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

class DonneesFormulaire {
	
	const TYPE_RESSOURCE_FORMULAIRE = 'formulaire';
	const TYPE_RESSOURCE_FORMULAIRE_ATTACHEMENT = 'fomulaire_attachment';
	
	private $formulaire;
	private $filePath;
	private $info;

	public function __construct($filePath){
		$this->filePath = $filePath;
		$this->retrieveInfo();
	}
	
	public function setFormulaire(Formulaire $formulaire){
		$this->formulaire = $formulaire;
	}
	
	public function getFormulaire(){
		return $this->formulaire;
	}
	
	private function retrieveInfo(){
		if ( ! file_exists($this->filePath)){
			return ;
		}
		$this->info = Spyc::YAMLLoad($this->filePath);		
	}
	
	public function save(Recuperateur $recuperateur, FileUploader $fileUploader){	
		foreach ($this->formulaire->getFields() as $field){
			if ($field->getType() == 'file'){
				$this->saveFile($field,$fileUploader);
			} else {
				$this->info[$field->getName()] = $recuperateur->get($field->getName());
			}
		}
		$dump = Spyc::YAMLDump($this->info);
		file_put_contents($this->filePath,$dump);
	}
	
	private function saveFile(Field $field, FileUploader $fileUploader){
		$fname = $field->getName();
		
		if ($fileUploader->getName($fname)){
			$this->info[$fname] = $fileUploader->getName($fname);
			$fileUploader->save($fname, $this->getFilePath($fname));
		}
	}
	
	public function getFilePath($field_name){
		return  $this->filePath."_".$field_name;
	}
	
	public function get($item){
		if (empty($this->info[$item])){
			return false;
		}
		return $this->info[$item];
	}
	
	public function geth($item){
		return htmlentities($this->get($item),ENT_QUOTES);
	}
	
	public function isValidable(){
		foreach($this->formulaire->getAllFields() as $field){
			if ($field->isRequired() && ! $this->get($field->getName())){
				return false;
			}
		}
		return true;
	}
	
	public function getAllRessource(){
		$result = array();
		$result[] = array("url"=> $this->filePath,"type" => self::TYPE_RESSOURCE_FORMULAIRE);
		foreach($this->formulaire->getAllFields() as $field){
			if ($field->getType()== 'file' && $this->get($field->getName())){
				$result[] = array("url" => $this->getFilePath($field->getName()), "type" => self::TYPE_RESSOURCE_FORMULAIRE_ATTACHEMENT);
			}
		}
		return $result;
	}
}