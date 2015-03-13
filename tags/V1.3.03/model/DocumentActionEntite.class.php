<?php

class DocumentActionEntite extends SQL {

	public function add($id_a,$id_e,$id_j){
		$sql = "INSERT INTO document_action_entite (id_a,id_e,id_j) VALUES (?,?,?)";
		$this->query($sql,$id_a,$id_e,$id_j);
	}
	
	public function getTrueAction($id_e,$id_d){
		$sql = "SELECT document_entite.last_action " .
			" FROM document_entite " .  
			" JOIN document ON document_entite.id_d = document.id_d" .
			" WHERE document_entite.id_e = ? " . 
			" AND document.id_d=?";
		return $this->queryOne($sql,$id_e,$id_d);	
	}

	public function getCreatorOfDocument($id_e,$id_d){
		$sql = "SELECT utilisateur.* FROM document_action " .
				" JOIN utilisateur ON document_action.id_u = utilisateur.id_u " .
				" WHERE document_action.id_e=? AND id_d=? " .
				" ORDER BY id_a ASC" . 
				" LIMIT 1 ";
		return $this->queryOne($sql,$id_e,$id_d);
	}
	
	public function getLastAction($id_e,$id_d){
		$sql = "SELECT action FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " . 
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND id_d=? ORDER BY date DESC,document_action.id_a DESC LIMIT 1 ";
		return $this->queryOne($sql,$id_e,$id_d);
	}
	
	public function getLastActionNotModif($id_e,$id_d){
		$sql = "SELECT action FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " . 
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND id_d=? AND action != ? ORDER BY date DESC,document_action.id_a DESC LIMIT 1 ";
		return $this->queryOne($sql,$id_e,$id_d,'modification');
	}
	
	
	public function getLastActionInfo($id_e,$id_d){
		$sql = "SELECT document_action.action, journal.message, document_action.date " .
			" FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " .
			" LEFT JOIN journal ON journal.id_j=document_action_entite.id_j " . 
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND document_action.id_d=? " . 
			" ORDER BY document_action.date DESC,document_action.id_a DESC LIMIT 1 ";
		return $this->queryOne($sql,$id_e,$id_d);
	}
	
	public function getAction($id_e,$id_d){
		$sql = "SELECT document_action.action,document_action.date,document_action_entite.id_e,document_action.id_u,denomination,nom,prenom,document_action_entite.id_j" .
			" FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " .
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND document_action.id_d=? ORDER BY document_action.date,document_action_entite.id_a ";
		return $this->query($sql,$id_e,$id_d);
	}
	
	public function getNbDocument($id_e,$type,$search,$etat = false){
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? AND document.titre LIKE ?" ;
		$data = array($id_e,$type,"%$search%");
		if ($etat){
			$sql .= " AND document_entite.last_action=?";
			$data[] = $etat; 
		}

		return $this->queryOne($sql,$data);
	}
	
	public function getOffset($last_id,$id_e,$type,$limit){
		$sql = "SELECT document_entite.last_action_date FROM document_entite WHERE id_d=? AND id_e=?";
		$last_date = $this->queryOne($sql,$last_id,$id_e);
		if (!$last_date){
			return 0;
		}
		
		$sql = "SELECT count(*)" .
				"  FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d " .
				" WHERE document_entite.id_e = ? AND document.type=? AND document_entite.last_action_date > ?" ;
		$nb =  $this->queryOne($sql,$id_e,$type,$last_date);
		
		$offset = floor($nb / $limit)*$limit;
		return $offset;
	}
	
	
	public function getInfo($id_d,$id_e){
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date FROM document_entite " .
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.id_d=? ";
		$result =  $this->queryOne($sql,$id_e,$id_d);
		$sql = "SELECT action  FROM document_action " .
				" JOIN document_action_entite ON document_action.id_a = document_action_entite.id_a " .
				" WHERE document_action_entite.id_e=? AND document_action.id_d= ? " .
				" ORDER BY document_action.date DESC, document_action.id_a DESC LIMIT 1";
		$result['last_action_display'] = $this->queryOne($sql,$id_e,$id_d);
		return $result;
		
	}
	
