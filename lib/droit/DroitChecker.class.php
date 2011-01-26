<?php


class DroitChecker {
	
	private $roleUtilisateur;
	private $id_u;
	
	public function __construct($roleUtilisateur,$id_u){
		$this->roleUtilisateur = $roleUtilisateur;
		$this->id_u = $id_u;
	}
	
	public function verifDroitOrRedirect($droit,$id_e){
		if ( ! $this->hasDroit($droit,$id_e)) {
			header("Location: " . SITE_BASE . "index.php");
			exit;
		}
	}
	
	public function hasDroit($droit,$id_e){
		return  $this->roleUtilisateur->hasDroit($this->id_u,$droit,$id_e);
	}
	

}