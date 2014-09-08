
<table style='width:100%;'>
<tr>
<td>
<h2>Liste des collectivités</h2>
</td>
<?php if ($this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"entite:edition",0)) : ?>
<td class='align_right'>
<a href="entite/import.php" class='btn'>Importer</a>
<a href="entite/edition.php"  class='btn'>Nouveau</a>
</td>
<?php endif;?>
</tr>
</table>




<form action='entite/detail.php' method='get' class="form-inline">
	<input type='text' name='search' value='<?php echo $search?>'/>
	<button type='submit' class='btn'><i class='icon-search'></i>Rechercher</button>
</form>


<?php 
$this->SuivantPrecedent($offset,20,$nbCollectivite,"entite/detail.php?search=$search");
?>
<table class="table table-striped">
	<tr>
		<th class='w200'>Dénomination</th>
		<th>Siren</th>
		<th>Type</th>
		<th>Active</th>
	</tr>
<?php foreach($liste_collectivite as $i => $info) : ?>
	<tr>
		<td><a href='entite/detail.php?id_e=<?php echo  $info['id_e'] ?>'><?php hecho($info['denomination']) ?></a></td>
		<td><?php 
		echo $info['siren'] ?></td>
		<td>
			<?php echo Entite::getNom($info['type']) ?>
		</td>
		<td>
			<?php if(! $info['is_active']) :?>
			<b>Désactivé</b>
			<?php endif;?>
		</td>
	</tr>
<?php endforeach; ?>
</table>

