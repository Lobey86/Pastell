<?php

if ( $recuperateur->get('suivant') || $recuperateur->get('precedent')){
	return;
}

if (! $donneesFormulaire->get('envoi_iparapheur')){
	return;
}

$formulaire = $documentType->getFormulaire();
$formulaire->addDonnesFormulaire($donneesFormulaire);

foreach ($formulaire->getTab() as $page_num => $name) {
	//echo "$name";
	if ($name == 'iParapheur'){
		header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page_num");
		exit;
	}
}

