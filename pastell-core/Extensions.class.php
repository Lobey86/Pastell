<?php 

class Extensions {
	
	const MODULE_FOLDER_NAME = "module";
	const CONNECTEUR_FOLDER_NAME = "connecteur";
	const MANIFEST_FILENAME = "manifest.yml";
	
	private $extensionSQL;
	private $yml_loader;
	private $pastellManifestReader;
	
	public function __construct(ExtensionSQL $extensionSQL,YMLLoader $yml_loader, ManifestReader $pastellManifestReader){
		$this->extensionSQL = $extensionSQL;
		$this->yml_loader = $yml_loader;
		$this->pastellManifestReader = $pastellManifestReader;
	}
	
	public function getAll(){
		$extensions_list = array();
		foreach($this->extensionSQL->getAll() as $extension){
			$extensions_list[$extension['id_e']] = $this->getInfo($extension['id_e']); 
		}
		return $extensions_list;
	}
	
	public function getAllConnecteur(){
		static $result;
		if ($result){
			return $result;
		}
		
		$result = array();
		foreach($this->getAllExtensionsPath() as $search){
			foreach($this->getAllConnecteurByPath($search) as $id_connecteur){
				$result[$id_connecteur] = $search."/".self::CONNECTEUR_FOLDER_NAME."/$id_connecteur";
			}
		}
		return $result;
	}
	
	public function getConnecteurPath($id_connecteur){
		$result = $this->getAllConnecteur();
		return $result[$id_connecteur];
	}
	
	
	private function getAllExtensionsPath(){
		static $to_search;
		if ($to_search){
			return $to_search;
		}
		$to_search = array(PASTELL_PATH);
		foreach($this->extensionSQL->getAll() as $extension){
			$to_search[] = $extension['path'];
		}
		return $to_search;
	}
	
	public function getAllModule(){
		static $result;
		if ($result){
			return $result;
		}
		
		$result = array();
		foreach($this->getAllExtensionsPath() as $search){
			foreach($this->getAllModuleByPath($search) as $id_module){
				$result[$id_module] = $search."/".self::MODULE_FOLDER_NAME."/$id_module";
			}
		}
		return $result;
	}
	
	public function getModulePath($id_module_to_found){
		$result = $this->getAllModule();
		if (empty($result[$id_module_to_found])){
			return false;
		}
		return $result[$id_module_to_found];
	}
	
	public function getInfo($id_e){
		$info = $this->extensionSQL->getInfo($id_e);
		$info = $this->getInfoFromPath($info['path']);
		$info['error'] = false;
		$info['warning'] = false;
		
		$info['id_e'] = $id_e;
		if (! file_exists($info['path'])){
			$info['error'] = "Extension non-trouvé";
			$info['error-detail'] = "L'emplacement {$info['path']} n'a pas été trouvé sur le système de fichier";
		} else if (! $info['manifest']['nom']){
			$info['warning'] = "manifest.yml absent";
			$info['warning-detail'] = "Le fichier manifest.yml n'a pas été trouvé dans {$info['path']}";	
		} else if (! $this->pastellManifestReader->isRevisionOK($info['manifest']['pastell-version'])) {
			$version = $this->pastellManifestReader->getVersion();
			$info['warning'] = "Version de pastell incorrecte";
			$info['warning-detail'] = "Ce module attent une version de Pastell ({$info['manifest']['pastell-version']}) non prise en charge par ce Pastell";
		}
		return $info;
	}
	
	private function getInfoFromPath($path){
		$result['path'] = $path; 
		$result['nom'] = basename($path);
		$result['flux'] = $this->getAllModuleByPath($path);
		$result['connecteur'] = $this->getAllConnecteurByPath($path);
		$result['manifest'] = $this->getManifest($path);
		return $result;
	}
	
	private function getManifest($path){
		$manifestReader = new ManifestReader(new YMLLoader(), "$path/".self::MANIFEST_FILENAME);
		return $manifestReader->getInfo();
	}
	
	private function getAllModuleByPath($path){
		return $this->globAll($path."/".self::MODULE_FOLDER_NAME."/*");
	}
	
	private function getAllConnecteurByPath($path){
		return $this->globAll($path."/".self::CONNECTEUR_FOLDER_NAME."/*");
	}
	
	private function globAll($glob_expression){
		$result = array();
		foreach (glob($glob_expression) as $file_config){			
			$result[] =  basename($file_config);
		}
		return $result;
	}	
}