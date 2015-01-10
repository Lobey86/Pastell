<?php
class FieldData {
	
	private $field;
	private $value;
	
	private $lastError;
	
	public function __construct(Field $field, $value){
		$this->field = $field;
		$this->setValue($value);
	}
	
	public function setValue($value){
		$field = $this->field;
		if ($field->getProperties('depend') && is_array($value)){
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
			if (isset($select[$value])){
				$value =  $select[$value];
			}
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
	
	public function getLastError(){
		return $this->lastError;
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
	
	public function getValueNum($num=0){
		$valueList = $this->getValue();
		if ($valueList){
			return $valueList[0];
		}
		return false;
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
	
	public function isValide(){
		if ($this->field->isRequired() && ! $this->value){
			$this->lastError = "Le formulaire est incomplet : le champ «" . $this->field->getLibelle() . "» est obligatoire.";		
			return false;
		}
		if ($this->field->getType() == 'mail-list' && $this->value && ! $this->isMailList()){
			$this->lastError = "Le formulaire est incomplet : le champ «" . $this->field->getLibelle() . " ne contient pas une liste d'email valide.";
			return false;
		}
		if ($this->field->pregMatch() &&  ! preg_match($this->field->pregMatch(),$this->value)){
			$this->lastError = "Le champ «{$this->field->getLibelle()}» est incorrect ({$this->field->pregMatchError()}) ";
			return false;
		}
		return true;
	}
	
	public function getMailList(){
		return $this->get_mail_list($this->value);
	}
	
	public function isMailList(){
		foreach($this->get_mail_list($this->value) as $mail){
			if (! $mail){
				continue;
			}
			if ( ! $this->isMail(trim($mail))){
				return false;
			}
		}
		return true;
	}
	
	public function isMail($mail){
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
	
	
	
	
	
}