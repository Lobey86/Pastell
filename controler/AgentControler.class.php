<?php
class AgentControler extends PastellControler {
	
	public function listAgent(){
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$offset = $recuperateur->getInt('offset',0);
		$page = $recuperateur->getInt('page',0);
		$search = $recuperateur->get('search');
		
		$this->hasDroitLecture($id_e);
		$info = $this->EntiteSQL->getInfo($id_e);
		$id_ancetre = $this->EntiteSQL->getCollectiviteAncetre($id_e);
		if ($id_ancetre == $id_e){
			$siren = $info['siren'];
		} else {
			$infoAncetre = $this->EntiteSQL->getInfo($id_ancetre);
			$siren = $infoAncetre['siren'];
		}
		if ($id_e){
			$this->nbAgent = $this->AgentSQL->getNbAgent($siren,$search);
			$this->listAgent = $this->AgentSQL->getBySiren($siren,$offset,$search);
		} else {
			$this->nbAgent = $this->AgentSQL->getNbAllAgent($search);
			$this->listAgent = $this->AgentSQL->getAllAgent($search,$offset);
		}
		$this->offset = $offset;
		$this->page = $page;
		$this->id_ancetre = $id_ancetre;
		$this->droit_edition = $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"entite:edition",$id_e);
		$this->id_e = $id_e;
		$this->search = $search;
		$this->render("AgentList");
	}
	
}