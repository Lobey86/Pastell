<?php

class ClassificationActes {
	
	private $filePath;
	private $link;
	
	public function __construct($filePath){
		$this->filePath = $filePath;
	}
	
	public function getActes(){
		$classification = simplexml_load_file( $this->filePath );
		$namespaces = $classification->getNameSpaces(true);
		return $classification->children($namespaces['actes']); 
	}

	public function getInfo($classification){
		$actes = $this->getActes();
		$result = $this->getInfoRec(".".$classification,$actes->Matieres,1,"");
		if ($result){
			return "$classification $result";
		}
		return false;
	}
	
	public function getInfoRec($cherche,$element,$niveau,$debut){
		$matiere = "Matiere$niveau";
		foreach($element->$matiere as $matiere1){
			$code = $debut.".".$matiere1['CodeMatiere'];
			if ($code == $cherche){
				return utf8_decode($matiere1['Libelle']);
			}
			
			$result =  $this->getInfoRec($cherche,$matiere1,$niveau+1,$code);
			if ($result){
				return $result;
			}
		}
		return false;
	}
	
	public function affiche($link){
		$this->link = $link;
		$actes = $this->getActes();
		$this->afficheInternal($actes->Matieres,1,'');
	}
	
	public function afficheInternal(SimpleXMLElement $xml,$niveau,$classif){
		$matiere = "Matiere$niveau";
		//$matiere1['CodeMatiere'];
		?>
		<ul>
		<?php foreach($xml->$matiere as $matiere1):
			$libelle = utf8_decode($matiere1['Libelle']);
		
		?>
			<li>
				<?php if ($niveau > 1) : ?>
					<a href='<?php echo $this->link?>&classif=<?php echo $classif . $matiere1['CodeMatiere'].' ' .urlencode($libelle)?>'>
				<?php endif;?>
					<?php echo $libelle; ?>
				<?php if ($niveau > 1) : ?>
					</a>
				<?php endif;?>
				<?php $this->afficheInternal($matiere1,$niveau +1 , $classif . $matiere1['CodeMatiere']."."); ?>
			</li>
		<?php  endforeach; ?>
		</ul>
		<?php
	}
}