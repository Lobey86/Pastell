<?php 

class CreationDocument extends Connecteur {
	
	const MANIFEST_FILENAME = 'manifest.xml';
	
	private $objectInstancier;	
	
	/**
	 * @var RecuperationFichier
	 */
	private $connecteurRecuperation;
	private $mode_auto;

	public function __construct(ObjectInstancier $objectInstancier){
		$this->objectInstancier = $objectInstancier;
	}
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$id_ce = $donneesFormulaire->get("connecteur_recup_id");
		$this->connecteurRecuperation = $this->objectInstancier->ConnecteurFactory->getConnecteurById($id_ce);
		$this->mode_auto = $donneesFormulaire->get('connecteur_auto');
	}
	
	public function recupAllAuto($id_e){
		if (!$this->mode_auto){
			return array("Le mode automatique est désactivé");
		}
		return $this->recupAll($id_e);
	}
	
	public function recupAll($id_e){
		$result = array();
		foreach($this->connecteurRecuperation->listFile() as $file){
			if (in_array($file, array('.','..'))){
				continue;
			}
			$result[] = $this->recupFile($file,$id_e);
		}
		return $result;
	}
	
	private function recupFile($filename,$id_e){
		$result = "";
		$traitement = false;
		$result_csv = "";
		$tmpFolder = $this->objectInstancier->TmpFolder->create();
		try{
			if (substr($filename, -4) !== ".zip"){
				throw new Exception("$filename n'est pas un fichier zip");
			}
			$this->connecteurRecuperation->retrieveFile($filename, $tmpFolder);
			$zip = new ZipArchive();
			$handle = $zip->open($tmpFolder."/".$filename);
			if (!$handle){
				throw new Exception("Impossible d'ouvrir le fichier d'archive $filename");
			}
			$zip->extractTo($tmpFolder);
			$zip->close();
			$result .= "Traitement de l'archive $filename "."<br />\n";
			if (file_exists($tmpFolder."/".self::MANIFEST_FILENAME)){
				$result .= $this->recupFileThrow($tmpFolder,$id_e); //XML
				$result .= "<br />\n";
				$traitement = true;
			}
			$result_csv .= $this->recupFileCSV($tmpFolder,$id_e); //CSV
			if ($result_csv) {
				$result .= $result_csv;
				$traitement = true;
			}		
			if (!($traitement)) {
				$result .= "L'archive ne contient pas de fichier à traiter (self::MANIFEST_FILENAME ou fichier au format csv)<br />\n";
			}
			
		} catch (Exception $e){
			return "Erreur lors de l'importation : ".$e->getMessage();
		}
		
		$this->objectInstancier->TmpFolder->delete($tmpFolder);
		$this->connecteurRecuperation->deleteFile($filename);
		return $result;
	}
	
	private function recupFileThrow($tmpFolder,$id_e){

		$manifest_file = $tmpFolder."/".self::MANIFEST_FILENAME;
		if (! file_exists($manifest_file)){
			return "Le fichier ".self::MANIFEST_FILENAME." n'a pas été trouvé dans l'archive";
		}
		$xml = simplexml_load_file($manifest_file);
		if (! $xml){
			return "Le fichier ".self::MANIFEST_FILENAME." n'est pas lisible";
		}
		$pastell_type = strval($xml->attributes()->type);
		if (!$pastell_type){
			return "L'attribut 'type' n'a pas été trouvé dans le manifest";
		}
		
		if (!$this->objectInstancier->DocumentTypeFactory->isTypePresent($pastell_type)){
			return "Le type $pastell_type n'existe pas sur cette plateforme Pastell";
		}
		
		$new_id_d = $this->objectInstancier->Document->getNewId();
		$this->objectInstancier->Document->save($new_id_d,$pastell_type);
		$this->objectInstancier->DocumentEntite->addRole($new_id_d, $id_e, "editeur");
		
		$actionCreator = new ActionCreator($this->objectInstancier->SQLQuery,$this->objectInstancier->Journal,$new_id_d);
		$actionCreator->addAction($id_e,0,Action::CREATION,"Importation du document (récupération)");
		
		$donneesFormulaire = $this->objectInstancier->DonneesFormulaireFactory->get($new_id_d);
		
		foreach($xml->data as $data){
			$name = strval($data['name']);
			$value = strval($data['value']);
			if ($donneesFormulaire->fieldExists($name)){
				$donneesFormulaire->setData($name,$value);
			}
		}
		
		$titre_fieldname = $donneesFormulaire->getFormulaire()->getTitreField();
		$titre = $donneesFormulaire->get($titre_fieldname);
		$this->objectInstancier->Document->setTitre($new_id_d,$titre);

		foreach($xml->files as $files){
			$name = strval($files['name']);
			if (! $donneesFormulaire->fieldExists($name)){
				continue;
			}
			$file_num = 0;
			foreach($files->file as $file){
				$content = strval($file['content']);
				if (! file_exists($tmpFolder."/".$content)){
					continue;
				}
				$donneesFormulaire->addFileFromCopy($name,$content,$tmpFolder."/".$content,$file_num);
				$file_num++;
			}
		}	
		return "Création du document #ID $new_id_d - type : $pastell_type - $titre";
	}


	private function recupFileCSV($tmpFolder, $id_e){
		$result = "";
		$existe_csv = false;
		foreach(scandir($tmpFolder) as $file){
			if (substr($file, -4) == ".csv") {
				$existe_csv = true;
				$result .= "Traitement du fichier CSV $file "."<br />\n";
				$result .= $this->TraitementCSV($file, $tmpFolder,$id_e);
			}
		}		
		return $result;
	}
	
	private function TraitementCSV($file,$tmpFolder,$id_e){
		$result = "";
		$csv_file = $tmpFolder."/".$file;
		if (! file_exists($csv_file)){
			return "Le fichier ".$csv_file." n'a pas été trouvé dans le dossier";
		}		
		$handle = fopen($csv_file, "r");
		if (!$handle){
			return "Impossible d'ouvrir le fichier ".$csv_file;
		}
		
		$row = 1;
		$name_data = array();
		while (($ligne = fgetcsv($handle, 1000, ";")) !== FALSE) {
			if (isUTF8($csv_file)) {$ligne = utf8_decode_array($ligne);}			
			if ($row == 1) {
				// ligne en tete avec nom des champs
				$num = count($ligne);
				for ($c=0; $c < $num; $c++) {
					$name_data[] = $ligne[$c];
				}
			}
			else {
				// lignes documents à créer
				$result .= $this->TraitementLigneCSV($tmpFolder, $name_data, $ligne,$id_e). "<br />\n";
			}
			$row++;
		}
		fclose($handle);
				
		return $result;		
	}
	
	private function TraitementLigneCSV($tmpFolder, $name_data, $ligne_csv,$id_e){

		$erreur = "";
		$type_flux = $ligne_csv[0]; // premiere colonne: type de flux, ex: actes-generique
			
		if (!$this->objectInstancier->DocumentTypeFactory->isTypePresent($type_flux)){			
			$actionCreator = new ActionCreator($this->objectInstancier->SQLQuery,$this->objectInstancier->Journal,0);
			$actionCreator->addAction($id_e,0,Action::CREATION,"Importation par csv échec: Le type $type_flux n'existe pas");			
			return "Importation par csv échec: Le type $type_flux n'existe pas";
		}
			
		$new_id_d = $this->objectInstancier->Document->getNewId();
		$this->objectInstancier->Document->save($new_id_d,$type_flux);
		$this->objectInstancier->DocumentEntite->addRole($new_id_d, $id_e, "editeur");			
		$donneesFormulaire = $this->objectInstancier->DonneesFormulaireFactory->get($new_id_d);
		$actionCreator = new ActionCreator($this->objectInstancier->SQLQuery,$this->objectInstancier->Journal,$new_id_d);

		// Récupération de la description des champs name_data suivant le type de flux (formulaire)
		$num = count($name_data);
		for ($c=1; $c < $num; $c++) {
			if ($donneesFormulaire->getFormulaire()->getField($name_data[$c])) {
				$field[$c] = $donneesFormulaire->getFormulaire()->getField($name_data[$c]);
			}
		}	
		
		// Traitement de la ligne
		$num = count($ligne_csv);		
		$file_num = 0; // pour les fichiers multiples
		for ($c=1; $c < $num; $c++) {
			if ($field[$c]) {				
				switch ($field[$c]->getType()) {
    				case 'date':
						$date = preg_replace("#^(\d{2})/(\d{2})/(\d{4})$#",'$3-$2-$1',$ligne_csv[$c]);
						$donneesFormulaire->setData($field[$c]->getName(),$date);
						break;
    				case 'select':
						$select = $field[$c]->getSelect();
						foreach($select as $key => $value) {
							if ($ligne_csv[$c] == $value) {
								$donneesFormulaire->setData($field[$c]->getName(),$key);
								continue;
							}
						}
        				break;
    				case 'file':
    					if ($ligne_csv[$c]) {
    						$chemin_fic = $tmpFolder."/".utf8_encode_array($ligne_csv[$c]);
    						if (file_exists($chemin_fic)) {
    							$name_file = end(explode("/", $ligne_csv[$c]));
    							$donneesFormulaire->addFileFromCopy($field[$c]->getName(),$name_file,$chemin_fic, $file_num);
    							if ($field[$c]->isMultiple()) {$file_num++;} 							
    						}
    						else {$erreur .= "Le fichier $ligne_csv[$c] n'a pas été trouvé.";}    							
    					}
        				break;
    				default:
    					$donneesFormulaire->setData($field[$c]->getName(),$ligne_csv[$c]);
    					break;
				}
			}
		}		
		$titre_fieldname = $donneesFormulaire->getFormulaire()->getTitreField();
		$titre = $donneesFormulaire->get($titre_fieldname);
		$this->objectInstancier->Document->setTitre($new_id_d,$titre);

		if (! $donneesFormulaire->isValidable()){
			$erreur .= $donneesFormulaire->getLastError();
		}
		
		if ($erreur) {
			$actionCreator->addAction($id_e,0,Action::CREATION,"Importation par csv avec erreur: $erreur");
			return "Importation par csv avec erreur: document #ID $new_id_d - type : $type_flux - $titre - Erreur: $erreur";			
		}
		else {
			$actionCreator->addAction($id_e,0,Action::CREATION,"Importation par csv succès");
			return "Importation par csv succès: document #ID $new_id_d - type : $type_flux - $titre";
		}
	}		
}