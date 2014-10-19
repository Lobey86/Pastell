<?php
class Unoconv extends Connecteur {
	
	//cloudooo
	
	private $tmpFolder;
	
	private $unoconv_path;
	
	public function __construct(TmpFolder $tmpFolder){
		$this->tmpFolder = $tmpFolder;
	}
	
	public function setConnecteurConfig(DonneesFormulaire $donneesFormulaire){
		$this->unoconv_path = $donneesFormulaire->get('unoconv_path');
	}
	
	public function convertToPDF($source){
		if (! file_exists($source) || ! is_readable($source)){
			throw new Exception("Impossible de lire le fichier $source ");
		}
		$unoconv = "{$this->unoconv_path} -f pdf $source";
		
		exec($unoconv,$output,$return_var);
		if ($return_var != 0){
			throw new Exception("La commande de conversion ($unoconv) a échoué (code $return_var) : \n".implode("\n",$output));
		}
		
		$info = pathinfo($source);
		$new_filename = $info['filename'] . '.pdf' ;
		$new_filepath = dirname($source)."/".$new_filename;
		
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