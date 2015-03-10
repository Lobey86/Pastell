<?php


require_once 'XML/RPC2/Client.php';

class Cloudooo  extends Connecteur {
	
	private $tmpFolder;
	
	private $cloudooo_hostname;
	private $cloudooo_port;
	
	public function __construct(TmpFolder $tmpFolder){
		$this->tmpFolder = $tmpFolder;
		
		
	}
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->cloudooo_hostname = $donneesFormulaire->get('cloudooo_hostname');
		$this->cloudooo_port = $donneesFormulaire->get('cloudooo_port');
	}
	
	public function convertToPDF($source){
		if (! include_once("XML/RPC2/Client.php")){
			throw new Exception("Le paquet PEAR XML_RPC2 n'a pas été trouvé");
		}
		if (! file_exists($source) || ! is_readable($source)){
			throw new Exception("Impossible de lire le fichier $source ");
		}
		
		$info = pathinfo($source);
		$new_filename = $info['filename'] . '.pdf' ;
		$new_filepath = dirname($source)."/".$new_filename;
		
		$dataaConvertir = file_get_contents($source);
		
		$dataExtention = pathinfo($source,PATHINFO_EXTENSION);
		$dataSortieExtention = "pdf";
		
		$options = array(
				'uglyStructHack' => true
		);
		
		$url = "http://{$this->cloudooo_hostname}:{$this->cloudooo_port}" ;
		$client = @ XML_RPC2_Client::create($url, $options);
		
		try {
			$result = $client->convertFile(base64_encode($dataaConvertir), $dataExtention, $dataSortieExtention, false, true);
		} catch (XML_RPC2_FaultException $e) {
			$this->log('Exception #' . $e->getFaultCode() . ' : ' . $e->getFaultString(), 'debug');
			return false;
		}
		
		file_put_contents($new_filepath, base64_decode($result));
		
		
		if (! file_exists($new_filepath)){
			throw new Exception("Le fichier « $new_filepath » n'a pas pu être créé");
		}
		
		return $new_filepath;
	}
	
	
	public function convertField(DonneesFormulaire $donneesFormulaire, $input_field_name,$output_field_name){
		$filename = $donneesFormulaire->getFileName($input_field_name);
		$file_path = $donneesFormulaire->getFilePath($input_field_name);
		
		$tmp_folder = $this->tmpFolder->create();
		$tmp_file_source = $tmp_folder."/".$filename; 
		copy($file_path, $tmp_file_source);
		$new_filepath = $this->convertToPDF($tmp_file_source);
		$new_filename = basename($new_filepath);
		
		$donneesFormulaire->addFileFromCopy($output_field_name,$new_filename,$new_filepath,0);
		
		$this->tmpFolder->delete($tmp_folder);
	}
	
	
}