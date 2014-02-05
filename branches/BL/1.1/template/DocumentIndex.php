<?php if ($id_e != 0) { ?>
<div>
<form action='document/index.php' method='get' >
	<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
	<input type='text' name='search' value='<?php echo $search?>'/>
	<input type='submit' value='Rechercher' />
</form>
<p class='petit'><a href='document/search.php?id_e=<?php echo $id_e?>'>Recherche avancée</a></p>
</div>
<?php

	$this->SuivantPrecedent($offset,$limit,$count,"document/index.php?id_e=$id_e&search=$search");
	$documentListAfficheur->affiche($listDocument,$id_e);

}

?>

<?php $this->render("EntiteNavigation")?>

<?php if ($id_e) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>'>Voir le journal des évènements</a>
<br/><br/>
<?php endif; ?>
