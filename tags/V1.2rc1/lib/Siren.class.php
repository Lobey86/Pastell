<?php
class Siren {
	
	//http://xml.insee.fr/schema/siret.html#controles
	public function isValid($siren){
		if (strlen($siren) != 9){
			return false;
		}
		
		$sum = $this->getKey($siren);
		return ($sum % 10 == 0);
	}
	
	private function getKey($siren){
		$sum = 0;
		$siren_array = str_split($siren);
		foreach($siren_array as $i => $chiffre){
			if ($i%2 == 1){
				$chiffre2 = str_split($chiffre*2);
				foreach ($chiffre2 as $c){
					$sum += $c;
				}
			} else {
				$sum += $chiffre;
			}
		}	
		return $sum;
	}
	
	public function generate(){
		$siren = "";
		for($i=0; $i<8; $i++){
			$siren = $siren . rand(0,9);
		}		
		$key = $this->getKey($siren);
		$siren = $siren . (( 10 - $key%10 )%10);
		return $siren;
	}
	
}