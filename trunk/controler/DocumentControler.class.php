<?php


class DocumentControler extends PastellControler {
	
	private function redirectToList($id_e,$type = false){
		$this->redirect("/document/list.php?id_e=$id_e&type=$type");
	}
	
	private function verifDroitLecture($id_e,$id_d){
		$info = $this->Document->getInfo($id_d);
		if (!$info){
			$this->redirectToList($id_e);
		}

		if ( ! $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),$info['type'].":lecture",$id_e)) {
			$this->redirectToList($id_e,$info['type']);
		}
		
		$my_role = $this->DocumentEntite->getRole($id_e,$id_d);
		if (! $my_role ){
			$this->redirectToList($id_e,$info['type']);
		}
		return $info;
	}
	
	public function detailAction(){
		$recuperateur = new Recuperateur($_GET);
		$id_d = $recuperateur->get('id_d');
		$id_e = $recuperateur->getInt('id_e');
		$page = $recuperateur->getInt('page',0);

		$info_document = $this->verifDroitLecture($id_e, $id_d);
		$this->Journal->addConsultation($id_e,$id_d,$this->Authentification->getId());
		
		$documentType = $this->DocumentTypeFactory->getFluxDocumentType($info_document['type']);
		

		$this->info = $info_document;
		$this->id_e = $id_e;
		$this->id_d = $id_d;
		$this->page = $page;
		$this->documentType = $documentType;
		$this->infoEntite = $this->EntiteSQL->getInfo($id_e);
		$this->formulaire =  $documentType->getFormulaire();
		$this->donneesFormulaire = $this->DonneesFormulaireFactory->get($id_d,$info_document['type']);
		$this->actionPossible = $this->ActionPossible;
		$this->theAction = $documentType->getAction();
		$this->documentEntite = $this->DocumentEntite;
		$this->my_role = $this->documentEntite->getRole($id_e,$id_d);
		$this->documentEmail = $this->DocumentEmail;
		$this->documentActionEntite = $this->DocumentActionEntite;
		
		
		$this->page_title =  $info_document['titre'] . " (".$documentType->getName().")";;
		$this->template_milieu = "DocumentDetail"; 
		$this->renderDefault();
	}
	

	
}