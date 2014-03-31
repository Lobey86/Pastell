<?php
require_once (PASTELL_PATH . "/ext/spyc.php");

class DonneesFormulaire {
	
	private $formulaire;
	private $filePath;
	private $info;
	private $isModified;
	private $lastError;
	private $onChangeAction;

	private $editable_content;
	private $has_editable_content;
	
	public function __construct($filePath, Formulaire $formulaire){
		$this->filePath = $filePath;
		$this->formulaire = $formulaire;
		$this->retrieveInfo();
		$this->onChangeAction = array();
	}
	
	public function setEditableContent(array $editable_content){
		$this->has_editable_content = true;
		$this->editable_content = $editable_content;
	}
		
	public function isReadOnly($field_name){
		$field = $this->formulaire->getField($field_name);
		
		if ($field->getProperties('no-show')){
			return true;
		}
		
		$read_only_content = $field->getProperties('read-only-content') ;
		if (!$read_only_content){
			return false;
		}	
		foreach($read_only_content as $key => $value){
			if ($this->get($key) != $value){
				return false;
			}
		}
		return true;
	}
	
	public function isEditable($field_name){
		
		if ($this->isReadOnly($field_name)){
			return false;
		}
		
		if ( ! $this->has_editable_content){
			return true;
		}
		return in_array($field_name,$this->editable_content);
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
			if (! $this->isEditable($field->getName())){
					continue;
			}
			$type = $field->getType();
			
			if ($type == 'externalData'){
				continue;
			}
			if ( $type == 'file'){
				$this->saveFile($field,$fileUploader);
			} elseif($field->getProperties('depend') && is_array($this->get($field->getProperties('depend')))) {
				foreach($this->get($field->getProperties('depend')) as $i => $file){
					if (empty($this->info[$field->getName()."_$i"])){
						$this->info[$field->getName()."_$i"] = false;
					}
					if ($this->info[$field->getName()."_$i"] != $recuperateur->get($field->getName()."_$i")){
						$this->info[$field->getName()."_$i"] = $recuperateur->get($field->getName()."_$i");
						$this->isModified = true;
					}
				}				
			} else {
				$name = $field->getName();
				$value =  $recuperateur->get($name);
				if ($type == 'password'){
					$value =  $recuperateur->getNoTrim($name,"");
				}
				if (! isset($this->info[$name])){
						$this->info[$name] = "";
					}
				
				if ( ( $this->info[$name] != $value) &&  $field->getOnChange()  ){
					if (! in_array($field->getOnChange(),$this->onChangeAction)){
						$this->onChangeAction[] = $field->getOnChange();
					}
				}

				if ( ( ($type != 'password' ) || $field->getProperties('may_be_null')  ) ||  $value){
					$this->setInfo($field,$value);					
				} 
			}
		}
		$this->saveDataFile(false);
	}
	
	private function setInfo(Field $field, $value){
		if ($this->info[$field->getName()] == $value){
			return;
		}
		if ($field->getType() == 'date'){
			$value = preg_replace("#^(\d{2})/(\d{2})/(\d{4})$#",'$3-$2-$1',$value);
		}

		$this->setInfo2($field->getName(),$value);
		$this->isModified = true;
	}
	
	public function saveAllFile(FileUploader $fileUploader){
		$allField = $this->formulaire->getAllFields();
		foreach($fileUploader->getAll() as $filename => $name){
			if (isset($allField[$filename])){
				$this->saveFile($allField[$filename],$fileUploader);
			}
		}
		if ($this->isModified) {
			$this->saveDataFile();                
		}
	}
	
	public function saveAll(Recuperateur $recuperateur,FileUploader $fileUploader){
		$modif = array();
		$allField = $this->formulaire->getAllFields();
		foreach($recuperateur->getAll() as $key => $value){
			
			if (isset($allField[$key])){
				$field = $this->formulaire->getField($key);
				$this->setInfo($allField[$key],$value);		
				$modif[] = $key;
			}
		}
		foreach($fileUploader->getAll() as $filename => $name){
			if (isset($allField[$filename])){
				$this->saveFile($allField[$filename],$fileUploader);
				$modif[] = $filename;
			}
		}
		if ($this->isModified){
			$this->saveDataFile();
		}
		return $modif;
	}
	
	
	public function isModified(){
		return $this->isModified;
	}
	
	public function getOnChangeAction(){
		return $this->onChangeAction;
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
			if ($field->getOnChange()){
				$this->onChangeAction[] = $field->getOnChange();
			}
		}
	}
	
	public function setData($field_name,$field_value){		
		$this->setInfo2($field_name,$field_value);
		$this->saveDataFile();		
	}
	
	public function setTabData(array $field){
		foreach($field as $name => $value){
			$this->setInfo2($name,$value);
		}
		$this->saveDataFile();
	}
	
	private function setInfo2($field_name,$field_value){
		
		$this->info[$field_name] = $field_value;
	}
		
	public function get($item,$default=false){

		$item  = Field::Canonicalize($item);
		if (empty($this->info[$item])){
			return $default;
		}
		$field = $this->formulaire->getField($item);
		if ($field && $field->getType() == 'password'){
			return htmlspecialchars_decode($this->info[$item],ENT_COMPAT);				
		}
		
		return $this->info[$item];
	}
	
	public function setTabDataVerif(array $input_field){
		$allField = $this->formulaire->getAllFields();
		foreach($input_field as $field_name => $value){
			if (isset($allField[$field_name])){
				$this->setInfo2($field_name,$value);
				$this->isModified = true;
				if ($allField[$field_name]->getOnChange()){
					$this->onChangeAction[] = $allField[$field_name]->getOnChange();
				}
			}
		}
	
		foreach($allField as $field_name=>$field){
			if($field->getProperties('depend') &&
			is_array($this->get($field->getProperties('depend')))) {
				foreach($this->get($field->getProperties('depend')) as $i => $file){
					if (isset($input_field[$field_name."_$i"])){
						$this->setInfo2($field_name."_$i",$input_field[$field_name."_$i"]);
						$this->isModified = true;
					}
				}
			}
		}
		$this->saveDataFile();
	}
	
	
	public function addFileFromData($field_name,$file_name,$raw_data,$file_num = 0){
		$this->info[$field_name][$file_num] = $file_name;
		file_put_contents($this->getFilePath($field_name,$file_num),$raw_data);
		$this->saveDataFile();
	}
	
	public function removeFile($fieldName,$num = 0){		
		unlink($this->getFilePath($fieldName,$num));
		for($i = $num + 1; $i < count($this->info[$fieldName]) ; $i++){
			rename($this->getFilePath($fieldName,$i),$this->getFilePath($fieldName,$i - 1));
		}
		
		array_splice($this->info[$fieldName],$num,1);
		$this->saveDataFile();
	}
	
	
	public function getFilePath($field_name,$num = 0){
		return  $this->filePath."_".$field_name."_$num";
	}
	
	public function getFileContent($field_name,$num=0){
		$file_path = $this->getFilePath($field_name,$num);
		if (! is_readable($file_path)){
			$this->lastError = "Le fichier $file_path ne peut pas être lu";
			return false;
		}
		return file_get_contents($file_path);
	}
	
	//http://stackoverflow.com/questions/6595183/docx-file-type-in-php-finfo-file-is-application-zip
	private function getOpenXMLMimeType($file_name){
		$ext = pathinfo($file_name,PATHINFO_EXTENSION);
		$openXMLExtension = array(
				'xlsx' => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
				'xltx' => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
				'potx' =>  "application/vnd.openxmlformats-officedocument.presentationml.template",
				'ppsx' =>  "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
				'pptx'   =>  "application/vnd.openxmlformats-officedocument.presentationml.presentation",
				'sldx'   =>  "application/vnd.openxmlformats-officedocument.presentationml.slide",
				'docx'   =>  "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				'dotx'   =>  "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
				'xlam'   =>  "application/vnd.ms-excel.addin.macroEnabled.12",
				'xlsb'   =>  "application/vnd.ms-excel.sheet.binary.macroEnabled.12");
		if (isset($openXMLExtension[$ext])){
			return $openXMLExtension[$ext];
		}
		return false;
	}
	
	public function getContentType($field_name,$num  = 0){
		$file_path = $this->getFilePath($field_name,$num);
		if (! file_exists($file_path)){
			return;
		}
		$fileInfo = new finfo();
		$result = $fileInfo->file($file_path,FILEINFO_MIME_TYPE);
		
		if ($result == 'application/zip'){
			$file_name = $this->getFileName($field_name,$num);
			$result = $this->getOpenXMLMimeType($file_name)?:'application/zip';
		}
		
		return $result;
	}
	
	public function getFileName($field_name,$num = 0){
		$all_file_name = $this->get($field_name);
		return 	$all_file_name[$num];
	}
	
	public function getWithDefault($item){
		$default = $this->formulaire->getField($item)->getDefault();
		return $this->get($item,$default);
	}
	
	public function geth($item,$default = false){
		return nl2br(htmlentities($this->get($item,$default),ENT_QUOTES,"ISO-8859-15"));
	}

	public function isValidable(){
		$this->formulaire->addDonnesFormulaire($this);
		foreach($this->formulaire->getAllFields() as $field){
			if ($field->isRequired() && ! $this->get($field->getName())){
				$this->lastError = "Le formulaire est incomplet : le champ «" . $field->getLibelle() . "» est obligatoire.";
				return false;
			}
			if ($field->getType() == 'mail-list' && $this->get($field->getName())){
				if ( ! $this->is_mail_list($this->get($field->getName()))){
					$this->lastError = "Le formulaire est incomplet : le champ «" . $field->getLibelle() . " ne contient pas une liste d'email valide.";
					return false;
				}
			}
			if ($field->pregMatch()){			
				if ( ! preg_match($field->pregMatch(),$this->get($field->getName()))){
					$this->lastError = "Le champ «{$field->getLibelle()}» est incorrect ({$field->pregMatchError()}) ";
					return false;
				}
			}
			if ($field->getProperties('is_equal')){
				if ($this->get($field->getProperties('is_equal')) != $this->get($field->getName())){
					$this->lastError =$field->getProperties('is_equal_error');
					return false;
				}
			}
			if ($field->getProperties('content-type')){
				$ctype = $this->getContentType($field->getName(),0);
				if ($ctype && $ctype != $field->getProperties('content-type')){
					$this->lastError = "Le fichier «{$field->getLibelle()}» n'est pas un fichier {$field->getProperties('content-type')} ($ctype trouvé)";
					return false;
				}
			
			}
		}
		return true;
	}

	private function is_mail($mail){
		if (preg_match('/^([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})$/i',$mail)){
			return true;
		}
		
		if (preg_match('/^[^@<]*<([^@\s]+)@((?:[-a-z0-9]+\.)+[a-z]{2,})>$/i',$mail)){
			return true;
		}
		
		if (preg_match('/^groupe: ".*"$/',$mail)){
			return true;
		}
		
		if (preg_match('/^role: ".*"$/',$mail)){
			return true;
		}
		
		if (preg_match('/^groupe hérité de .*: ".*"$/',$mail)){
			return true;
		}
	
		if (preg_match('/^rôle hérité de .*: ".*"$/',$mail)){
			return true;
		}
	
		if (preg_match('/^groupe global: ".*"$/',$mail)){
			return true;
		}
		
		if (preg_match('/^rôle global: ".*"$/',$mail)){
			return true;
		}
		
		return false;
	}
	
	
	private function is_mail_list($scalar_mail_list){
		
		foreach($this->get_mail_list($scalar_mail_list) as $mail){
			if (! $mail){
				continue;
			}
			if ( ! $this->is_mail(trim($mail))){
				return false;
			}
		}
		return true;
	}
	
	public function getMailList($type){
		return $this->get_mail_list($this->get($type));
	}
	
	private function  get_mail_list($scalar_mail_list){
		$mails = array(0=>'');
		$i = 0;
		$state = 1;
		foreach(str_split($scalar_mail_list) as $letter){
			if ($letter == '"'){
				$state = 1 - $state;
			}
			if ($letter == ',' && $state){
				$mails[++$i] = '';
			} else {
				$mails[$i].=$letter;
			}
		}
		$result = array();
		foreach($mails as $mail){
			$mail = trim($mail);
			if ($mail) {
				$result[] = $mail;
			} 
			
		}
		return array_unique($result);
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
	
	public function getRawData(){
		return $this->info;
	}
	
	public function getMetaData(){
		return file_get_contents($this->filePath);
	}
	
	public function getAllFile(){
		$result = array();
		foreach($this->formulaire->getAllFields() as $field){
			if ($field->getType() != 'file'){
				continue;
			}
			if (! $this->get($field->getName())){
				continue;
			}
			$result[] = $field->getName();
		}
		return $result;
	}
	
	private function saveDataFile($setModifiedToFalse = true){
		foreach($this->info as $field_name => $field_value){
			$field = $this->formulaire->getField($field_name);
			if ($field && $field->getType() == 'password'){
				$field_value = htmlspecialchars($field_value,ENT_COMPAT);				
			}
			$result[$field_name] = $field_value; 
		}
		$dump = Spyc::YAMLDump($result);
		file_put_contents($this->filePath,$dump);
        
        // Le dossier est enregistré : il faut réinitialiser la variable isModified=false
        if ($setModifiedToFalse) {
        	$this->isModified=false;  
        }              
		
	}
	
	public function sendFile($field_name,$num=0){
		$file_path = $this->getFilePath($field_name,$num);
		$file_name_array = $this->get($field_name);
		if (empty($file_name_array[$num])){
			$this->lastError = "Ce fichier n'existe pas";
			return false;
		}
		$file_name= $file_name_array[$num];

		if (! file_exists($file_path)){
			$this->lastError = "Ce fichier n'existe pas";
			return false;
		}

		header("Content-type: ".mime_content_type($file_path));
		header("Content-disposition: attachment; filename=\"$file_name\"");
		header("Expires: 0");
		header("Cache-Control: must-revalidate, post-check=0,pre-check=0");
		header("Pragma: public");
		
		readfile($file_path);
		return true;
	}
	
	public function copyFile($field_name,$folder_destination,$num = 0){
		$file_name = $this->get($field_name);
		$file_name = $file_name[$num];
		$file_path = $this->getFilePath($field_name,$num);
		if (! file_exists($file_path)){
			return false;
		}
		copy($file_path,utf8_encode("$folder_destination/$file_name"));
		return $folder_destination."/".$file_name;
	}
	
	public function copyAllFiles($field_name,$folder_destination){
		$result = array();
		if (!$this->get($field_name) ){
			return $result;
		}
		foreach($this->get($field_name) as $i => $file_name){
			$result[] = $this->copyFile($field_name, $folder_destination,$i);
		}
		return $result;
	}
	
}