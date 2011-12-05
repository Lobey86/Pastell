<?php

class TypeActes {
	
	private $fileName;
	
	public function __construct($fileName){
		$this->fileName = $fileName;
	}
	
	public function getData($fileName){		
		static $type;
		
		if ($type){
			return $type;
		}
		
		$file_handle = fopen($fileName,"r");
		
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

	private function getArbre($type){
		$result = array();
		foreach($type as $element){
			$code_interne = explode(".",$element['code_interne']);
			$ptr = & $result;
			foreach($code_interne as $number){
				$ptr = & $ptr['fils'][$number];
			}
			$ptr = $element;
		}
		return $result;
	}
	
	public function afficheSousArbre($url,$sousArbre){
		?>
			<li>
				<?php if (strlen($sousArbre['code_interne']) <= 2):?>
					<b ><?php echo $sousArbre['code_interne'] . "&nbsp;-&nbsp;" . $sousArbre['nom']; ?></b>
				<?php else : ?>
					<a href='<?php echo $url?>&classif=<?php echo $sousArbre['code_interne'] ?>'>
						<?php echo $sousArbre['code_interne'] . "&nbsp;-&nbsp;" .$sousArbre['nom']; ?>
					</a>
				<?php endif;?>		
			<?php if (isset($sousArbre['fils'])):?>
				<ul>
					<?php foreach($sousArbre['fils'] as $element):?>
						<?php $this->afficheSousArbre($url,$element)?>
					<?php endforeach;?>
				</ul>
			<?php endif;?>
			</li>	
		<?php 		
	}
	
	public function afficheClassification($url){
		$tab = $this->getData($this->fileName);
		$arbre = $this->getArbre($tab);
		?>
		 <script>
		  $(document).ready(function(){
		    $("#classification").treeview( {collapsed: true,animated: "fast",control: "#container"});
		  });
 		 </script>
 		 <div id='container'>
 		 	<a href='#'>Tout replier</a>
			<a href='#'>Tout déplier</a>
		</div>
		<ul  id="classification" class="filetree">
		<?php foreach($arbre['fils'] as $element):?>		
			<?php $this->afficheSousArbre($url,$element); ?>
		<?php  endforeach; ?>
		</ul>
		<?php 
	}
	
	public function getInfo($classif){
		$tab = $this->getData($this->fileName);
		return $tab[$classif];
	}
}