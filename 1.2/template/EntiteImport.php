<?php if ($entite_info['id_e']) : ?>
	<div class='lien_retour'>
		<a href='<?php echo "entite/detail.php?id_e={$entite_info['id_e']}" ?>'>« <?php hecho($entite_info['denomination']) ?></a>
	</div>
<?php else : ?>
	<div class='lien_retour'>
		<a href='entite/detail.php'>« <?php echo "Liste des collectivités" ?></a>
	</div>
<?php endif;?>

<div id="bloc_onglet">
	<?php foreach ($onglet_tab as $onglet_number => $onglet_name) : ?>
		<a href='entite/import.php?page=<?php echo $onglet_number?>' <?php echo ($onglet_number == $page)?'class="onglet_on"':'' ?>>
			<?php echo $onglet_name?>
		</a>
	<?php endforeach;?>
</div>
<?php $this->render($template_onglet); ?>


