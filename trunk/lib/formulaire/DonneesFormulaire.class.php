<?php

require_once (PASTELL_PATH . "/ext/spyc.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/FileUploader.class.php");
require_once( PASTELL_PATH . "/lib/helper/mail_validator.php");
class DonneesFormulaire {
	
	private $formulaire;
	private $filePath;
	private $info;
	private $isModified;
	private $lastError;

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
		
		$this->formulaire->addDonnesFormulaire($this);
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
					if ($type == 'date'){
						$value = preg_replace("#^(\d{2})/(\d{2})/(\d{4})$#",'$3-$2-$1',$value);
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
		
		unlink($this->getFilePath($fieldName,$num));
		for($i = $num + 1; $i < count($this->info[$fieldName]) ; $i++){
			rename($this->getFilePath($fieldName,$i),$this->getFilePath($fieldName,$i - 1));
		}
		
		array_splice($this->info[$fieldName],$num,1);
		$dump = Spyc::YAMLDump($this->info);
		file_put_contents($this->filePath,$dump);
	}
	
	public function getFilePath($field_name,$num = 0){
		return  $this->filePath."_".$field_name."_$num";
	}
	
	public function get($item,$default=false){
		if (empty($this->info[$item])){
			
			return $default;
		}
		return $this->info[$item];
	}
	
	public function geth($item,$default = false){

		return nl2br(htmlentities($this->get($item,$default),ENT_QUOTES));
	}
	
	public function isValidable(){
		$this->formulaire->addDonnesFormulaire($this);
		foreach($this->formulaire->getAllFields() as $field){
			if ($field->isRequired() && ! $this->get($field->getName())){
				$this->lastError = "Le formulaire est incomplet : le champ «" . $field->getLibelle() . "» est obligatoire.";
				return false;
			}
			if ($field->getType() == 'mail-list' && $this->get($field->getName())){
				if ( ! is_mail_list($this->get($field->getName()))){
					$this->lastError = "Le formulaire est incomplet : le champ «" . $field->getLibelle() . "» ne contient pas une liste d'email valide.";
					return false;
				}
			}
		}
		return true;
	}
	
	public function getLastError(){
		return $this->lastError;
	}
	
	public function delete(){
		$file_to_delete = glob($this->filePath."*");
		foreach($file_to_delete as $file){
			unlink($file);
		}
	}
	
}