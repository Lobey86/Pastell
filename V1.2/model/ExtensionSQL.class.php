<?php 

class ExtensionSQL extends SQL {
	
	public function getAll(){
		$sql = "SELECT * FROM extension ORDER BY nom";
		return $this->query($sql);
	}
	
	public function getInfo($id_e){
		$sql = "SELECT * FROM extension WHERE id_e=?";
		return $this->queryOne($sql,$id_e);
	}
	
	public function edit($id_e,$path){
		if($id_e){
			$sql = "UPDATE extension SET path=? WHERE id_e=?";
			$this->query($sql,$path,$id_e);
		} else {
			$sql = "INSERT INTO extension(path) VALUES (?)";
			$this->query($sql,$path);	
		}
	}
	
	public function delete($id_e){
		$sql = "DELETE FROM extension WHERE id_e=?";
		$this->query($sql,$id_e);
	}
}