<?php

//Permet d'utiliser un fichier de configuration nn.yml
//afin de reconfigurer automatiquement une collectivité  
//Ne fonctionne que pour les TdT et iParapheur défini dans une collectivité !

require_once( dirname(__FILE__) . "/../web/init.php");

if (empty($argv[1])){
	echo "Usage : {$argv[0]} id_collectivite.yml\n";
	exit;
}

$file_config = $argv[1];

$id_e = basename($file_config,".yml");

$formulaire = new Formulaire(array());
$donneesFormulaire = new DonneesFormulaire($file_config,$formulaire);

$classification_cdg = $donneesFormulaire->get('classification_cdg');


if ($classification_cdg){
	$id_ce = createConnecteur($id_e,'actes-cdg','classification-cdg','classification-cdg');
	if ( $id_ce ){
		$connecteur_form = new DonneesFormulaire($objectInstancier->workspacePath ."/connecteur_{$id_ce}.yml",$formulaire);
		foreach($classification_cdg as $i => $file_name){
			$file_content = $donneesFormulaire->getFileContent('classification_cdg',$i);
			$connecteur_form->addFileFromData('classification_cdg', $file_name, $file_content);
			$a_jour = $donneesFormulaire->get("classification_a_jour_$i");
			$connecteur_form->setData("classification_a_jour_$i", $a_jour);
		}
	}
}

$tdt_url = $donneesFormulaire->get('tdt_url');

if ($tdt_url){
	$id_ce = createConnecteur($id_e,'actes','TdT','s2low');
	if ($id_ce){
		$connecteur_form = new DonneesFormulaire($objectInstancier->workspacePath ."/connecteur_{$id_ce}.yml",$formulaire);
		
		copy_field($donneesFormulaire,$connecteur_form,array("tdt_url"=>'url',"tdt_user_certificat_password"=>"user_certificat_password","nomemclature_file"));
		copy_file($donneesFormulaire,$connecteur_form,array("classification_file"));		
		copy_file($donneesFormulaire,$connecteur_form,array("tdt_user_certificat" => "user_certificat",
				"tdt_user_certificat_pem" => "user_certificat_pem", 
				"tdt_user_key_pem" => "user_key_pem",
				"tdt_server_certificate" => "server_certificate",		
		));		
		
	}	
}

$iparapheur_wsdl = $donneesFormulaire->get('iparapheur_wsdl');

if ($iparapheur_wsdl){
	$id_ce = createConnecteur($id_e,'actes','signature','iParapheur');
	if ($id_ce){
		$connecteur_form = new DonneesFormulaire($objectInstancier->workspacePath ."/connecteur_{$id_ce}.yml",$formulaire);
		copy_field($donneesFormulaire,$connecteur_form,array('iparapheur_activate','iparapheur_wsdl','iparapheur_user_certificat_password','iparapheur_login','iparapheur_password',));
		copy_file($donneesFormulaire,$connecteur_form,array('iparapheur_user_certificat','iparapheur_user_key_pem'));
	}
	
}

function copy_field(DonneesFormulaire $fromData, DonneesFormulaire $toData,array $keys){
	foreach($keys as $from_key => $to_key){
		if (is_int($from_key)){
			$from_key = $to_key;
		}
		$value = $fromData->get($from_key);
		$toData->setData($to_key,$value);
	}
}

function copy_file(DonneesFormulaire $fromData, DonneesFormulaire $toData,array $keys){
	foreach($keys as $from_key => $to_key){
		if (is_int($from_key)){
			$from_key = $to_key;
		}
		$all_file = $fromData->get($from_key);
		if (!is_array($all_file)){
			echo $all_file." inconnu\n\n";
			return;
		}
		foreach($all_file as $i => $file_name){
			$file_content = $fromData->getFileContent($from_key,$i);
			$toData->addFileFromData($to_key, $file_name, $file_content);
		}
	}
}




function createConnecteur($id_e,$flux,$type_connecteur,$id_connecteur){
	global $objectInstancier;
	if ($objectInstancier->FluxEntiteSQL->getConnecteur($id_e,$flux,$type_connecteur)){
		echo "Il existe déjà un connecteur $type_connecteur ... skip\n";
		return false;
	} 
	$id_ce = $objectInstancier->ConnecteurEntiteSQL->addConnecteur($id_e,$id_connecteur,$type_connecteur,$id_connecteur);
	echo "Création du connecteur $id_ce\n";
	$objectInstancier->FluxEntiteSQL->addConnecteur($id_e,$flux,$type_connecteur,$id_ce);
	echo "Ajout connecteur $id_ce sur le flux $flux\n";
	return $id_ce;
}