	public function getListDocument($id_e,$type,$offset,$limit,$search="",$etat = false){
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type=? " . 
				" AND document.titre LIKE ?" ;
		$data = array($id_e,$type,"%$search%");
		
		if ($etat){
			$sql .= " AND document_entite.last_action=?";
			$data[] = $etat; 
		}	
	
		$sql .= " ORDER BY document_entite.last_action_date DESC LIMIT $offset,$limit";	
			
		$list = $this->query($sql,$data);
		return $this->addEntiteToList($id_e,$list);
	}
	
	public function  getListDocumentByEntite($id_e,array $type_list,$offset,$limit,$search){
		
		$type_list = "'" . implode("','",$type_list) . "'";
		
		$sql = "SELECT *,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date  FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.type IN ($type_list) " . 
				" AND document.titre LIKE ?" .
				" ORDER BY document_entite.last_action_date DESC LIMIT $offset,$limit";	
		$list = $this->query($sql,$id_e,"%$search%");
		return $this->addEntiteToList($id_e,$list);
	}
	
	private function addEntiteToList($id_e,$list){
		
		foreach($list as $i => $doc){
			$id_d = $doc['id_d'];
			$sql = "SELECT DISTINCT document_action.id_e,entite.denomination " .
					" FROM document_action ".
					" JOIN document_action_entite ON document_action.id_a = document_action_entite.id_a " . 
					" JOIN entite ON entite.id_e=document_action.id_e ". 
					" WHERE id_d= ? AND document_action_entite.id_e=? AND entite.id_e != ?";
			$list[$i]['entite'] = $this->query($sql,$id_d,$id_e,$id_e);
			
			$sql = "SELECT action  FROM document_action " .
					" JOIN document_action_entite ON document_action.id_a = document_action_entite.id_a " .
					" WHERE document_action_entite.id_e=? AND document_action.id_d= ? " .
					" ORDER BY document_action.date DESC, document_action.id_a DESC LIMIT 1";
			$list[$i]['last_action_display'] = $this->queryOne($sql,$id_e,$id_d); 
			
		}
		return $list;
	}
	
	public function getNbDocumentByEntite($id_e,array $type_list,$search){
		
		$type_list = "'" . implode("','",$type_list) . "'";
			
		$sql = "SELECT count(*) FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" WHERE document_entite.id_e = ? AND document.titre LIKE ? AND document.type IN ($type_list) " ;

		return $this->queryOne($sql,$id_e,"%$search%");
	}
	
	public function getUserFromAction($id_e,$id_d,$action){
		$sql = "SELECT * FROM document_action_entite " .
			" JOIN document_action ON document_action_entite.id_a = document_action.id_a ".
			" LEFT JOIN utilisateur ON document_action.id_u = utilisateur.id_u " . 
			" JOIN entite ON document_action.id_e  = entite.id_e ".
			" WHERE document_action_entite.id_e = ? AND id_d=? AND document_action.action= ? LIMIT 1";
		return $this->queryOne($sql,$id_e,$id_d,$action);
	}
	
	
	public function getNbDocumentBySearch($id_e,$type,$search,$state,$last_state_begin,$last_state_end,$allDroitEntite,$etatTransit,$state_begin,$state_end,$indexedFieldValue = array()){
		$col = "count(*) as nb";
		$order = "";	
		$result = $this->getSearchSQL($col,$order,$id_e,$type,$search,$state,$last_state_begin,$last_state_end,$allDroitEntite,$etatTransit,$state_begin,$state_end,$indexedFieldValue);
		return $result[0]['nb'];
		
	}
	
