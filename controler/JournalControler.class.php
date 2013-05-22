<?php
class JournalControler extends PastellControler {
	
	public function exportAction(){
				
		$recuperateur = new Recuperateur($_REQUEST);
		$this->id_e = $recuperateur->getInt('id_e',0);
		$this->type = $recuperateur->get('type');
		$this->id_d = $recuperateur->get('id_d');
		$this->id_u = $recuperateur->get('id_u');
		
		$this->verifDroit($this->id_e,'journal:lecture');
		
		$this->entite_info = $this->EntiteSQL->getInfo($this->id_e);	
		$this->utilisateur_info = $this->Utilisateur->getInfo($this->id_u);
		$this->document_info = $this->Document->getInfo($this->id_d);
		
		
		$this->recherche = $recuperateur->get('recherche');
		$this->date_debut = $recuperateur->get('date_debut',date("Y-m-d"));
		$this->date_fin = $recuperateur->get('date_fin',date("Y-m-d"));
	
		$this->page_title="Journal des évènements - Export";
		$this->template_milieu = "JournalExport";
		$this->renderDefault();
	}
	
	public function detailAction(){
		$recuperateur = new Recuperateur($_GET);
		$this->id_j = $recuperateur->getInt('id_j',0);
		$this->offset = $recuperateur->getInt('offset',0);
		$this->id_e = $recuperateur->getInt('id_e',0);
		$this->type = $recuperateur->get('type');
		$this->id_d = $recuperateur->get('id_d');
		
		$this->info = $this->Journal->getAllInfo($this->id_j);
		$this->verifDroit($this->info['id_e'], "journal:lecture");
		
		$this->preuve_txt = $this->OpensslTSWrapper->getTimestampReplyString($this->info['preuve']);
		
		$horodateur = $this->ConnecteurFactory->getGlobalConnecteur('horodateur');
		if ($horodateur) {
			try {
				$horodateur->verify($this->info['message_horodate'],$this->info['preuve']);
				$this->preuve_is_ok = true;
			} 
			catch(Exception $e){
				$this->preuve_is_ok = false;	
				$this->preuve_error = $e->getMessage();
			}
		} else {
			$this->preuve_is_ok = false;	
			$this->preuve_error = "Aucun horodateur n'est configuré";
		}
		
		$this->page_title="Evenement numéro {$this->id_j}";
		$this->template_milieu = "JournalDetail";
		$this->renderDefault();
	}
	
	public function indexAction(){
		
		$recuperateur = new Recuperateur($_GET);
		$id_e = $recuperateur->getInt('id_e',0);
		$this->offset = $recuperateur->getInt('offset',0);
		$this->type = $recuperateur->get('type');
		$this->id_d = $recuperateur->get('id_d');
		$this->id_u = $recuperateur->get('id_u');
		$this->recherche = $recuperateur->get('recherche');
		$this->date_debut = $recuperateur->get('date_debut');
		$this->date_fin = $recuperateur->get('date_fin');
		
		
		$liste_collectivite = $this->RoleUtilisateur->getEntite($this->getId_u(),'journal:lecture');
		
		if ( ! $liste_collectivite){
			header("Location: ". SITE_BASE . "/index.php");
			exit;
		}
		
		if (! $id_e && (count($liste_collectivite) == 1)){
			$id_e = $liste_collectivite[0];
		} 
		$this->verifDroit($id_e, "journal:lecture");
		$this->id_e = $id_e;	
		
		$infoEntite = $this->EntiteSQL->getInfo($this->id_e);
		
		
		$this->count = $this->Journal->countAll($this->id_e,$this->type,$this->id_d,$this->id_u,$this->recherche);
		
		$page_title="Journal des évènements";
		if ($this->id_e){
			$page_title .= " - ".$infoEntite['denomination'];
		}
		if ($this->type){
			$page_title .= " - " . $this->type;
		}
		if ($this->id_d) {
			$documentInfo = $this->Document->getInfo($this->id_d);
			$page_title .= " - " . $documentInfo['titre'];
		}
		if ($this->id_u){
			$infoUtilisateur = $this->Utilisateur->getInfo($this->id_u);
			$page_title .= " - " . $infoUtilisateur['prenom'] ." " . $infoUtilisateur['nom'];
		}
		
		$this->limit = 20;
		$this->all = $this->Journal->getAll($this->id_e,$this->type,$this->id_d,$this->id_u,$this->offset,$this->limit,$this->recherche,$this->date_debut,$this->date_fin) ;
		$this->liste_collectivite = $liste_collectivite;
		
		$this->setNavigationInfo($id_e, "journal/index.php?a=a");
		$this->infoEntite  = $infoEntite;
		$this->page_title = $page_title;
		$this->template_milieu = "JournalIndex";
		$this->renderDefault();
	}
	
}