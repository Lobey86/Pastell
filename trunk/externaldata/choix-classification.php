<?php

class ClassificationAfficheur {
	
	private $link;
	
	public function __construct($link){
		$this->link = $link;
	}
	
	public function afficheClassification(SimpleXMLElement $xml,$niveau,$classif){

		$matiere = "Matiere$niveau";
		//$matiere1['CodeMatiere'];
		?>
		<ul>
		<?php foreach($xml->$matiere as $matiere1):
			$libelle = utf8_decode($matiere1['Libelle']);
		
		?>
			<li>
				<?php if ($niveau > 1) : ?>
					<a href='<?php echo $this->link?>&classif=<?php echo $classif . $matiere1['CodeMatiere'].' ' .$libelle?>'>
				<?php endif;?>
					<?php echo $libelle; ?>
				<?php if ($niveau > 1) : ?>
					</a>
				<?php endif;?>
				<?php $this->afficheClassification($matiere1,$niveau +1 , $classif . $matiere1['CodeMatiere']."."); ?>
			</li>
		<?php  endforeach; ?>
		</ul>
		<?php
	}

}

$donneesFormulaire = $donneesFormulaireFactory->get($id_e,$type);


$file = $donneesFormulaire->getFilePath('classification_file');


if (! file_exists($file)){
	$lastError->setLastError("La classfication en matière et sous-matière n'est pas disponible");
	header("Location: edition.php?id_d=$id_d&id_e=$id_e&page=$page");
	exit;
}

$classification = simplexml_load_file( $donneesFormulaire->getFilePath('classification_file'));
$namespaces = $classification->getNameSpaces(true);
$actes = $classification->children($namespaces['actes']); 

$classificationAfficheur = new ClassificationAfficheur("document/external-data-controler?id_e=$id_e&id_d=$id_d&page=$page&field=$field");

  
$page_title = "Choix de la classification en matière et sous matière";
include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">
<h2>Classification</h2>
Veuillez sélectionner une classification : 
<?php $classificationAfficheur->afficheClassification($actes->Matieres,1,""); ?>
</div>
<?php include( PASTELL_PATH ."/include/bas.php");
