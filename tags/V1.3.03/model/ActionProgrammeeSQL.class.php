<?php
class ActionProgrammeeSQL extends SQL {
	
	public function add($id_d,$id_e,$id_u,$action){
		$sql = "INSERT INTO action_programmee(id_d,id_e,id_u,action) VALUES (?,?,?,?)";
		$this->query($sql,$id_d,$id_e,$id_u,$action);
	}
	
	public function hasActionProgrammee($id_d,$id_e){
		$sql = "SELECT count(*) FROM action_programmee WHERE id_d=? AND id_e=?";
		return $this->queryOne($sql,$id_d,$id_e);
	}
	
	public function getAll(){
		$sql = "SELECT * FROM action_programmee";
		return $this->query($sql);
	}
	
	public function delete($id_d,$id_e){
		$sql = "DELETE FROM action_programmee WHERE id_d=? AND id_e=?";
		$this->query($sql,$id_d,$id_e);
	}

}