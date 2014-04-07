
<?php if ($info) : ?>
<a href='document/detail.php?id_d=<?php echo $id_d?>&id_e=<?php echo $id_e?>&page=<?php echo $page?>'>« <?php echo $info['titre']? $info['titre']:$info['id_d']?></a>
<?php else : ?>
<a href='document/list.php?type=<?php echo $type ?>&id_e=<?php echo $id_e?>'>« Liste des documents <?php echo $documentType->getName($type);  ?></a>
<?php endif;?>
<br/><br/>

<?php 
	if ($formulaire->getNbPage() > 1 ) {
		$afficheurFormulaire->afficheStaticTab($page);
	}
?>

<div class="box_contenu clearfix">
<?php $afficheurFormulaire->affiche($page,"document/edition-controler.php",
			"document/recuperation-fichier.php?id_d=$id_d&id_e=$id_e",
			"document/supprimer-fichier.php?id_d=$id_d&id_e=$id_e&page=$page&action=$action",
			"document/external-data.php"
			); ?>
</div>
