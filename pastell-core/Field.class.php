<?php

class Field {
	
	private $libelle;
	private $properties;
	
	public static function Canonicalize($field_name){	
		$name = strtolower($field_name);
		$name = strtr($name," àáâãäçèéêëìíîïñòóôõöùúûüýÿ","_aaaaaceeeeiiiinooooouuuuyy");
		$name = preg_replace('/[^\w_]/',"",$name);
		return $name;
	}
	
	public function __construct($libelle,$properties){
		$this->libelle = $libelle;
		$this->properties = $properties;
	}
	
	public function getLibelle(){
		if (isset($this->properties['name'])){
			return $this->properties['name'];
		}
		return $this->libelle;
	}
	
	public function getName(){
		return self::Canonicalize($this->libelle);
	}
	
	public function isRequired(){
		return  (! empty($this->properties['requis']));
	}
	
	public function getType(){
		if (!empty($this->properties['type'])){
			return $this->properties['type'];
		}
		return "text";
	}
	
	public function isMultiple(){
		return  (! empty($this->properties['multiple']));
	}
	
	public function getSelect(){
		return $this->properties['value'];
	}
	
	public function getDefault(){
		if ($this->getType() == 'date' && ! $this->getProperties('default') ){
			return date("Y-m-d");
		}
		return $this->getProperties('default');
	}
	
	public function isTitle(){
		return (! empty($this->properties['title']));
	}
	
	public function getOnChange(){
		return $this->getProperties('onchange');
	}
	
	public function pregMatch(){
		return $this->getProperties('preg_match');
	}
	
	public function pregMatchError(){
		return $this->getProperties('preg_match_error');
	}
	
	public function getProperties($properties){
		if ( ! isset($this->properties[$properties])){
			return false;
		}
		return $this->properties[$properties];
	}
	
	public function getAllProperties(){
		$result = $this->properties;
		if (empty($result['name'])){
			$result['name'] = $this->getLibelle(); 
		}
		return $result;
	}
	
	public function isEnabled($id_e,$id_d){
		
		$action_name = $this->getProperties('choice-action');
		if ( ! $action_name){
			return true;
		}
		
		global $objectInstancier;
		$id_u = $objectInstancier->Authentification->getId();
		try { 
			return $objectInstancier->ActionExecutorFactory->isChoiceEnabled($id_e,$id_u,$id_d,$action_name);
		} catch (Exception $e){
			return false;
		}
	}
	
	public function isShowForRole($role){
		if ($this->getProperties('no-show')){
			return false;
		}
	
		$show_role = $this->getProperties('show-role') ;
	
		if (! $show_role){
			return true;
		}
	
		foreach($show_role as $role_unit){
			if ($role == $role_unit){
				return true;
			}
		}
		return false;
	}
	
}