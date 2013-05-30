<?php 

class APIAction {
	
	private $objectInstancier;
	private $id_u;
	
	public function __construct(ObjectInstancier $objectInstancier,$id_u){
		$this->objectInstancier = $objectInstancier;
		$this->id_u = $id_u;
	}
	
	//SOAP passe la valeur NULL si on ne précise pas de valeur 
	private function setDefault(& $variable,$default){
		if (! $variable){
			$variable = $default;
		}
	}
	
	private function verifDroit($id_e,$droit){
		if  (! $this->objectInstancier->RoleUtilisateur->hasDroit($this->id_u,$droit,$id_e)){
			throw new Exception("Acces interdit id_e=$id_e, droit=$droit,id_u={$this->id_u}");
		}
	}
	
	private function getError($Errormessage){
		$result['status'] = 'error';
		$result['error-message'] = $Errormessage;;
		return $result;
	}
	
	public function version(){
		$infoVersionning = $this->objectInstancier->Versionning->getAllInfo();
		$infoVersionning['version_complete'] = $infoVersionning['version-complete'];
		return $infoVersionning; 
	}
	
	public function documentType(){
		$allDocType = $this->objectInstancier->DocumentTypeFactory->getAllType();
		$allDroit = $this->objectInstancier->RoleUtilisateur->getAllDroit($this->id_u);
		
		foreach($allDocType as $type_flux => $les_flux){
			foreach($les_flux as $nom => $affichage) {
				if ($this->objectInstancier->RoleUtilisateur->hasOneDroit($this->id_u,$nom.":lecture")){
					$allType[$nom]  = array('type'=>$type_flux,'nom'=>$affichage);
				}
			}
		}		
		return $allType;
	}
	
	public function documentTypeInfo($type){
		$this->setDefault($type,'');
		if ( !  $this->objectInstancier->RoleUtilisateur->hasOneDroit($this->id_u,"$type:lecture")) {
				throw new Exception("Acces interdit type=$type,id_u=$this->id_u");
		}
		
		$documentType = $this->objectInstancier->documentTypeFactory->getFluxDocumentType($type);
		$formulaire = $documentType->getFormulaire();
		
		foreach($formulaire->getAllFields() as $key => $fields){	
			$result[$key] = $fields->getAllProperties(); 	
		}
		return $result;
	}
	
	public function listEntite(){
		return $this->objectInstancier->RoleUtilisateur->getAllEntiteWithFille($this->id_u,'entite:lecture');
	}
	
	public function listDocument($id_e,$type,$offset,$limit){
		$this->setDefault($id_e,0);
		$this->setDefault($type,'');
		$this->setDefault($offset,0);
		$this->setDefault($limit,100);
		$this->verifDroit($id_e,"$type:lecture");
		return $this->objectInstancier->DocumentActionEntite->getListDocument($id_e , $type , $offset, $limit) ;
	}

	public function detailDocument($id_e,$id_d){
		$document = $this->objectInstancier->Document;
		$info = $document->getInfo($id_d);
		$result['info'] = $info;
		
		$this->verifDroit($id_e,$info['type'].":edition");
		
		$donneesFormulaire  = $this->objectInstancier->donneesFormulaireFactory->get($id_d,$info['type']);
		$actionPossible = $this->objectInstancier->ActionPossible;
		
		$result['data'] = $donneesFormulaire->getRawData();
		$result['action-possible'] = $actionPossible->getActionPossible($id_e,$this->id_u,$id_d);
		$result['action_possible'] = $result['action-possible']; 
		
		$result['last_action'] = $this->objectInstancier->DocumentActionEntite->getLastActionInfo($id_e,$id_d);
		
		return $result;
	}
	
	public function detailSeveralDocument($id_e,array $all_id_d){
		$result = array();
		foreach($all_id_d as $id_d) {
			$result[$id_d] = $this->detailDocument($id_e, $id_d);
		} 
		return $result;
	}
	
	public function createDocument($id_e,$type){
		$this->verifDroit($id_e,"$type:edition");
		$document = $this->objectInstancier->Document;
		$id_d = $document->getNewId();	
		$document->save($id_d,$type);
		$this->objectInstancier->DocumentEntite->addRole($id_d,$id_e,"editeur");
		
		$actionCreator = new ActionCreator($this->objectInstancier->SQLQuery, $this->objectInstancier->Journal, $id_d);
		$actionCreator->addAction($id_e,$this->id_u,Action::CREATION,"Création du document [webservice]");
		
		$info['id_d'] = $id_d;
		return $info;
	}

