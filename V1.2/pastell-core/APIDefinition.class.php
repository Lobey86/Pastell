<?php
class APIDefinition {
	
	const KEY_PARAMETERS = 'parameters';
	const KEY_FUNCTION = 'function';
	const KEY_DESCRIPTION = 'description';
	const KEY_INPUT = 'input';
	const KEY_DEFAULT = 'default';
	const KEY_REQUIRED = 'required';
	const KEY_COMMENT = 'comment';
	const KEY_OUTPUT = 'output';
	const KEY_IS_VARIABLE = 'is_variable';
	const KEY_IS_MULTIPLE = 'is_multiple';
	const KEY_CONTENT = 'content';
	const KEY_SOAP = 'soap';
	const KEY_SOAP_NAME = 'soap-name';
	
	private $api_definition_file_path; 
	private $ymlLoader;
	
	public function __construct($api_definition_file_path, YMLLoader $ymlLoader){
		$this->api_definition_file_path = $api_definition_file_path;
		$this->ymlLoader = $ymlLoader;
	}
	
	private function setDefaultValue(array & $array,$key_or_keys_array,$default){
		if (is_array($key_or_keys_array)){
			foreach($key_or_keys_array as $key){
				$this->setDefaultValue($array,$key,$default);
			}
		} elseif (! isset($array[$key_or_keys_array])){
			$array[$key_or_keys_array] = $default;
		}
	}
	
	public function getFunctions(){
		$api_definition = $this->ymlLoader->getArray($this->api_definition_file_path);
		$functions =  $api_definition[self::KEY_FUNCTION];
		foreach($functions as $name => $fonction){
			$functions[$name][self::KEY_SOAP_NAME] = $this->camelize($name);
			$this->setDefaultValue($functions[$name],array(self::KEY_INPUT,self::KEY_OUTPUT),array());
			$this->setDefaultValue($functions[$name],self::KEY_COMMENT,"");
			$this->setDefaultValue($functions[$name],self::KEY_SOAP,false);
			
			
			foreach($functions[$name][self::KEY_INPUT] as $param_name => $param_properties){
				if (empty($param_properties)){
					if (isset($api_definition[self::KEY_PARAMETERS][$param_name])){
						$functions[$name][self::KEY_INPUT][$param_name] = $api_definition[self::KEY_PARAMETERS][$param_name];
					}	
				}
				$this->setDefaultValue(	$functions[$name][self::KEY_INPUT][$param_name],self::KEY_DEFAULT,"");
			}
			
			foreach($functions[$name][self::KEY_OUTPUT] as $param_name => $param_properties){
				$this->setDefaultValue(	$functions[$name][self::KEY_OUTPUT][$param_name],array(self::KEY_IS_VARIABLE,self::KEY_IS_MULTIPLE),false);
				$this->setDefaultValue(	$functions[$name][self::KEY_OUTPUT][$param_name],self::KEY_CONTENT,array());
				foreach($functions[$name][self::KEY_OUTPUT][$param_name][self::KEY_CONTENT] as $content_name => $content_properties){
					$this->setDefaultValue($functions[$name][self::KEY_OUTPUT][$param_name][self::KEY_CONTENT][$content_name],array(self::KEY_IS_VARIABLE,self::KEY_IS_MULTIPLE),false);
					$this->setDefaultValue($functions[$name][self::KEY_OUTPUT][$param_name][self::KEY_CONTENT][$content_name],self::KEY_COMMENT,"");
				}
			}
		}
		return $functions;
	}
	
	public function getSoapFunctions(){
		$result = array();
		foreach($this->getFunctions() as $name => $properties){
			if ($properties[self::KEY_SOAP] ){
				$result[$name] = $properties;
			}
		}
		return $result;
		
	}
	
	private function camelize($name){
		$allName = explode("-",$name);
		$allName = array_map('ucfirst',$allName);
		$allName[0] = lcfirst($allName[0]);
		return implode("",$allName);
	} 
	
	
}