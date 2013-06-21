<?php if ($id_e != 0) { ?>

<div class="box">
<form action='document/index.php' method='get' class="form-inline">
	<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
	<input type='text' name='search' value='<?php echo $search?>'/>
	<button type='submit' class='btn'><i class='icon-search'></i>Rechercher</button>
	<a style="margin-left:100px;" href='document/search.php?id_e=<?php echo $id_e?>'>Recherche avancée</a>
</form>



</div>

<?php

	$this->SuivantPrecedent($offset,$limit,$count,"document/index.php?id_e=$id_e&search=$search");
	$documentListAfficheur->affiche($listDocument,$id_e);

}

?>

<?php $this->render("EntiteNavigation")?>

<?php if ($id_e) : ?>
<a class='btn btn-mini' href='journal/index.php?id_e=<?php echo $id_e?>'><i class='icon-list'></i>Voir le journal des évènements</a>
<?php endif; ?>
