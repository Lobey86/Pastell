<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH . "/lib/document/DocumentListAfficheur.class.php");
require_once (PASTELL_PATH . "/lib/entite/NavigationEntite.class.php");
require_once( PASTELL_PATH . "/lib/api/CSVoutput.class.php");


$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->get('id_e',0);
$type = $recuperateur->get('type');
$search = $recuperateur->get('search');

$lastEtat = $recuperateur->get('lastetat');
$last_state_begin = $recuperateur->get('last_state_begin');
$last_state_end = $recuperateur->get('last_state_end');

$last_state_begin_iso = getDateIso($last_state_begin);
$last_state_end_iso = getDateIso($last_state_end);

$etatTransit = $recuperateur->get('etatTransit');
$state_begin =  $recuperateur->get('state_begin');
$state_end =  $recuperateur->get('state_end');
$tri =  $recuperateur->get('tri');

$offset = 0;
$documentActionEntite = new DocumentActionEntite($sqlQuery);

$limit = $documentActionEntite->getNbDocumentBySearch($id_e,$type,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso);

$listDocument = $documentActionEntite->getListBySearch($id_e,$type,$offset,$limit,$search,$lastEtat,$last_state_begin_iso,$last_state_end_iso,$tri);	

foreach($listDocument as $i => $doc){
	$result[] = array(
					$doc['denomination'],
					$doc['id_d'],
					$doc['titre'],
					$doc['last_action'],
					$doc['last_action_date'],
					
					);	
}
$CSVoutput = new CSVoutput();
$CSVoutput->sendAttachment("pastell-export-$id_e-$type-$search-$lastEtat-$tri.csv",$result);