	public function externalData($id_e, $id_d,$field){
		$document = $this->objectInstancier->Document;
		$info = $document->getInfo($id_d);
		
		$this->verifDroit($id_e,"{$info['type']}:edition");
				
		$documentType =  $this->objectInstancier->documentTypeFactory->getFluxDocumentType($info['type']);
		$formulaire = $documentType->getFormulaire();
		$theField = $formulaire->getField($field);
		
		if ( ! $theField ){
			throw new Exception("Type $field introuvable");
		}
		
		$action_name = $theField->getProperties('choice-action');
		return $this->objectInstancier->ActionExecutorFactory->displayChoice($id_e,$this->id_u,$id_d,$action_name,true,$field);	
	}
	
	public function modifDocument($data,FileUploader $fileUploader = null){
		$id_e = $data['id_e'];
		$id_d = $data['id_d'];
		$document = $this->objectInstancier->Document;
		$info = $document->getInfo($id_d);
		$this->verifDroit($id_e, "{$info['type']}:edition");
		
		unset($data['id_e']);
		unset($data['id_d']);
		
		$donneesFormulaire = $this->objectInstancier->DonneesFormulaireFactory->get($id_d);
		$actionPossible = $this->objectInstancier->ActionPossible;
		
		if ( ! $actionPossible->isActionPossible($id_e,$this->id_u,$id_d,'modification')) {
			throw new Exception("L'action « modification »  n'est pas permise");
		}
		
		$donneesFormulaire->setTabDataVerif($data);
		if ($fileUploader) {
			$donneesFormulaire->saveAllFile($fileUploader);
		} 
		return $this->changeDocumentFormulaire($id_e,$id_d,$info['type'],$donneesFormulaire);
	}

	public function sendFile($id_e, $id_d,$field_name, $file_name,$file_number,$file_content){
		$document = $this->objectInstancier->Document;
		$info = $document->getInfo($id_d);
		$this->verifDroit($id_e, "{$info['type']}:edition");
		$donneesFormulaire = $this->objectInstancier->DonneesFormulaireFactory->get($id_d,$info['type']);
		$donneesFormulaire->addFileFromData($field_name,$file_name,$file_content,$file_number);
		return $this->changeDocumentFormulaire($id_e,$id_d,$info['type'],$donneesFormulaire);
	}
	
	private function changeDocumentFormulaire($id_e,$id_d, $type,DonneesFormulaire $donneesFormulaire){
		$documentType = $this->objectInstancier->DocumentTypeFactory->getFluxDocumentType($type);
		$formulaire = $documentType->getFormulaire();
	
		$titre_field = $formulaire->getTitreField();
		$titre = $donneesFormulaire->get($titre_field);
		
		$document = $this->objectInstancier->Document;
		$document->setTitre($id_d,$titre);
		
		foreach($donneesFormulaire->getOnChangeAction() as $action) {	
			$result = $this->objectInstancier->ActionExecutorFactory->executeOnDocument($id_e,$this->id_u,$id_d,$action,array(),true);
		}
				
		$actionCreator = new ActionCreator($this->objectInstancier->SQLQuery,$this->objectInstancier->Journal,$id_d);
		$actionCreator->addAction($id_e,$this->id_u,Action::MODIFICATION,"Modification du document [WS]");
		
		$result['result'] = "ok";
		$result['formulaire_ok'] = $donneesFormulaire->isValidable()?1:0;
		if (! $result['formulaire_ok']){
			$result['message'] = $donneesFormulaire->getLastError();
		} else {
			$result['message'] = "";
		}
		return $result;
	}
	
	public function receiveFile($id_e, $id_d,$field_name,$file_number){
		$document = $this->objectInstancier->Document;
		$info = $document->getInfo($id_d);
		$this->verifDroit($id_e, "{$info['type']}:lecture");
		$donneesFormulaire = $this->objectInstancier->DonneesFormulaireFactory->get($id_d);
		$result['file_name'] = $donneesFormulaire->getFileName($field_name,$file_number);
		$result['file_content'] = $donneesFormulaire->getFileContent($field_name,$file_number);
		return $result;
	}
	
	public function action($id_e, $id_d,$action,$id_destinataire = array()){
		$this->setDefault($id_destinataire,array());
		$document = $this->objectInstancier->Document;
		$info = $document->getInfo($id_d);
		$this->verifDroit($id_e, "{$info['type']}:edition");
		
		$actionPossible = $this->objectInstancier->ActionPossible;
		
		if ( ! $actionPossible->isActionPossible($id_e,$this->id_u,$id_d,$action)) {
			throw new Exception("L'action « $action »  n'est pas permise : " .$actionPossible->getLastBadRule());
		}
		
		$result = $this->objectInstancier->ActionExecutorFactory->executeOnDocument($id_e,$this->id_u,$id_d,$action,$id_destinataire,true);
		$message = $this->objectInstancier->ActionExecutorFactory->getLastMessage();
		
		if ($result){
			return array("result" => $result,"message"=>$message);
		} else {
			return $this->getError($message);
		}
	}
	
}