<?php

//ROLE : 
// 
// utilisateur : peut créer des transactions
// admin : peut créer des utilisateurs, des transactions, ... sur son entité et toute les entités descendante 
// superadmin : idem admin sur toute les collectivités

class UtilisateurEntite {
	
	const ROLE_SUPER_ADMIN = 'super_admin' ;
	const ROLE_ADMIN = 'admin';
	
	private $sqlQuery;
	private $id_u;
	private $info;
	
	public function __construct(SQLQuery $sqlQuery, $id_u){
		$this->sqlQuery = $sqlQuery;
		$this->id_u = $id_u;
	}
	
	public function getSiren(){
		$info = $this->getInfo();
		return $info['siren'];
	}
	
	public function getRole(){
		$info = $this->getInfo();
		return $info['role'];
	}
	
	public function getInfo(){
		if ( ! $this->info ){
			$sql = "SELECT * FROM utilisateur_role WHERE id_u = ?";
			$this->info = $this->sqlQuery->fetchOneLine($sql,array($this->id_u));
		}
		return $this->info;
	}
}