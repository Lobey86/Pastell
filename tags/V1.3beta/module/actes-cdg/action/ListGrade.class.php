<?php 

class ListGrade extends ChoiceActionExecutor {
	
	public function go(){
		$recuperateur = new Recuperateur($_GET);
		$libelle = $recuperateur->get('libelle');
		$donneesFormulaire = $this->getDonneesFormulaire();		
		$donneesFormulaire->setData('grade',$libelle);
	}
	
	public function displayAPI(){
		return $this->objectInstancier->GradeSQL->getAll();
	}
	
	public function display(){
		$info = $this->objectInstancier->Document->getInfo($this->id_d);
		$this->titre = $info['titre'];
		$this->all_grade = $this->objectInstancier->GradeSQL->getAll();
		$this->renderPage("Choix d'un grade", "GradeSelect");
	}
	
}