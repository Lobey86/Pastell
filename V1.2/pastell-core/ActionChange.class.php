<?php 
class ActionChange extends SQL {
	
	private $documentActionSQL;
	private $documentActionEntite;
	private $journal;
		
	public function __construct(DocumentActionSQL $documentActionSQL,DocumentActionEntite $documentActionEntite,Journal $journal,SQLQuery $sqlQuery){
		parent::__construct($sqlQuery);
		$this->documentActionSQL = $documentActionSQL;
		$this->documentActionEntite = $documentActionEntite;
		$this->journal = $journal;	
	}
	
	public function addAction($id_d,$id_e,$id_u,$action,$message_journal){
		$id_a = $this->documentActionSQL->add($id_d, $id_e, $id_u, $action);
		$id_j = $this->journal->addSQL(Journal::DOCUMENT_ACTION,$id_e,$id_u,$id_d,$action,$message_journal);
		$this->documentActionEntite->add($id_a, $id_e, $id_j);
	}
	
	public function updateModification($id_d, $id_e,$id_u,$action){				
		$document_action = $this->documentActionSQL->getLastActionInfo($id_d, $id_e); 		
		if ( ! $document_action || $document_action['id_u'] != $id_u || $document_action['action'] != $action){
			return $this->addAction($id_d,$id_e, $id_u, $action,"Modification du document");
		}
		$this->documentActionSQL->updateDate($document_action['id_a']);
		$this->journal->addSQL(Journal::DOCUMENT_ACTION,$id_e,$id_u,$id_d,$action,"Modification du document");
	}

}