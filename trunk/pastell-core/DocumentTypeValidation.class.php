<?php 

class DocumentTypeValidation {
	
	private $yml_loader;
	private $module_definition;
	private $last_error;
	
	public function __construct(YMLLoader $yml_loader){
		$this->yml_loader = $yml_loader;
		$this->module_definition = $yml_loader->getArray(__DIR__."/module-definition.yml");
	}
	
	public function validate(array $typeDefinition){
		$this->last_error = array();
		return $this->validatePart('definition.yml',$typeDefinition,'');
	}
	
	private function validatePart($part,$typeDefinition,$previous_part){
		if (! $typeDefinition){
			return;
		}
		if ($previous_part) {
			$new_part = "$previous_part:$part";
		} else {
			$new_part = "$part";
		}
		foreach($typeDefinition as $key => $data){
			$key_info = $this->getPossibleKeyInfo($part,$key);
			if(! $key_info){
				$this->last_error[] = "<b>$new_part</b>: la clé <b>$key</b> ($data) n'est pas attendu";
				continue;
			}
			$type_finded = $this->verifType($data,$key_info,$new_part,$key);
			if (! $type_finded){
				continue;
			}
			if ($type_finded=='list' || $type_finded == 'associative_array'){
				$this->validatePart($key_info['key_name'],$data,$new_part);
			}
		}
		return ! $this->last_error;
	}
	
	private function verifType($data,$key_info,$new_part,$key){
		$type_expected = $key_info['type'];
		$type_finded = $this->getDataType($data);
		if ($type_expected == 'choice'){
			$type_expected = 'string';	
			if (! in_array($data,$key_info['choice'])){
				$value = implode(',',$key_info['choice']);
				$this->last_error[] = "<b>$new_part:$key</b>  doit être une des valeurs suivante : $value - $data trouvé";
				return false;
			}
		} 
		if ($type_expected=='associative_array' || $type_expected == 'list'){
			if ($data == ''){
				return $type_expected;
			}
		}
		if ($type_expected == 'string_or_boolean' ){
			if ($type_finded == 'string' || $type_finded == 'boolean'){
				return $type_finded;
			}
		}
		if ($type_finded != $type_expected){
			$this->last_error[] = "<b>$new_part:$key</b> doit être de type <b>$type_expected</b> - $type_finded trouvé";
			return false;
		}
		return $type_finded;
	}
	
	private function getPossibleKeyInfo($part,$key){
		if (empty($this->module_definition[$part])){
			$this->last_error[] = "Erreur dans le fichier module-definiton.yml: la clé <b>$part</b> n'est pas défini";
			return false;
		}
		if (isset($this->module_definition[$part]['possible_key'][$key])){
			$result = $this->module_definition[$part]['possible_key'][$key];
			$result['key_name'] = $key;
			return $result;
		}
		if (substr($key,0,3) == 'or_'){
			if (isset($this->module_definition[$part]['possible_key']['or_X'])){
				$result = $this->module_definition[$part]['possible_key']['or_X'];
				return $result;
			}
		}
		if (substr($key,0,4) == 'and_'){
			if (isset($this->module_definition[$part]['possible_key']['and_X'])){
				$result = $this->module_definition[$part]['possible_key']['and_X'];
				return $result;
			}
		}
		if (isset($this->module_definition[$part]['possible_key']['*'])){
			$result = $this->module_definition[$part]['possible_key']['*'];
			return $result;
		}
		return false;
	}
	
	private function getDataType($data){
		if (is_array($data)){
			 if ((bool)count(array_filter(array_keys($data), 'is_string'))){
				return 'associative_array'; 	
			 }
			 return 'list';
		}
		if (is_string($data)){
			return 'string';
		}
		if (is_bool($data)){
			return 'boolean';
		}
	}
	
	public function getLastError(){
		return $this->last_error;
	}
	
}