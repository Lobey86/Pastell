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
		foreach($typeDefinition as $key => $data){
			if(empty($this->module_definition['definition.yml'][$key])){
				$this->last_error[] = "Dans le fichier definition.yml: la clé <b>$key</b> n'est pas connu par le système";
			}
		}
		return ! $this->last_error;
	}
	
	public function getLastError(){
		return $this->last_error;
	}
	
}