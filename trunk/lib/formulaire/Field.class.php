<?php

class Field {
	
	private $libelle;
	private $properties;
	
	public function __construct($libelle,$properties){
		$this->libelle = $libelle;
		$this->properties = $properties;
	}
	
	public function getLibelle(){
		return $this->libelle;
	}
	
	public function getName(){
		$name = trim($this->libelle);
		$name = strtolower($name);
		$name = strtr($name," àáâãäçèéêëìíîïñòóôõöùúûüýÿ","_aaaaaceeeeiiiinooooouuuuyy");
		$name = preg_replace('/[^\w_]/',"",$name);
		return $name;
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
	
}