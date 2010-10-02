<?php


class DocumentActionEntite {
	
	private $sqlQuery;
	private $id_a;
	
	public function __construct($sqlQuery){
		$this->sqlQuery =$sqlQuery;
	}
	
	public function addAction($id_a,$id_e,$journal,$notificationMail){
		
		$info = $this->getInfo($id_a);
		
		$sql = "INSERT INTO document_action_entite (id_a,id_e) VALUES (?,?)";
		$this->sqlQuery->query($sql,$id_a,$id_e);
		
		$journal->add(Journal::DOCUMENT_ACTION,$id_e,$info['id_d'],$info['action'],"");
		
		$notificationMail->notify($id_e,$info['id_d'],$info['action'],$info['type']);
		
	}
	
	
	public function getAction($id_e,$id_d){
		$sql = "SELECT * FROM document_action_entite " .
				" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
				" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " . 
				" JOIN entite ON document_action.id_e  = entite.id_e ".
				" WHERE document_action_entite.id_e = ? AND id_d=? ORDER BY date ";
		return $this->sqlQuery->fetchAll($sql,$id_e,$id_d);
	}

	
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
		$sql = "SELECT * FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " . 
				" ORDER BY document.modification DESC LIMIT $offset,$limit";	
		$result =  $this->sqlQuery->fetchAll($sql,$id_e,$type);
		foreach($result as $i => $document){
			$lesAction = $this->getAction($id_e,$document['id_d']);
			$result[$i]['action'] = array();
			foreach($lesAction as $action){
				$result[$i]['action'][$action['action']] = $action['date'];
			}
		}
		return $result;
	}
	
	
	public function  getListDocumentByEntite($id_e,array $type_list,$offset,$limit){
		
		$type_list = "'" . implode("','",$type_list) . "'";
		
		$sql = "SELECT * FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type IN ($type_list) " . 
				" ORDER BY document.modification DESC LIMIT $offset,$limit";	
		$result =  $this->sqlQuery->fetchAll($sql,$id_e);
		foreach($result as $i => $document){
			$lesAction = $this->getAction($id_e,$document['id_d']);
			$result[$i]['action'] = array();
			foreach($lesAction as $action){
				$result[$i]['action'][$action['action']] = $action['date'];
			}
		}
		return $result;
	}
	
	public function getNbDocumentByEntite($id_e,array $type_list){
		
		$type_list = "'" . implode("','",$type_list) . "'";
			
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type IN ($type_list) " ;

		return $this->sqlQuery->fetchOneValue($sql,$id_e);
	}
	
}