<?php
class DocumentIndexor {
	
	private $documentIndexSQL;
	private $id_d;
	
	public function __construct(DocumentIndexSQL $documentIndexSQL,$id_d){
		$this->documentIndexSQL = $documentIndexSQL;
		$this->id_d = $id_d;
	}
	
	public function index($fieldName,$fieldValue){
		$this->documentIndexSQL->index($this->id_d, $fieldName, $fieldValue);
	}
}