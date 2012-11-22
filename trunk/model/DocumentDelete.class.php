<?php


class DocumentDelete {
	
	private $sqlQuery;
	
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function delete($id_d){
		$sql = "DELETE FROM document WHERE id_d=?";
		$this->sqlQuery->query($sql,$id_d);
		
		$sql = "SELECT id_a FROM document_action WHERE id_d=?";
		$id_a = $this->sqlQuery->fetchOneValue($sql,$id_d);
		
		$sql = "DELETE FROM document_action_entite WHERE id_a=?";	
		$this->sqlQuery->query($sql,$id_a);
		
		$sql = "DELETE FROM document_action WHERE id_d=?";
		$this->sqlQuery->query($sql,$id_d);
		$sql = "DELETE FROM document_entite WHERE id_d=?";
		$this->sqlQuery->query($sql,$id_d);
	}
	
	
}