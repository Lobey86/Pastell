
<h2>Liste des collectivités
<?php if ($this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"entite:edition",0)) : ?>
<p id="bloc_boutons">
<a href="entite/import.php" >
<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
Importer</a>
<a href="entite/edition.php" >
<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
Nouveau</a>
</p>
<?php endif;?>
</h2>
<div>
<form action='entite/detail.php' method='get' >
	<input type='text' name='search' value='<?php echo $search?>'/>
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php 
suivant_precedent($offset,20,$nbCollectivite,"entite/detail.php?search=$search");
?>
	<table class="tab_01">
		<tr>
			<th>Dénomination</th>
			<th>Siren</th>
			<th>Type</th>
		</tr>
	<?php foreach($liste_collectivite as $i => $info) : ?>
		<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
			<td><a href='entite/detail.php?id_e=<?php echo  $info['id_e'] ?>'><?php hecho($info['denomination']) ?></a></td>
			<td><?php 
			echo $info['siren'] ?></td>
			<td>
				<?php echo Entite::getNom($info['type']) ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>

