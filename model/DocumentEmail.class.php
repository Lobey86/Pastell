<?php
class DocumentEmail extends SQL {
	
	const DESTINATAIRE = 'to';
	
	private $zenMail;
	
	public static function getChaineTypeDestinataire($code){
		$type = array('to' => 'Destinataire', 'cc' => 'Copie à' , 'bcc' => 'Copie caché à' );
		return $type[$code]; 
	}
	
	private $sqlQuery;
	
	public function __construct(SQLQuery $sqlQuery, ZenMail $zenMail){
		parent::__construct($sqlQuery);
		$this->sqlQuery = $sqlQuery;
		$this->zenMail = $zenMail;
	}

	public function add($id_d,$email,$type){
		$key = $this->getKey($id_d,$email);
		if ($key){
			return $key;
		}
		$key = md5($id_d . $email. mt_rand());
		$sql = "INSERT INTO document_email(id_d,email,`key`,date_envoie,type_destinataire) VALUES (?,?,?,now(),?)";
		$this->query($sql,$id_d,$email,$key,$type);	
		return $key;
	}
	
	public function getKey($id_d,$email){
		$sql = "SELECT `key` FROM document_email WHERE id_d=? AND email=?";
		return $this->queryOne($sql,$id_d,$email);	
	}
	
	public function getInfo($id_d){
		$sql = "SELECT * FROM document_email WHERE id_d=?";
		return $this->query($sql,$id_d);
	}
	
	public function getInfoFromKey($key){
		$sql = "SELECT * FROM document_email WHERE `key`=?";
		return $this->queryOne($sql,$key);
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
		$this->query($sql,$key);	
		
		$sql = "SELECT id_e FROM document_entite WHERE id_d=?";
		$id_e = $this->queryOne($sql,$result['id_d']);
		
		$journal->addActionAutomatique(Journal::MAIL_SECURISE,$id_e,$result['id_d'],'Consulté', $result['email'] . " a consulté le document");
		
		$sql = "SELECT count(*) as nb_total,sum(lu) as nb_lu FROM document_email WHERE id_d=?";
		$count = $this->queryOne($sql,$result['id_d']);
		
		if ($count['nb_lu'] == $count['nb_total']){
			$next_action = 'reception';
		} else {
			$next_action = 'reception-partielle';
		}
		
		$documentActionEntite = new DocumentActionEntite($this->sqlQuery);
		$action = $documentActionEntite->getLastAction($id_e,$result['id_d']);
		
		
		$message_action = ($next_action == 'reception')?"Tous les destinataires ont consulté le message":"Un destinataire a consulté le message";
		if ($action != $next_action){
			$actionCreator = new ActionCreator($this->sqlQuery,$journal,$result['id_d']);
			$actionCreator->addAction($id_e,0,$next_action,$message_action);
		}
		
		$document = new Document($this->sqlQuery,new PasswordGenerator());
		$infoDocument = $document->getInfo($result['id_d']);
		
		
		$message = "Le mail sécurisé {$infoDocument['titre']} a été consulté par {$result['email']}";
		if ($next_action == 'reception'){
			$message .= "\n\nTous les destinataires ont consulté le message";	
		}
		$message .= "\n\nConsulter le détail du document : " . SITE_BASE . "document/detail.php?id_d={$result['id_d']}&id_e=$id_e";
	
		$notification = new Notification($this->sqlQuery);
		$notificationMail = new NotificationMail($notification,$this->zenMail,$journal);
		$notificationMail->notify($id_e, $result['id_d'], $next_action, 'mailsec', $message);
		
		return $this->getInfoFromKey($key);
	}
	
}