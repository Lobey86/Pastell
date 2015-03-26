<?php

/*
 * Ce script permet de corriger un poblème apparu lors d'une migration après passage de deux fois le script de mis à jour des champs (type->nomemclature)
 * L'ancienne version du script si elle était passé deux fois ne effecait le champs cible (ici nomemclature)
 * 
 * Le présent script permet de restaurer les lignes effacés à partir d'un backup.
 * 
 * L'utilisation de YAML est obligatoire car certaines valeurs sont sur plusieurs ligne et il n'y a pas de moyen simple de s'en sortir avec de simple regex.
 * 
 * 
 */

require_once(__DIR__."/../../ext/spyc.php");

$workspace = "/Users/eric/Desktop/cdg85_yml/aprescript/workspacecorrompu/";

$backup = "/Users/eric/Desktop/cdg85_yml/backup/workspace/";


foreach(glob("$workspace/*/*/*.yml") as $file_path){
	
	$filename = basename($file_path); 
	
	$yaml = spyc_load_file($file_path);
		
	if (! $yaml['nomemclature']){
		echo "(workspace)$filename : ne contient pas de ligne nomemclature : skip\n";
		continue;
	}
	
	if ($yaml['nomemclature'] != '\"\"'){
		echo "(workspace)$filename : contient déjà une nomemclature : skip\n";
		continue;
	}
	
	$backup_file_path = $backup;
	$backup_file_path .= basename(dirname(dirname($file_path))) ."/";
	$backup_file_path .= basename(dirname($file_path)) ."/";
	$backup_file_path .= $filename;
	
	
	$backup_yaml = spyc_load_file($backup_file_path);
	
	if (empty($backup_yaml['type'])){
		echo "(backup)$filename : ne contient pas de ligne type ou type vide : skip\n";
		continue;
	}
	
	$yaml['nomemclature'] = '\"'.$backup_yaml['type'].'\"';
	
	$dump = Spyc::YAMLDump($yaml);
	
	file_put_contents($file_path, $dump);
	echo "Remplacement sur le fichier $filename : OK\n";
	
}

		
		
		

