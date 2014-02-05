
<?php if ($id_e && $has_many_collectivite) : ?>
<a href='entite/detail.php'>« Administration</a>
<?php endif; ?>
<br/><br/>

<div id="bloc_onglet">
	<?php foreach ($formulaire_tab as $page_num => $name) : ?>
		<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_number)?'class="onglet_on"':'' ?>>
			<?php echo $name?>
		</a>
	<?php endforeach;?>
</div>
<div class="box_contenu clearfix">

<?php if($tab_number!=5):?>
<?php $this->render($tableau_milieu)?>
<?php else:?>
<a href='mailsec/annuaire.php?id_e=<?php echo $id_e?>'>Annuaire »</a>	
<?php endif;?>


</div>

