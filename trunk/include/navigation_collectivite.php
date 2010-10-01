<?php


function navigation_racine($liste_collectivite,$url){
	?>
	<div class="box_contenu clearfix">
	<h2>Navigation dans les collectivités</h2>
	<b>Toutes les collectivités</b>
	<ul>
	
		<?php foreach($liste_collectivite as $col):
			global $sqlQuery;
			$entite = new Entite($sqlQuery,$col);
			$entiteInfo = $entite->getInfo();
		?>
			<li>&nbsp;<a href='<?php echo $url ?>&id_e=<?php echo $col ?>'><?php echo $entiteInfo['denomination']?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
	<?php 
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
			<li><?php for($j=0; $j<$i+1 ; $j++) echo "&nbsp;";?><b><?php echo $infoEntite['denomination']?></b></li>
			<?php endif;?>
		<?php foreach($entite->getFille() as $fille): ?>
			<li><?php for($j=0; $j<$i+2 ; $j++) echo "&nbsp";?><a href='<?php echo $url ?>&id_e=<?php echo $fille['id_e']?>'><?php echo $fille['denomination']?></a></li>
		<?php endforeach; ?>
	</ul>
</div>
	<?php 
}