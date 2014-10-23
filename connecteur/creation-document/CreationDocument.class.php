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
		$tmpFolder = $this->objectInstancier->TmpFolder->create();
		try{
			$result = $this->recupFileThrow($filename, $tmpFolder,$id_e);
		} catch (Exception $e){
			return "Erreur lors de l'importation : ".$e->getMessage();
		}
		$this->objectInstancier->TmpFolder->delete($tmpFolder);
		
		$this->connecteurRecuperation->deleteFile($filename);
		return $result;
	}
	
	private function recupFileThrow($filename,$tmpFolder,$id_e){
		if (substr($filename, -4) !== ".zip"){		
			throw new Exception("$filename n'est pas un fichier zip");
		}
		$this->connecteurRecuperation->retrieveFile($filename, $tmpFolder);
		$zip = new ZipArchive();
		$handle = $zip->open($tmpFolder."/".$filename);
		if (!$handle){
			throw new Exception("Impossible d'ouvrir le fichier d'archive");
		}
		$zip->extractTo($tmpFolder);
		$zip->close();
		$manifest_file = $tmpFolder."/".self::MANIFEST_FILENAME;
		if (! file_exists($manifest_file)){
			throw new Exception("Le fihcier ".self::MANIFEST_FILENAME." n'a pas été trouvé dans l'archive");
		}
		$xml = simplexml_load_file($manifest_file);
		if (! $xml){
			throw new Exception("Le fichier ".self::MANIFEST_FILENAME." n'est pas lisible");
		}
		$pastell_type = strval($xml->attributes()->type);
		if (!$pastell_type){
			throw new Exception("L'attribut 'type' n'a pas été trouvé dans le manifest");
		}
		
		if (!$this->objectInstancier->DocumentTypeFactory->isTypePresent($pastell_type)){
			throw new Exception("Le type $pastell_type n'existe pas sur cette plateforme Pastell");
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
	
	
}