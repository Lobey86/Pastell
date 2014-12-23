<?php if ($entite_info['id_e']) : ?>
	<a class='btn btn-mini' href='<?php echo "entite/detail.php?id_e={$entite_info['id_e']}" ?>'><i class='icon-circle-arrow-left'></i><?php hecho($entite_info['denomination']) ?></a>
<?php else : ?>
	<a class='btn btn-mini' href='entite/detail.php'><i class='icon-circle-arrow-left'></i><?php echo "Liste des collectivités" ?></a>
<?php endif;?>

<br/><br/>

<ul class="nav nav-pills">
	<?php foreach ($onglet_tab as $onglet_number => $onglet_name) : ?>
	<li <?php echo ($onglet_number == $page)?'class="active"':'' ?>>
		<a href='entite/import.php?page=<?php echo $onglet_number?>'>
			<?php echo $onglet_name?>
		</a>
	</li>
	<?php endforeach;?>
</ul>



<?php $this->render($template_onglet); ?>


