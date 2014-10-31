<?php
class Journal extends SQL {
	
	const DOCUMENT_ACTION = 1;
	const NOTIFICATION = 2;
	const MODIFICATION_ENTITE = 3;
	const MODIFICATION_UTILISATEUR = 4;
	const MAIL_SECURISE = 5;
	const CONNEXION = 6;
	const DOCUMENT_CONSULTATION = 7 ;
	const ENVOI_MAIL = 8;
	const DOCUMENT_ACTION_ERROR = 9;
	const DOCUMENT_TRAITEMENT_LOT = 10;
	
	private $id_u;
	private $utilisateurSQL;
	private $documentSQL;
	private $documentTypeFactory;
	
	private $horodateur;
	
	public function __construct(SQLQuery $sqlQuery, Utilisateur $utilisateurSQL, Document $documentSQL, DocumentTypeFactory $documentTypeFactory){
		parent::__construct($sqlQuery);
		$this->utilisateurSQL = $utilisateurSQL;
		$this->documentSQL = $documentSQL;
		$this->documentTypeFactory = $documentTypeFactory;
	}
	
	public function setHorodateur(Horodateur $horodateur){
		$this->horodateur = $horodateur;
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
	
	public function add($type_journal,$id_e,$id_d,$action,$message){
		return $this->addSQL($type_journal,$id_e,$this->id_u,$id_d,$action,$message);
	}
	
	public function addActionAutomatique($type,$id_e,$id_d,$action,$message){
		return $this->addSQL($type,$id_e,0,$id_d,$action,$message);
	}
	
	public function addSQL($type,$id_e,$id_u,$id_d,$action,$message){
		if ($id_d){
			$document_info = $this->documentSQL->getInfo($id_d);
			$document_type = $document_info['type']?:"";
		} else {
			$document_type = "";
		}
		if (!$id_e){
			$id_e = "0";
		}
		
		$now = date(Date::DATE_ISO);
		$message_horodate = "$type - $id_e - $id_u - $id_d - $action - $message - $now - $document_type";
		
		$preuve = "";
		$date_horodatage = "";
		
		if ($this->horodateur){
			$preuve = $this->horodateur->getTimestampReply($message_horodate);
		} 
		if ($preuve) {
			$date_horodatage = $this->horodateur->getTimeStamp($preuve);
		}
	
		
		$sql = "INSERT INTO journal(type,id_e,id_u,id_d,action,message,date,message_horodate,preuve,date_horodatage,document_type) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
		$this->query($sql,$type,$id_e,$id_u,$id_d,$action,$message,$now,$message_horodate,$preuve,$date_horodatage,$document_type);
		
		$sql = "SELECT id_j FROM journal WHERE type=? AND id_e=? AND id_u=? AND id_d=? AND action=? AND date=?  AND preuve=? ";
		return $this->queryOne($sql,$type,$id_e,$id_u,$id_d,$action,$now,$preuve);
	}
	
	
	public function getAll($id_e,$type,$id_d,$id_u,$offset,$limit,$recherche = "",$date_debut=false,$date_fin=false){
		list($sql,$value) = $this->getQueryAll($id_e, $type, $id_d, $id_u, $offset, $limit,$recherche,$date_debut,$date_fin);
		
		$result = $this->query($sql,$value);
		foreach($result as $i => $line){
			$documentType = $this->documentTypeFactory->getFluxDocumentType($line['document_type']);
			$result[$i]['document_type_libelle'] = $documentType->getName();
			$result[$i]['action_libelle'] = $documentType->getAction()->getActionName($line['action']);
		}
		return $result;
	}
	
	public function getQueryAll($id_e,$type,$id_d,$id_u,$offset,$limit,$recherche = "",$date_debut=false,$date_fin=false){
		$value = array();
		$sql = "SELECT journal.*,document.titre,entite.denomination, utilisateur.nom, utilisateur.prenom,entite.siren " .
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
			$sql.= "AND DATE(journal.date) >= ?";
			$value[] = $date_debut;
		}
		if ($date_fin){
			$sql.= "AND DATE(journal.date) <= ?";
			$value[] = $date_fin;
		}
		
		$sql .= " ORDER BY id_j DESC " ;
		if ($limit != -1){
			$sql .= " LIMIT $offset,$limit";
		}
		return array($sql,$value);
	}
	
	
	
	public function countAll($id_e,$type,$id_d,$id_u,$recherche,$date_debut,$date_fin){
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
		if ($date_debut){
			$sql.= "AND DATE(journal.date) >= ?";
			$value[] = $date_debut;
		}
		if ($date_fin){
			$sql.= "AND DATE(journal.date) <= ?";
			$value[] = $date_fin;
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
						"Envoi de mail",
						"Erreur lors de la tentative d'une action",
						"Programmation d'un traitement par lot"
		);
		return $type_string[$type];
	}
	
	public function getInfo($id_j){		
		$sql = "SELECT * FROM journal WHERE id_j=?";
		return $this->queryOne($sql,$id_j);
	}
	
	public function getAllInfo($id_j){
		global $objectInstancier;
		
		$sql = "SELECT journal.*,document.titre,entite.denomination, utilisateur.nom, utilisateur.prenom FROM journal " .
			" LEFT JOIN document ON journal.id_d = document.id_d " .
			" LEFT JOIN entite ON journal.id_e = entite.id_e " .
			" LEFT JOIN utilisateur ON journal.id_u = utilisateur.id_u " .
			" WHERE id_j=?"; 
		$result = $this->queryOne($sql,$id_j);
		
		if (!$result){
			return $result;
		}
		
		$documentType = $this->documentTypeFactory->getFluxDocumentType($result['document_type']);
		$result['document_type_libelle'] = $documentType->getName();
		$result['action_libelle'] = $documentType->getAction()->getActionName($result['action']);
		
		return $result;
	}
	
	public function horodateAll(){
		if (! $this->horodateur){
			echo "Aucun horodateur configuré\n";
			return;
		}
		$sql = "SELECT * FROM journal WHERE preuve=?";
		$all = $this->query($sql,"");
		$sql = "UPDATE journal set preuve=?,date_horodatage=? WHERE id_j=?";
		foreach ($all as $info){
			$preuve = $this->horodateur->getTimestampReply($info['message_horodate']);
			$date_horodatage = $this->horodateur->getTimeStamp($preuve);
			$this->query($sql,$preuve,$date_horodatage,$info['id_j']);
			echo "{$info['id_j']} horodaté : $date_horodatage \n";
			
		}
	}
}