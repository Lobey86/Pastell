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
	
	public function isTitle(){
		return (! empty($this->properties['title']));
	}
	
	public function getProperties($properties){
		if ( ! isset($this->properties[$properties])){
			return false;
		}
		return $this->properties[$properties];
	}
	
}