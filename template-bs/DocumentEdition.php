
<?php if ($info) : ?>
<a class='btn btn-mini' href='document/detail.php?id_d=<?php echo $id_d?>&id_e=<?php echo $id_e?>&page=<?php echo $page?>'><i class='icon-circle-arrow-left'></i><?php echo $info['titre']? $info['titre']:$info['id_d']?></a>
<?php else : ?>
<a class='btn btn-mini' href='document/list.php?type=<?php echo $type ?>&id_e=<?php echo $id_e?>'><i class='icon-circle-arrow-left'></i>Liste des documents <?php echo $documentType->getName($type);  ?></a>
<?php endif;?>


<?php 
	if ($formulaire->getNbPage() > 1 ) {
		$afficheurFormulaire->afficheStaticTab($page);
	}
?>

<div class="box">
<?php $afficheurFormulaire->affiche($page,"document/edition-controler.php",
			"document/recuperation-fichier.php?id_d=$id_d&id_e=$id_e",
			"document/supprimer-fichier.php?id_d=$id_d&id_e=$id_e&page=$page&action=$action",
			"document/external-data.php"
			); ?>
</div>
