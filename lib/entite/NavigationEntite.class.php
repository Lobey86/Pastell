<?php


class NavigationEntite {
	
	private $id_e ;
	private $listeCollectivite;
	
	public function __construct($id_e, array $listeCollectivite){
		$this->id_e = $id_e;
		$this->listeCollectivite = $listeCollectivite;
	}
	
	private function getAncetre(){
		global $sqlQuery;
		$entite = new Entite($sqlQuery,$this->id_e);
		
		$ancetre = $entite->getAncetre();
		
		if (in_array(0,$this->listeCollectivite)){
			return $ancetre;
		}
		
		$allParent = array();
		foreach($ancetre as $parent){
			$allParent[] = $parent['id_e'];
		}
		foreach($allParent as $id_e){
			if (! in_array($id_e,$this->listeCollectivite)){
				array_shift($ancetre);
			} else {
				return $ancetre;
			}
		}
		return $ancetre;
	}
	
	public function affiche($url){
		
		global $sqlQuery;
		$entite = new Entite($sqlQuery,$this->id_e);
		$infoEntite = $entite->getInfo();
		
		if ($this->id_e != 0 && count($this->listeCollectivite) == 1 && $this->listeCollectivite[0] != 0 ){
			if (! $entite->getFille()){
				if (! $this->getAncetre()){
					return;
				}
			}
		}
		
		
	?>
		<div class="box_contenu clearfix">
			<h2>Navigation dans les collectivités</h2>
			<?php if ($this->id_e != 0 && (count($this->listeCollectivite) > 1 || $this->listeCollectivite[0] == 0)) : ?>
				<a href='<?php echo $url?>'>Toutes</a>
			<?php endif;?>
			<?php foreach($this->getAncetre() as $i => $mere) : ?>
				&gt; <a href='<?php echo $url ?>&id_e=<?php echo $mere['id_e']?>'><?php echo $mere['denomination']?></a>
			<?php endforeach; ?>
				<?php if ($infoEntite['denomination']) : ?>
				&gt; <b><?php echo $infoEntite['denomination']?></b>
				<?php endif;?>
			<ul>
				
				<?php if ($this->id_e != 0 || ($this->listeCollectivite[0] == 0)) :?>
					<?php foreach($entite->getFille() as $fille): ?>
						<li>
							<a href='<?php echo $url ?>&id_e=<?php echo $fille['id_e']?>'>
								<?php echo $fille['denomination']?>
							</a>
						</li>
					<?php endforeach; ?>
				<?php else : ?>
					<?php foreach($this->listeCollectivite as $fille2):
						$entite2 = new Entite($sqlQuery,$fille2);
						$fille = $entite2->getInfo();
					?>
						<li>
							<a href='<?php echo $url ?>&id_e=<?php echo $fille['id_e']?>'>
								<?php echo $fille['denomination']?>
							</a>
						</li>
					<?php endforeach;?>
				<?php endif;?>
				
				
			</ul>
		</div>
	<?php 
	}
	
	
				/*if (($id_e == 0) && ! $roleUtilisateur->hasDroit($authentification->getId(),"$type:lecture",$id_e) ){
					navigation_racine($liste_collectivite,"document/list.php?type=$type");
				} else {
					navigation_collectivite($entite,"document/list.php?type=$type");
				}*/

function navigation_racine($liste_collectivite,$url){
	if (array(count($liste_collectivite) > 1)) : 
	?>
	<ul>
	
		<?php foreach($liste_collectivite as $col):
			global $sqlQuery;
			$entite = new Entite($sqlQuery,$col);
			$entiteInfo = $entite->getInfo();
		?>
			<li>&nbsp;<a href='<?php echo $url ?>&id_e=<?php echo $col ?>'><?php echo $entiteInfo['denomination']?></a></li>
		<?php endforeach; ?>
	</ul>
	<?php 
	endif;
}

function navigation_collectivite(Entite $entite,$url){
		$infoEntite = $entite->getInfo();
		$i = 0;
		
	?>
<div class="box_contenu clearfix">
	<h2>Navigation dans les collectivités</h2>
	<ul>
		<li>
		<?php if ($entite->exists() ) : ?>
			<a href='<?php echo $url ?>'>
		<?php else : ?>
			<b>	
		<?php endif;?>
		Toutes les collectivités
		<?php if ( ! $entite->exists()) : ?>
			</b>
		<?php else :?>
			</a>
		<?php endif;?>
		</li>
		<?php foreach($entite->getAncetre() as $i => $mere) : ?>
		<li><?php for($j=0; $j<$i ; $j++) echo "&nbsp";?><a href='<?php echo $url ?>&id_e=<?php echo $mere['id_e']?>'><?php echo $mere['denomination']?></a></li>
		<?php endforeach; ?>
			<?php if ($entite->exists()) : ?>
			<li><?php for($j=0; $j<$i+1 ; $j++) echo "&nbsp;";?></li>
			<?php endif;?>
		<?php foreach($entite->getFille() as $fille): ?>
			<li><?php for($j=0; $j<$i+2 ; $j++) echo "&nbsp";?><a href='<?php echo $url ?>&id_e=<?php echo $fille['id_e']?>'><?php echo $fille['denomination']?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
	<?php 
}
	
}