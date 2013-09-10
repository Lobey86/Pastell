<?php 

class Extensions {
	
	const MODULE_FOLDER_NAME = "module";
	const CONNECTEUR_FOLDER_NAME = "connecteur";
	const MANIFEST_FILENAME = "manifest.yml";
	
	private $extensionSQL;
	private $yml_loader;
	
	public function __construct(ExtensionSQL $extensionSQL,YMLLoader $yml_loader){
		$this->extensionSQL = $extensionSQL;
		$this->yml_loader = $yml_loader;
	}
	
	public function getAll(){
		$extensions_list = array();
		foreach($this->extensionSQL->getAll() as $extension){
			$extensions_list[$extension['id_e']] = $this->getInfoFromPath($extension['path']); 
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
		return $result[$id_module_to_found];
	}
	
	public function getInfo($id_e){
		$info = $this->extensionSQL->getInfo($id_e);
		$info = $this->getInfoFromPath($info['path']);
		$info['manifest'] = $this->getManifest($info['path']);
		$info['id_e'] = $id_e;
		$info['exists'] = file_exists($info['path']);
		return $info;
	}
	
	public function getManifest($path){
		$manifest_path = "$path/".self::MANIFEST_FILENAME;
		if (! file_exists($manifest_path)){
			return false;
		}
		return $this->yml_loader->getArray($manifest_path);
	}
	
	private function getInfoFromPath($path){
		$result['path'] = $path; 
		$result['nom'] = basename($path);
		$result['flux'] = $this->getAllModuleByPath($path);
		$result['connecteur'] = $this->getAllConnecteurByPath($path);
		return $result;
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