<?php 

class DIAGenerate extends ActionExecutor {
	
	public function go(){
		
		$id_e = $this->id_e;
		
		$file_name  = "DIA_".mt_rand(0,mt_getrandmax()).".xml";
		
		
		$xml = simplexml_load_file(__DIR__."/../fixtures/DIA_DDI-01.xml");
		$file_content = $xml->asXML(); 
		
		$document =  $this->getDocument();
		$id_d = $document->getNewId();	
		
		$document->save($id_d,'dia');
		$document->setTitre($id_d, $file_name);
		
		$documentEntite = new DocumentEntite($this->getSQLQuery());
		$documentEntite->addRole($id_d,$id_e,"editeur");
		$actionCreator = new ActionCreator($this->getSQLQuery(),$this->getJournal(),$id_d);
		$actionCreator->addAction($id_e,0,'creation',"Document récupéré sur le serveur PEC");
		$donneesFormulaire = $this->getDonneesFormulaireFactory()->get($id_d);
		$donneesFormulaire->setData('titre',$file_name);
		$donneesFormulaire->addFileFromData('dia',$file_name,$file_content);
		$this->setLastMessage("Document DIA $file_name créé");			
		return true;
		
	}
	
}