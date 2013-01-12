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
	
	
}