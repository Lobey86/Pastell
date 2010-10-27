<?php

require_once (PASTELL_PATH . "/ext/spyc.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/FileUploader.class.php");

class DonneesFormulaire {
	
	private $formulaire;
	private $filePath;
	private $info;
	private $isModified;

	public function __construct($filePath, Formulaire $formulaire){
		$this->filePath = $filePath;
		$this->formulaire = $formulaire;
		$this->retrieveInfo();
	}
	
	public function injectData($field,$data){
		$this->info[$field] = $data;
	}
	
	private function retrieveInfo(){
		if ( ! file_exists($this->filePath)){
			return ;
		}
		$this->info = Spyc::YAMLLoad($this->filePath);		
	}
	
	public function saveTab(Recuperateur $recuperateur, FileUploader $fileUploader,$pageNumber){	
		$this->isModified = false;
		
		$this->formulaire->setTabNumber($pageNumber);
		
		foreach ($this->formulaire->getFields() as $field){
			$type = $field->getType();
			if ($type == 'externalData'){
				continue;
			}
			if ( $type == 'file'){
				$this->saveFile($field,$fileUploader);
			} else {
				$name = $field->getName();
				$value =  $recuperateur->get($name);
				if ( ($type != 'password' ) ||  $value){
					if (! isset($this->info[$name])){
						$this->info[$name] = "";
					}
					if ($this->info[$name] != $value){
						$this->isModified = true;
					}
				
					$this->info[$name] = $value;
				}
			}
		}
		$dump = Spyc::YAMLDump($this->info);
		file_put_contents($this->filePath,$dump);
	}
	
	public function isModified(){
		return $this->isModified;
	}
	
	
	private function saveFile(Field $field, FileUploader $fileUploader){
		$fname = $field->getName();
		
		
		
		if ($fileUploader->getName($fname)){
			
			if ($field->isMultiple()){
				$this->info[$fname][] =  $fileUploader->getName($fname);
			} else {
				$this->info[$fname][0] =  $fileUploader->getName($fname);
			}
			
			$num = count($this->info[$fname]) - 1 ;
			$fileUploader->save($fname , $this->getFilePath($fname,$num));
			$this->isModified = true;
		}
	}
	
	public function setData($field_name,$field_value){
		$this->info[$field_name] = $field_value;
		$dump = Spyc::YAMLDump($this->info);
		file_put_contents($this->filePath,$dump);
	}
	
	
	public function addFileFromData($field_name,$file_name,$raw_data){
		$this->info[$field_name][0] = $file_name;
		file_put_contents($this->getFilePath($field_name,0),$raw_data);
		$dump = Spyc::YAMLDump($this->info);
		file_put_contents($this->filePath,$dump);
	}
	
	public function removeFile($fieldName,$num = 0){
		//TODO déplacement des fichiers
		array_splice($this->info[$fieldName],$num,1);
		$dump = Spyc::YAMLDump($this->info);
		file_put_contents($this->filePath,$dump);
	}
	
	public function getFilePath($field_name,$num = 0){
		return  $this->filePath."_".$field_name."_$num";
	}
	
	public function get($item){
		if (empty($this->info[$item])){
			return false;
		}
		return $this->info[$item];
	}
	
	public function geth($item){
		return nl2br(htmlentities($this->get($item),ENT_QUOTES));
	}
	
	public function isValidable(){
		
		foreach($this->formulaire->getAllFields() as $field){
			if ($field->isRequired() && ! $this->get($field->getName())){
				return false;
			}
		}
		return true;
	}
	
}