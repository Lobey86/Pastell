<?php

require_once( PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php" );
require_once( PASTELL_PATH . "/lib/action/ActionCreator.class.php" );

//document_email(id_d,email,key,lu)

class DocumentEmail {
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}

	public function add($id_d,$email){
		$key = $this->getKey($id_d,$email);
		if ($key){
			return $key;
		}
		$key = md5($id_d . $email. mt_rand());
		$sql = "INSERT INTO document_email(id_d,email,`key`,date_envoie) VALUES (?,?,?,now())";
		$this->sqlQuery->query($sql,$id_d,$email,$key);	
		return $key;
	}
	
	public function getKey($id_d,$email){
		$sql = "SELECT `key` FROM document_email WHERE id_d=? AND email=?";
		return $this->sqlQuery->fetchOneValue($sql,$id_d,$email);	
	}
	
	public function getInfo($id_d){
		$sql = "SELECT * FROM document_email WHERE id_d=?";
		return $this->sqlQuery->fetchAll($sql,$id_d);
	}
	
	public function getInfoFromKey($key){
		$sql = "SELECT * FROM document_email WHERE `key`=?";
		return $this->sqlQuery->fetchOneLine($sql,$key);
	}
	
	public function consulter($key, Journal $journal){
		$result = $this->getInfoFromKey($key);
		if (! $result){
			return false;
		}
		if ($result['lu']){
			return $result;
		}
		$sql = "UPDATE document_email SET lu=1,date_lecture=now() WHERE `key` = ?";
		$this->sqlQuery->query($sql,$key);	
		
		$sql = "SELECT id_e FROM document_entite WHERE id_d=?";
		$id_e = $this->sqlQuery->fetchOneValue($sql,$result['id_d']);
		
		$journal->addActionAutomatique(Journal::MAIL_SECURISE,$id_e,$result['id_d'],'lecture', $result['email'] . " a consulté le document");
		
		$sql = "SELECT count(*) as nb_total,sum(lu) as nb_lu FROM document_email WHERE id_d=?";
		$count = $this->sqlQuery->fetchOneLine($sql,$result['id_d']);
		
		if ($count['nb_lu'] == $count['nb_total']){
			$next_action = 'reception';
		} else {
			$next_action = 'reception-partielle';
		}
		
		
		$documentActionEntite = new DocumentActionEntite($this->sqlQuery);
		$action = $documentActionEntite->getLastAction($id_e,$result['id_d']);
		
		if ($action != $next_action){
			$actionCreator = new ActionCreator($this->sqlQuery,$journal,$result['id_d']);
			$actionCreator->addAction($id_e,0,$next_action,($next_action == 'reception')?"Tous les destinataires ont consulté le message":"Un destinataire a consulter le message");
		}
		
		return $this->getInfoFromKey($key);
	}
	
}