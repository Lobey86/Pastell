<?php
class DisplayField {
	
	private $field;
	private $value;
	
	public function __construct(Field $field, $value){
		$this->field = $field;
		
		if ($field->getProperties('depend')){
			$new_value = array();
			foreach($value as $filename => $value){
				if ($field->getType() == 'checkbox') {
					$new_value[] = "$filename : ".($value?"OUI":"NON");
				} elseif($field->getType() == 'select') {
					$select = $field->getSelect();
					$new_value[] = "$filename : ".$select[$value];
				}
			}
			$value = $new_value;
			
		} else if($field->getType() == 'select'){
			$select = $this->field->getSelect();
			$value =  $select[$value];
		} else if($field->getType() == 'checkbox'){
			$value = $value?"OUI":"NON";
		}
		if($field->getType() == 'password'){
			if ( $field->getProperties('may_be_null') && ! $value ){
				$value="(aucun)";
			} else {
				$value = "*********";
			}
		}		
		if ($field->getType() == 'date') {
			$value = date_iso_to_fr($value);
		}
				
		if ($field->getType() == 'link'){
			$value = $field->getProperties('link_name');
		}
		if ($value == ''){
			$value = $field->getDefault();
		}

		$this->value = $value;
	}
	
	public function getField(){
		return $this->field;
	}
	
	public function getValue(){
		if (! $this->value){
			return array();
		}
		if (! is_array($this->value)) {
			return array($this->value);
		}
		return $this->value;
	}
	
	public function isURL(){
		return (in_array($this->field->getType(),array('file','url','link')));
	}
	
	public function getURL($recuperation_fichier_url,$num,$id_e){
		if( $this->field->getType() == 'file') { 
			return "$recuperation_fichier_url&field=".$this->field->getName()."&num=$num";
		} else if($this->field->getType() == 'url') {
			return $this->value;
		} elseif($this->field->getType() == 'link'){
			return SITE_BASE . $this->field->getProperties('script')."?id_e=$id_e" ;
		}
	}
	
}