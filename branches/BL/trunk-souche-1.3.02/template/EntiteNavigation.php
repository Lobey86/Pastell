<div class="box">
	<h2>Navigation dans les collectivités</h2>
	
	<table class="table table-striped table-hover table-condensed">
	
	<?php if ($navigation_entite_affiche_toutes) : ?>
		<tr>
		<td>
		<a href='<?php echo $navigation_url?>'>Toutes</a>
		</td>
		</tr>
	<?php endif;?>
	
	<?php foreach($navigation_all_ancetre as $ancetre) : ?>
		<tr>
		<td>
		<img src="img/commun/picto_decal.png" alt="" /><a href='<?php echo $navigation_url ?>&id_e=<?php echo $ancetre['id_e']?>'><?php echo $ancetre['denomination']?></a>
		</td>
		</tr>
	<?php endforeach; ?>
	
	<?php if ($navigation_denomination) : ?>
		<tr>
		<td>
			<img src="img/commun/picto_decal.png" alt="" /><b><?php echo $navigation_denomination ?></b>
		</td>
		</tr>		
	<?php endif;?>
	
	
	
	<?php foreach($navigation_liste_fille as $fille) : ?>
		<tr>
		<td>
			<img src="img/commun/picto_decal_niv2.png" alt="" /><a href='<?php echo $navigation_url ?>&id_e=<?php echo $fille['id_e'] ?>'>
				<?php hecho($fille['denomination']) ?>
			</a>
		</td>
		</tr>
	<?php endforeach;?>
	
	</table>
	
</div>