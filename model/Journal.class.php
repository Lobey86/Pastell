<?php

//Il y a un new Utilisateur en plein milieu !

class Journal extends SQL {
	
	const DOCUMENT_ACTION = 1;
	const NOTIFICATION = 2;
	const MODIFICATION_ENTITE = 3;
	const MODIFICATION_UTILISATEUR = 4;
	const MAIL_SECURISE = 5;
	const CONNEXION = 6;
	const DOCUMENT_CONSULTATION = 7 ;
	
	private $id_u;
	private $signServer;
	private $utilisateurSQL;
	
	public function __construct(SignServer $signServer, SQLQuery $sqlQuery){
		parent::__construct($sqlQuery);
		$this->signServer = $signServer;
		$this->utilisateurSQL = new Utilisateur($sqlQuery);
	}
	
	public function setId($id_u){
		$this->id_u = $id_u;
	}
	
	public function addConsultation($id_e,$id_d,$id_u){
		$sql  =  "SELECT count(*) FROM journal WHERE id_u=? AND id_d=?";
		$nb = $this->queryOne($sql,$id_u,$id_d);
		if ($nb){
			return;
		}
		$infoUtilisateur = $this->utilisateurSQL->getInfo($id_u);
		$nom = $infoUtilisateur['prenom']." ".$infoUtilisateur['nom'];
		$this->add(Journal::DOCUMENT_CONSULTATION,$id_e,$id_d,"Consulté","$nom a consulté le document");
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
		$this->query($sql,array($type,$id_e,$id_u,$id_d,$action,$message,$now,$message_horodate,$preuve,$date_horodatage));
		
		return $preuve;
	}
	
	
	public function getAll($id_e,$type,$id_d,$id_u,$offset,$limit,$recherche = "",$date_debut=false,$date_fin=false){
		list($sql,$value) = $this->getQueryAll($id_e, $type, $id_d, $id_u, $offset, $limit,$recherche,$date_debut,$date_fin);
		
		return $this->query($sql,$value);
	}
	
	public function getQueryAll($id_e,$type,$id_d,$id_u,$offset,$limit,$recherche = "",$date_debut=false,$date_fin=false){
		$value = array();
		$sql = "SELECT journal.*,document.titre,document.type as document_type,entite.denomination, utilisateur.nom, utilisateur.prenom " .
			" FROM journal " .
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
			$sql .= " AND journal.id_d = ? ";
			$value[] = $id_d;
		}
		if ($id_u){
			$sql .= " AND journal.id_u = ? ";
			$value[] = $id_u;
		}
		if ($recherche){
			$sql .= " AND journal.message_horodate LIKE ?";
			$value[] = "%$recherche%";
		}
		if ($date_debut){
			$sql.= "AND journal.date_horodatage > ?";
			$value[] = $date_debut;
		}
		if ($date_fin){
			$sql.= "AND journal.date_horodatage < ?";
			$value[] = $date_fin;
		}
		
		$sql .= " ORDER BY id_j DESC LIMIT $offset,$limit";
		return array($sql,$value);
	}
	
	
	
	public function countAll($id_e,$type,$id_d,$id_u,$recherche){
		$sql = "SELECT count(journal.id_j) FROM journal LEFT JOIN document ON journal.id_d= document.id_d  WHERE 1 = 1 ";
		$value = array();
		
		if ($id_e){
			$sql .="AND id_e = ?";
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
		if ($id_u){
			$sql .= " AND journal.id_u = ? ";
			$value[] = $id_u;
		}
		if ($recherche){
			$sql .= " AND journal.message_horodate LIKE ?";
			$value[] = "%$recherche%";
		}
		return $this->queryOne($sql,$value);
	}
	
	
	public function getTypeAsString($type){
		$type_string = array(1=>"Action sur un document",
						"Notification",
						"Gestion des entités",
						"Gestion des utilisateurs",
						"Mail sécurisé",
						"Connexion",
						"Consultation de document",
		);
		return $type_string[$type];
	}
	
	public function getInfo($id_j){
		$sql = "SELECT * FROM journal WHERE id_j=?";
		return $this->queryOne($sql,$id_j);
	}
	public function getAllInfo($id_j){
		$sql = "SELECT journal.*,document.titre,entite.denomination, utilisateur.nom, utilisateur.prenom FROM journal " .
			" LEFT JOIN document ON journal.id_d = document.id_d " .
			" LEFT JOIN entite ON journal.id_e = entite.id_e " .
			" LEFT JOIN utilisateur ON journal.id_u = utilisateur.id_u " .
			" WHERE id_j=?"; 
		return $this->queryOne($sql,$id_j);
	}
	
	public function horodateAll(){
		$sql = "SELECT * FROM journal WHERE preuve=?";
		$all = $this->query($sql,"");
		$sql = "UPDATE journal set preuve=?,date_horodatage=? WHERE id_j=?";
		foreach ($all as $info){
			$preuve = $this->signServer->getTimestampReply($info['message_horodate']);
			$date_horodatage = "";
			if ($preuve){
				$date_horodatage = $this->signServer->getLastTimestamp();
				$this->query($sql,$preuve,$date_horodatage,$info['id_j']);
				echo "{$info['id_j']} horodaté : $date_horodatage \n";
			}
			
		}
		
	}
	
	
}