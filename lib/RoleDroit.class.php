<?php
class RoleDroit {
	
	private $documentTypeFactory;
	
	public function __construct(DocumentTypeFactory $documentTypeFactory){
		$this->documentTypeFactory = $documentTypeFactory;
	}

	public function getAllDroit(){
		$droit = array(	'entite:edition',
						'entite:lecture',
						'utilisateur:lecture',
						'utilisateur:edition',
						'role:lecture',
						'role:edition',
						'journal:lecture',
						'system:lecture',
						'system:edition',
						'annuaire:lecture',
						'annuaire:edition',
					);
		foreach($this->documentTypeFactory->getAllType() as $type_famille){
			foreach($type_famille as $type_id => $type_libelle){
				$droit[] = "$type_id:lecture";
				$droit[] = "$type_id:edition";
			}
		}			
		return $droit;
	}
	
}