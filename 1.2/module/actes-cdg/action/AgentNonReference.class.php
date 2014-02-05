<?php
class AgentNonReference extends ActionExecutor {
	
	public function go(){
		
		$data['matricule_de_lagent'] = "";
		$data['prenom'] = "";
		$data['nom_patronymique'] = "";
		$data['statut']  = "";
		$data['grade'] = "";
		
		$this->getDonneesFormulaire()->setTabData($data);
		
		$tab_name  =  $this->getDonneesFormulaire()->get("agent_non_reference") ? "Agent non-référencé" : "Agent";
		$page = $this->getFormulaire()->getTabNumber($tab_name);
		
		$this->redirect("/document/edition.php?id_d={$this->id_d}&id_e={$this->id_e}&page={$page}");
		
	}
	
}