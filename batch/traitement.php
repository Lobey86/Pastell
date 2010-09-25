<?php

//TODO Ca ne doit pas servir à grand chose ...

require_once(dirname(__FILE__)."/../web/init.php");
require_once( PASTELL_PATH . "/lib/Workflow.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");


$workflow = new Workflow($sqlQuery);

function pas_de_traitement($id_t,$begin_state,$end_state){
	echo "Aucun traitement pour la transaction $id_t ($begin_state -> $end_state)\n";
	return $end_state;
}

function send_cdg($id_t,$begin_state,$end_state){
	//ARG...
	global $sqlQuery;
	
	$transaction = new TransactionSQL($sqlQuery,$id_t);
	$entiteListe = new EntiteListe($sqlQuery);
	$cdg = $entiteListe->getAll(Entite::TYPE_CENTRE_DE_GESTION);
	//TODO ! 
	$siren_cdg = $cdg[0]['siren'];
	
	$transaction->addRole($siren_cdg,"destinataire");
	return $end_state;
	
}
/*$workflow->doStep(FluxArrete::TYPE,FluxArrete::STATE_SEND_TDT_ACK,FluxArrete::STATE_SEND_CDG,"send_cdg");
$workflow->doStep(FluxArrete::TYPE,FluxArrete::STATE_SEND_TDT,FluxArrete::STATE_SEND_TDT_ACK,"pas_de_traitement");
$workflow->doStep(FluxArrete::TYPE,Flux::STATE_POSTE,FluxArrete::STATE_SEND_TDT,"pas_de_traitement");
*/
