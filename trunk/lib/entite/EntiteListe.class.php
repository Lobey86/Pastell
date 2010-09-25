<?php
require_once("Entite.class.php");

class EntiteListe {
	
	private $sqlQuery;
	private $recherche;
	
	public function __construct(SQLQuery $sqlQuery){
		$this->sqlQuery = $sqlQuery;
	}
	
	public function setFiltre($recherche){
		if (! $recherche){
			return;
		}
		$this->recherche = "%".$recherche."%";
	}
	
	public function countCollectivite(){
		$sql = "SELECT count(*) FROM entite WHERE type=? OR type=? ORDER BY denomination" ;
		return $this->sqlQuery->fetchOneValue($sql,Entite::TYPE_COLLECTIVITE,Entite::TYPE_CENTRE_DE_GESTION);
	}
	
	public function getCollectivite($offset,$limit){
		$sql = "SELECT * FROM entite WHERE type=? OR type=? ORDER BY denomination LIMIT $offset,$limit" ;
		return $this->sqlQuery->fetchAll($sql,Entite::TYPE_COLLECTIVITE,Entite::TYPE_CENTRE_DE_GESTION);
	}
	
	public function getAll($type){
		$param = array($type);
		
		$sql = "SELECT * FROM entite WHERE type=? " ;
		if ($this->recherche){
			$sql .= " AND denomination LIKE ?" ;
			$param[] = $this->recherche;
		}
		
		$sql .= " ORDER BY denomination";
		
		
		return $this->sqlQuery->fetchAll($sql,$param);
	}
	
	public function getInfoFromArray(array $tabId_e){
		$str = str_pad("",count($tabId_e),"?");
		$array = implode(',',str_split ($str));
		$sql = "SELECT id_e,siren,denomination FROM entite WHERE id_e IN ($array)";
		$result = $this->sqlQuery->fetchAll($sql,$tabId_e);
		return $result;
	}
	
	public function getAllPossibleMother(){
		$sql = "SELECT * FROM entite WHERE type != ?";
		return $this->sqlQuery->fetchAll($sql,array(Entite::TYPE_FOURNISSEUR));
	}
	
	public function getAllFille($id_e){
		$sql = "SELECT * FROM entite WHERE entite_mere=?" ;
		return $this->sqlQuery->fetchAll($sql,$id_e);
	}
	
	public function getArbreFille($id_e,$profondeur = 0){

		$filles = $this->getAllFille($id_e);

		$result = array();
		foreach($filles as $filleInfo){
			$result[] = 
						array(
							'id_e' => $filleInfo['id_e'],
							'denomination' => $filleInfo['denomination'], 
							'profondeur' => $profondeur);
			$arbreFille = $this->getArbreFille($filleInfo['id_e'],$profondeur + 1);
			if ($arbreFille){
				$result =  array_merge($result,$arbreFille);
			}	
		}
		return $result;
	}
	
	public function getArbreFilleFromArray(array $tabId_e){
		$result = array();
		
	
		
		foreach($tabId_e as $id_e){
			if ($id_e != 0) {
			$sql = "SELECT * FROM entite WHERE id_e=?";
			$info = $this->sqlQuery->fetchOneLine($sql,$id_e);
			$result[] = array(
							'id_e' => $info['id_e'],
							'denomination' => $info['denomination'], 
							'profondeur' => 0);
			} else {
				$result[] = array(
							'id_e' => 0,
							'denomination' => "toutes les collecitivtés", 
							'profondeur' => 0);
			} 
			
			$result = array_merge($result,$this->getArbreFille($id_e,1));
		}
		return $result;
	}
	
	
	public function isAncestor($i,$j){
		
		$ancetre = $this->sqlQuery->fetchOneValue("SELECT entite_mere FROM entite WHERE id_e=?",$j);
		if ($ancetre == $i){
			return true;
		}
		if ($ancetre == 0){
			return false;
		}
		return isAncestor($i,$ancetre);		
	}
	
	public function getOnlyAncestor(array $tabId_e){
		$tabId_e = array_unique($tabId_e);
		$entrop = array();
		for($i=0; $i<count($tabId_e); $i++){
			for($j=0; $j<count($tabId_e); $j++){
				if ($i == $j){
					continue;
				}
				if ($this->isAncestor($tabId_e[$i],$tabId_e[$j])){
					$entrop[] = $tabId_e[$j];
				}
			}
		}
		return array_diff($tabId_e,$entrop);
	}
	
	
}