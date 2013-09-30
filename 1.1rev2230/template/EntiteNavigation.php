<div class="box_contenu clearfix">
	<h2>Navigation dans les collectivités</h2>
	<?php if ($navigation_entite_affiche_toutes) : ?>
		<a href='<?php echo $navigation_url?>'>Toutes</a>
	<?php endif;?>
	<?php foreach($navigation_all_ancetre as $ancetre) : ?>
		&gt; <a href='<?php echo $navigation_url ?>&id_e=<?php echo $ancetre['id_e']?>'><?php echo $ancetre['denomination']?></a>
	<?php endforeach; ?>
	<?php if ($navigation_denomination) : ?>
		&gt; <b><?php echo $navigation_denomination ?></b>
	<?php endif;?>
	<ul>
	<?php foreach($navigation_liste_fille as $fille) : ?>
		<li>
			<a href='<?php echo $navigation_url ?>&id_e=<?php echo $fille['id_e'] ?>'>
				<?php hecho($fille['denomination']) ?>
			</a>
		</li>
	<?php endforeach;?>
	</ul>
</div>