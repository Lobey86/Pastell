<?php
require_once( PASTELL_PATH . "/lib/base/SQLQuery.class.php");
require_once( PASTELL_PATH . "/lib/base/Date.class.php");
require_once( PASTELL_PATH ."/lib/timestamp/SignServer.class.php");

class Journal {
	
	const CREATE_SQL = "CREATE TABLE journal (
  id_j int(11) NOT NULL AUTO_INCREMENT,
  `type` int(11) NOT NULL,
  id_e int(11) NOT NULL,
  id_u int(11) NOT NULL,
  id_d varchar(16) NOT NULL,
  `action` varchar(16) NOT NULL,
  message varchar(128) NOT NULL,
  `date` datetime NOT NULL,
  preuve varchar(1024) NOT NULL,
  PRIMARY KEY (id_j)
)";
	
	const DOCUMENT_ACTION = 1;
	const NOTIFICATION = 2;
	const MODIFICATION_ENTITE = 3;
	const MODIFICATION_UTILISATEUR = 4;
	const MAIL_SECURISE = 5;
	
	private $sqlQuery;
	private $id_u;
	private $signServer;
	
	public function __construct(SignServer $signServer, SQLQuery $sqlQuery, $id_u){
		$this->sqlQuery = $sqlQuery;
		$this->id_u = $id_u;
		$this->signServer = $signServer;
	}
	
	public function add($type,$id_e,$id_d,$action,$message){
		return $this->addSQL($type,$id_e,$this->id_u,$id_d,$action,$message);
	}
	
	public function addActionAutomatique($type,$id_e,$id_d,$action,$message){
		return $this->addSQL($type,$id_e,0,$id_d,$action,$message);
	}
	
	public function addSQL($type,$id_e,$id_u,$id_d,$action,$message){
		$now = date(Date::DATE_ISO);
		$message_horodate = "$type - $id_e - $id_u - $id_d - $action - $message - $now";
		
		$preuve = $this->signServer->getTimestampReply($message_horodate);
		$date_horodatage = "";
		if ($preuve){
			$date_horodatage = $this->signServer->getLastTimestamp();
		}
		$sql = "INSERT INTO journal(type,id_e,id_u,id_d,action,message,date,message_horodate,preuve,date_horodatage) VALUES (?,?,?,?,?,?,?,?,?,?)";
		$this->sqlQuery->query($sql,array($type,$id_e,$id_u,$id_d,$action,$message,$now,$message_horodate,$preuve,$date_horodatage));
		
		return $preuve;
	}
	
	
	public function getAll($id_e,$type,$id_d,$offset,$limit){
		
		$value = array();
		$sql = "SELECT journal.*,document.titre,entite.denomination, utilisateur.nom, utilisateur.prenom FROM journal " .
			" LEFT JOIN document ON journal.id_d = document.id_d " .
			" LEFT JOIN entite ON journal.id_e = entite.id_e " .
			" LEFT JOIN utilisateur ON journal.id_u = utilisateur.id_u " .
			" WHERE 1=1 "; 
		
		if ($id_e){
			$sql .= "AND journal.id_e = ? ";
			$value[] = $id_e;
		}
		if ($type){
			$sql .= " AND document.type=?";
			$value[] = $type;
		}
		if ($id_d){
			$sql .= " AND document.id_d = ? ";
			$value[] = $id_d;
		}
		
		$sql .= " ORDER BY id_j DESC LIMIT $offset,$limit";
		return $this->sqlQuery->fetchAll($sql,$value);
	}
	
	public function countAll($id_e,$type,$id_d){
		$sql = "SELECT count(journal.id_j) FROM journal LEFT JOIN document ON journal.id_d= document.id_d  WHERE 1 = 1 ";
		$value = array();
		
		if ($id_e){
			"AND id_e = ?";
			$value[] = $id_e;
		}
		if ($type){
			$sql .= " AND document.type=?";
			$value[] = $type;
		}
		if ($id_d){
			$sql .= " AND document.id_d = ? ";
			$value[] = $id_d;
		}
		
		return $this->sqlQuery->fetchOneValue($sql,$value);
	}
	
	public function getAllTransactionBySiren($siren,$offset,$limit){
			$sql = "SELECT journal.* FROM transaction_role "  .  
					" JOIN transaction ON transaction_role.id_t = transaction.id_t " . 
					" AND transaction_role.siren=? " . 
					" JOIN journal ON journal.id_t = transaction.id_t ".
					" ORDER BY id_j DESC LIMIT $offset,$limit";
		return $this->sqlQuery->fetchAll($sql,array($siren));		
	}	
	
	public function countBySiren($siren){
		$sql = "SELECT count(*) FROM transaction_role "  .  
					" JOIN transaction ON transaction_role.id_t = transaction.id_t " . 
					" AND transaction_role.siren=? " . 
					" JOIN journal ON journal.id_t = transaction.id_t ";
		return $this->sqlQuery->fetchOneValue($sql,array($siren));
	}
	
	public function getTypeAsString($type){
		$type_string = array(1=>"Action sur un document",
						"Notification","Gestion des entités","Gestion des utilisateurs","Mail sécurisé");
		return $type_string[$type];
	}
	
	public function getInfo($id_j){
		$sql = "SELECT * FROM journal WHERE id_j=?";
		return $this->sqlQuery->fetchOneLine($sql,$id_j);
	}
	public function getAllInfo($id_j){
		$sql = "SELECT journal.*,document.titre,entite.denomination, utilisateur.nom, utilisateur.prenom FROM journal " .
			" LEFT JOIN document ON journal.id_d = document.id_d " .
			" LEFT JOIN entite ON journal.id_e = entite.id_e " .
			" LEFT JOIN utilisateur ON journal.id_u = utilisateur.id_u " .
			" WHERE id_j=?"; 
		return $this->sqlQuery->fetchOneLine($sql,$id_j);
	}
	
}