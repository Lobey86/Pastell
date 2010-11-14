<?php

class TypeActes {
	
	private $fileName;
	
	public function __construct($fileName){
		$this->fileName = $fileName;
	}
	
	private function getData(){		
		static $type;
		
		if ($type){
			return $type;
		}
		
		$file_handle = fopen($this->fileName,"r");
		
		$type = array();
		while( $ligne = fgetcsv($file_handle)){
			if ($ligne[2] == 0 ){
				continue;
			}
			
			$type[$ligne[2]] = array( 'nom' =>  trim($ligne[0]),
									'code_interne' => trim($ligne[2]),
									'code_actes' => trim($ligne[3]),
									'transmission_actes' => ($ligne[4] == 'oui'),
									'transmission_cdg' => ($ligne[5] == 'oui'),
									'archivage' => ($ligne[6] == 'oui')
			);
		}
		return $type;
	}

	public function afficheClassification($url){
		$tab = $this->getData();?>
		<br/>
		<?php foreach($tab as $classif => $matiere): ?>		
			<?php if (strlen($classif) < 2) : ?><br/><?php endif;?>	
			<?php for($i=0; $i<strlen($classif);$i++) : ?>&nbsp;&nbsp;<?php endfor;?>
			<?php if (strlen($classif) <2 ) : ?>
				<b><?php echo $matiere['nom']; ?></b>
			<?php else :?>
				<a href='<?php echo $url ?>&classif=<?php echo$classif ?>'><?php echo $matiere['nom']; ?></a>
			<?php endif;?>		
			<br/>
		<?php  endforeach; 
	}
	
	public function getInfo($classif){
		$tab = $this->getData();
		return $tab[$classif];
	}
}