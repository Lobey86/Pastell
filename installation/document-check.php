<?php
require_once( __DIR__ . "/../web/init.php");



foreach($objectInstancier->EntiteSQL->getAll() as $entite_info){
	foreach($objectInstancier->DocumentEntite->getAll($entite_info['id_e']) as $document_info){
		$documentType = get_document_type($document_info['type']);
		if (!$documentType->exists()){
			echo "Type de flux -- {$document_info['type']} -- inconnu : Entité #{$entite_info['id_e']} ({$entite_info['denomination']}) ; Document #{$document_info['id_d']}\n";
			continue;
		}		
		$tab_action = array_keys($documentType->getTabAction());
		if ( ! in_array($document_info['last_action'],$tab_action)){
			echo "Action -- {$document_info['last_action']} -- inconnu pour le flux {$document_info['type']} -- Entité #{$entite_info['id_e']} ({$entite_info['denomination']}) ; Document #{$document_info['id_d']}\n";
		}

	}
	
}
		


function get_document_type($type){
	global $objectInstancier;
	static $cache;
	if (! isset($cache[$type])){
		$cache[$type] = $objectInstancier->DocumentTypeFactory->getFluxDocumentType($type);
	}
	return $cache[$type];
}