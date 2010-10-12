<?php


class DocumentActionEntite {
	
	private $sqlQuery;
	private $id_a;
	
	public function __construct($sqlQuery){
		$this->sqlQuery =$sqlQuery;
	}
	
	public function addAction($id_a,$id_e,$journal,$message_journal = ""){
		
		$info = $this->getInfo($id_a);
		
		$sql = "INSERT INTO document_action_entite (id_a,id_e) VALUES (?,?)";
		$this->sqlQuery->query($sql,$id_a,$id_e);
		
		$journal->add(Journal::DOCUMENT_ACTION,$id_e,$info['id_d'],$info['action'],$message_journal);
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

	//TODO : ??
	public function getInfo($id_a){
		$sql = "SELECT * FROM document_action " . 
				" JOIN document ON document_action.id_d = document.id_d " . 
				" WHERE id_a=?";
		return $this->sqlQuery->fetchOneLine($sql,$id_a);
	}
	
	
	public function getNbDocument($id_e,$type){
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " ;

		return $this->sqlQuery->fetchOneValue($sql,$id_e,$type);
		
	}
	
	public function getListDocument($id_e,$type,$offset,$limit){
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " . 
				" ORDER BY document_entite.last_action_date DESC LIMIT $offset,$limit";	
			
		$list = $this->sqlQuery->fetchAll($sql,$id_e,$type);
		return $this->addEntiteToList($id_e,$list);
	
	}
	
	
	public function  getListDocumentByEntite($id_e,array $type_list,$offset,$limit){
		
		$type_list = "'" . implode("','",$type_list) . "'";
		
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date  FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type IN ($type_list) " . 
				" ORDER BY document_entite.last_action_date DESC LIMIT $offset,$limit";	
		$list = $this->sqlQuery->fetchAll($sql,$id_e);
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
	
	public function getNbDocumentByEntite($id_e,array $type_list){
		
		$type_list = "'" . implode("','",$type_list) . "'";
			
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type IN ($type_list) " ;

		return $this->sqlQuery->fetchOneValue($sql,$id_e);
	}
	
}