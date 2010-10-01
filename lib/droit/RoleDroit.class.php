<?php

class RoleDroit {
	
	const AUCUN_DROIT = 'aucun droit';
	
	private static $standard_role;
	
	public function __construct(){
		self::$standard_role = array(
					self::AUCUN_DROIT => array(),
					'admin' => array(
									'entite:edition',
									'entite:lecture',
									'journal:lecture',
									'rh-actes:edition',
									'rh-actes:lecture',
					
					),
					'lecteur' => array(
									'entite:lecture',
								),

			);
	}
	
	public function hasDroit($role,$droit){
		return  isset(self::$standard_role[$role]) && in_array($droit,self::$standard_role[$role]);
	}

	public function getAllRole(){
		return self::$standard_role;
	}
	
}