	public function getListBySearch($id_e,$type,$offset,$limit,$search,$state,$last_state_begin,$last_state_end,$tri,$allDroitEntite,$etatTransit,$state_begin,$state_end,$indexedFieldValue=array(),$sens_tri='DESC'){
		$col = "*,document.type as type,document_entite.last_action as last_action,document_entite.last_action_date as last_action_date, entite.denomination as entite_base";
		
		if (! in_array($sens_tri,array('ASC','DESC'))){
			$sens_tri ='DESC';
		}
		
		if (! in_array($tri, array_keys($indexedFieldValue))) {
			switch($tri){
				case 'entite': $tri="entite.denomination $sens_tri"; break;
				case 'titre':
				case 'title': $tri = "document.titre $sens_tri,document.id_d $sens_tri"; break;
				default: $tri =  "document_entite.last_action_date $sens_tri";
			}
			$order = " ORDER BY $tri LIMIT $offset,$limit ";	
		} else {
			$order = " ORDER BY document_index.field_value $sens_tri LIMIT $offset,$limit ";
		}
		
		$list = $this->getSearchSQL($col,$order,$id_e,$type,$search,$state,$last_state_begin,$last_state_end,$allDroitEntite,$etatTransit,$state_begin,$state_end,$indexedFieldValue,$tri);
		return $this->addEntiteToList($id_e,$list);
	}	
	
	private function getSearchSQL($col,$order,$id_e,$type,$search,$state,$last_state_begin,$last_state_end,$allDroitEntite,$etatTransit,$state_begin,$state_end,$indexedFieldValue = array(),$triIndexColName=false){
		
		$type_list = "'" . implode("','",$allDroitEntite) . "'";
		
		
		$sql = "SELECT $col " .
				" FROM document_entite " .  
				" JOIN document ON document_entite.id_d = document.id_d" .
				" JOIN entite ON document_entite.id_e = entite.id_e";
			
		if ($etatTransit){
			$sql .= " JOIN document_action ON document_action.id_d=document.id_d AND document_action.id_e=entite.id_e ";	
		}
		
		$binding = array();
		
		$i = 0;
		foreach( $indexedFieldValue as $indexName => $indexValue){
			if ($indexValue ){
				$sql .= " JOIN document_index as document_index_$i ON document.id_d=document_index_$i.id_d " .
						" AND document_index_$i.field_name=? AND document_index_$i.field_value LIKE ? ";
				$binding[] = $indexName;
				$binding[] = "%$indexValue%";
			}
			$i++;
		}
		
		if ($triIndexColName && in_array($triIndexColName, array_keys($indexedFieldValue))){
			$sql .= " LEFT JOIN document_index ON document.id_d=document_index.id_d AND document_index.field_name=? ";
			$binding[] = $triIndexColName;		
		}
		
		
		
		$sql .= " WHERE document.type IN ($type_list) ";
				
		
		if ($id_e){
			$sql .= " AND document_entite.id_e = ? ";
			$binding[] = $id_e;
		}
		if ($type){
			$sql.= " AND document.type=? " ;
			$binding[] = $type;
		}
		if ($search){
			$sql .=" AND document.titre LIKE ?" ;
			$binding[] = "%$search%";
		}
		if ($state) {
			foreach(explode(",",$state) as $s){
				$r[]="'".addslashes($s)."'";
			}
			$sql .= " AND document_entite.last_action IN (".implode(",",$r).")";
		}
		if ($last_state_begin){
			$sql .= " AND document_entite.last_action_date>?";
			$binding[] = $last_state_begin;
		}
		if ( $last_state_end){
			$sql .= " AND document_entite.last_action_date<?";
			$binding[] = $last_state_end;
		}
		if ($etatTransit){
			$sql .= " AND document_action.action = ? ";
			$binding[] = $etatTransit;	
			if ($state_begin){
				$sql .= " AND document_action.date>?";
				$binding[] = $state_begin;
			}
			if ( $state_end){
				$sql .= " AND document_action.date<?";
				$binding[] = $state_end;
			}			
		}
		
		
		$sql .= $order;	
		$list = $this->query($sql,$binding);
		return $list;
	}
	

	
	
}