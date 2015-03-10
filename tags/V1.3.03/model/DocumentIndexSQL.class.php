<?php
class DocumentIndexSQL extends SQL {
	
	public function index($id_d,$fieldName,$fieldValue){
		$sql = "SELECT count(*) FROM document_index WHERE id_d=? AND field_name=?";
		if ($this->queryOne($sql,$id_d,$fieldName)){
			$sql = "UPDATE document_index SET field_value = ? WHERE id_d=? AND field_name = ?";
			$this->query($sql,$fieldValue,$id_d,$fieldName);
		} else {
			$sql = "INSERT INTO document_index(id_d,field_name,field_value) VALUES(?,?,?)";
			$this->query($sql,$id_d,$fieldName,$fieldValue);
		}
	}
	
	public function get($id_d,$fieldName){
		$sql = "SELECT field_value FROM document_index WHERE id_d=? AND field_name=?";
		return $this->queryOne($sql,$id_d,$fieldName);
	}
	
}