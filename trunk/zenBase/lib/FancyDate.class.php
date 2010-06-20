<?php
class FancyDate {

	const HEURE = 'heure';
	const JOUR = 'jour';
	const MOIS = 'mois';
	const ANNEE = 'annee';	
	
	private $regroupement;
	private $always_show_time;
	private $today;
	
	public function __construct(){
		//TODO i18n
		setlocale (LC_TIME, "fr_FR.UTF-8");
		$this->today = time();		
	}	
	
	public function alwaysShowTime($bool){
		$this->always_show_time = $bool;	
	}
	
	public function setRegroupement($regroupement){
		$this->regroupement = $regroupement;	
	}
	
	public function setToday($unix_timestamp){
		$this->today = $unix_timestamp;
	}	
	
	public function getFancy($unix_timestamp){	
		if ($this->regroupement && $this->regroupement != self::HEURE){
			$strftime = "";
			switch($this->regroupement){				
				case self::JOUR : $strftime = "%e "; 
				case self::MOIS : $strftime .= "%b ";
				case self::ANNEE : $strftime .= "%Y";
			}			
		} elseif ($this->isToday($unix_timestamp)){
			$strftime = "%H:%M";
		} else {		
			$strftime = "%e %b";			
			if ($this->isThisYear($unix_timestamp)){			
				$strftime .= " %Y";
			}
			if ($this->always_show_time){
				$strftime .= " - %H:%M";
			}
		}
		return strftime($strftime,$unix_timestamp);
	}
	
	public function isThisYear($unix_timestamp){
		return date("Y",$this->today) != date("Y",$unix_timestamp);
	}
	
	public function isToday($unix_timestamp){
		return date("d/m/y",$unix_timestamp) == date("d/m/y",$this->today);
	}
}
