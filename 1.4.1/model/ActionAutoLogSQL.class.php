<?php
class ActionAutoLogSQL extends SQL {

	const LIMIT_AFFICHE = 50;
	
	public function add($id_e,$id_d,$etat_precedent,$etat_cible,$message){
		$sql = "SELECT last_action FROM document_entite WHERE id_e=? AND id_d=?";
		$last_etat = $this->queryOne($sql,$id_e,$id_d);
		if ($last_etat == $etat_precedent){
			$sql = "SELECT count(*) FROM action_auto_log WHERE id_e=? AND id_d=?";
			if ($this->queryOne($sql,$id_e,$id_d)){
				$sql = "UPDATE action_auto_log SET last_try=now(), last_message=?, nb_try = nb_try + 1 WHERE id_e=? AND id_d=?";
				$this->query($sql,$message,$id_e,$id_d);
			} else {			
				$sql = "INSERT INTO action_auto_log (id_e,id_d,etat_source,etat_cible,first_try,last_try,last_message,nb_try) VALUES (?,?,?,?,now(),now(),?,1)";
				$this->query($sql,$id_e,$id_d,$etat_precedent,$etat_cible,$message);
			}			
		} else {
			$sql = "DELETE FROM action_auto_log WHERE id_e=? AND id_d=?";
			$this->query($sql,$id_e,$id_d);
		}
	}
	
	public function getLog($offset = 0){
		$offset = intval($offset);
		$sql = "SELECT entite.denomination,action_auto_log.id_d,action_auto_log.id_e,document.titre, action_auto_log.first_try,action_auto_log.last_try,action_auto_log.nb_try,action_auto_log.last_message, " .
				" etat_source,etat_cible" .
				" FROM action_auto_log " .
				" JOIN document ON action_auto_log.id_d=document.id_d " .
				" JOIN entite ON action_auto_log.id_e=entite.id_e " .
				" ORDER BY first_try ".
				" LIMIT $offset,".self::LIMIT_AFFICHE;
		return $this->query($sql);
	}
	
	public function countLog(){
		$sql = "SELECT count(*) FROM action_auto_log";
		return $this->queryOne($sql);
	}
	
	
} 