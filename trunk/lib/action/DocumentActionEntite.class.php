<?php

class DocumentActionEntite {
	
	private $sqlQuery;
	private $id_a;
	
	public function __construct($sqlQuery){
		$this->sqlQuery =$sqlQuery;
	}
	
	public function getLastAction($id_e,$id_d){
		$sql = "SELECT action FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " . 
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND id_d=? ORDER BY date DESC,document_action.id_a DESC LIMIT 1 ";
		return $this->sqlQuery->fetchOneValue($sql,$id_e,$id_d);
	}
	
	public function getAction($id_e,$id_d){
		$sql = "SELECT * FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " . 
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND id_d=? ORDER BY date,document_action_entite.id_a ";
		return $this->sqlQuery->fetchAll($sql,$id_e,$id_d);
	}
	
	public function getNbDocument($id_e,$type,$search,$etat = false){
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? AND document.titre LIKE ?" ;
		$data = array($id_e,$type,"%$search%");
		if ($etat){
			$sql .= " AND document.last_action=?";
			$data[] = $etat; 
		}

		return $this->sqlQuery->fetchOneValue($sql,$data);
	}
	
	public function getListDocument($id_e,$type,$offset,$limit,$search,$etat = false){
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " . 
				" AND document.titre LIKE ?" ;
		$data = array($id_e,$type,"%$search%");
		
		if ($etat){
			$sql .= " AND document.last_action=?";
			$data[] = $etat; 
		}	
	
		$sql .= " ORDER BY document_entite.last_action_date DESC LIMIT $offset,$limit";	
			
		$list = $this->sqlQuery->fetchAll($sql,$data);
		return $this->addEntiteToList($id_e,$list);
	}
	
	public function  getListDocumentByEntite($id_e,array $type_list,$offset,$limit,$search){
		
		$type_list = "'" . implode("','",$type_list) . "'";
		
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date  FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type IN ($type_list) " . 
				" AND document.titre LIKE ?" .
				" ORDER BY document_entite.last_action_date DESC LIMIT $offset,$limit";	
		$list = $this->sqlQuery->fetchAll($sql,$id_e,"%$search%");
		return $this->addEntiteToList($id_e,$list);
	}
	
	private function addEntiteToList($id_e,$list){
		
		foreach($list as $i => $doc){
			$id_d = $doc['id_d'];
			$sql = "SELECT DISTINCT document_action.id_e,entite.denomination " .
					" FROM document_action ".
					" JOIN document_action_entite ON document_action.id_a = document_action_entite.id_a " . 
					" JOIN entite ON entite.id_e=document_action.id_e ". 
					" WHERE id_d= ? AND document_action_entite.id_e=? AND entite.id_e != ?";
			$list[$i]['entite'] = $this->sqlQuery->fetchAll($sql,$id_d,$id_e,$id_e);
		}
		return $list;
	}
	
	public function getNbDocumentByEntite($id_e,array $type_list,$search){
		
		$type_list = "'" . implode("','",$type_list) . "'";
			
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.titre LIKE ? AND document.type IN ($type_list) " ;

		return $this->sqlQuery->fetchOneValue($sql,$id_e,"%$search%");
	}
	
	public function getUserFromAction($id_e,$id_d,$action){
		$sql = "SELECT * FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " . 
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND id_d=? AND document_action.action= ? LIMIT 1";
		return $this->sqlQuery->fetchOneLine($sql,$id_e,$id_d,$action);
	}
	
	
	public function getNbDocumentBySearch($id_e,$type,$search){
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? AND document.titre LIKE ?" ;

		return $this->sqlQuery->fetchOneValue($sql,$id_e,$type,"%$search%");
	}
	
	public function getListBySearch($id_e,$type,$offset,$limit,$search){
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " . 
				" AND document.titre LIKE ?" .
				" ORDER BY document_entite.last_action_date DESC LIMIT $offset,$limit";	
			
		$list = $this->sqlQuery->fetchAll($sql,$id_e,$type,"%$search%");
		return $this->addEntiteToList($id_e,$list);
	
	}	
	
}