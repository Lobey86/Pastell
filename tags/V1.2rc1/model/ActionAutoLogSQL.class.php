<?php
class ActionAutoLogSQL extends SQL {

	const LIMIT_AFFICHE = 50;
	
	public function add($id_e,$id_d,$etat_precedent,$etat_cible,$message){
		$sql = "SELECT last_action FROM document_entite WHERE id_e=? AND id_d=?";
		$last_etat = $this->queryOne($sql,$id_e,$id_d);
		if ($last_etat == $etat_precedent){
			$sql = "INSERT INTO action_auto_log (id_e,id_d,etat_source,etat_cible,date,message) VALUES (?,?,?,?,now(),?)";
			$this->query($sql,$id_e,$id_d,$etat_precedent,$etat_cible,$message);			
		} else {
			$sql = "DELETE FROM action_auto_log WHERE id_e=? AND id_d=?";
			$this->query($sql,$id_e,$id_d);
		}
	}
	
	public function getLog($offset = 0){
		$offset = intval($offset);
		$sql = "SELECT entite.denomination,action_auto_log.id_d,action_auto_log.id_e,document.titre, min(date) as first_try,max(date) as last_try,count(*) as nb_try, " .
				" etat_source,etat_cible" .
				" FROM action_auto_log " .
				" JOIN document ON action_auto_log.id_d=document.id_d " .
				" JOIN entite ON action_auto_log.id_e=entite.id_e " .
				" GROUP BY action_auto_log.id_e,action_auto_log.id_d" .
				" ORDER BY first_try ".
				" LIMIT $offset,".self::LIMIT_AFFICHE;
		return $this->query($sql);
	}
	
	public function countLog(){
		$sql = "SELECT count(*) FROM (SELECT 1 FROM action_auto_log GROUP BY id_e,id_d) as t1";
		return $this->queryOne($sql);
	}
	
	public function getMessage($id_e,$id_d){
		$sql = "SELECT * FROM action_auto_log WHERE id_e=? AND id_d=? ORDER BY date DESC";
		return $this->query($sql,$id_e,$id_d);
	}
	
} 