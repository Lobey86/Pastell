<a class='btn btn-mini' href='mailsec/annuaire.php?id_e=<?php echo $id_e ?>'><i class='icon-circle-arrow-left'></i>Voir la liste des contacts</a>


<div class="box">
<h2>Liste des groupes basé sur des rôles  de <?php echo $infoEntite['denomination'] ?> </h2>

<form action='mailsec/operation-groupe-role.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />

<table class="table table-striped">
	<tr>
	
		<th>Nom</th>
		<th>Contact</th>
		<th>Partagé ?</th>
	</tr>
<?php foreach($listGroupe as $groupe) : 
	
	$utilisateur = $annuaireRole->getUtilisateur($groupe['id_r']);
	$nbUtilisateur = count($utilisateur); 
	$r = array();
	foreach($utilisateur as $u){
		$r[] = htmlentities("\"{$u['nom']} {$u['prenom']}\" <{$u['email']}>",ENT_QUOTES);
	}
	$utilisateur = implode(",<br/>",$r);
?>
	<tr>
		<td><input type='checkbox' name='id_r[]' value='<?php echo $groupe['id_r'] ?>'/>
			<?php echo $groupe['nom']?></td>
		<td><?php if ($nbUtilisateur) : ?>
				<?php echo $utilisateur;?>
			<?php else : ?>
				Ce groupe est vide
			<?php endif;?>	
		</td>
		<td>
			<?php echo $groupe['partage']?"OUI":"NON";?>	
		</td>
	</tr>
<?php endforeach;?>
	
</table>
<?php if ($can_edit) : ?>
<input type='submit' name='submit' class='btn btn-danger' value='Supprimer'/>
<input type='submit' name='submit' class='btn' value='Partager'/>
<input type='submit' class='btn btn-warning' name='submit' value='Enlever le partage'/>
<?php endif; ?>

</form>
</div>

<?php if ( $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) : ?>

<div class="box">
<h2>Ajouter un groupe</h2>
<form action='mailsec/add-groupe-role.php' method='post' >		
	<input type='hidden' name='id_e_owner' value='<?php echo $id_e ?>' />
	<table class="table table-striped">
			<tr>
				<th class="w200">Rôle</th>
				<td>
					<?php 
						$roleSQL = new RoleSQL($sqlQuery);
						$allRole = $roleSQL->getAllRole();
					?>
					<select name='role'>
						<option value=''>...</option>
						<?php foreach($allRole as $role ): ?>
							<option value='<?php echo $role['role']?>'> <?php echo $role['role'] ?> </option>
						<?php endforeach ; ?>
					</select>
				</td>
			</tr>
			<tr>
				<th>Collectivité ou service</th>
				<td>
				<select name='id_e'>
					<option value=''>...</option>
					<?php foreach($arbre as $entiteInfo): ?>
					<option value='<?php echo $entiteInfo['id_e']?>'>
						<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
						|_<?php echo $entiteInfo['denomination']?> </option>
					<?php endforeach ; ?>
				</select>
				</td>
			</tr>
	</table>
	<button type='submit' class='btn'><i class='icon-plus'></i>Ajouter</button>
</form>
</div>
<?php endif;?>

<?php if($groupe_herited) : ?>

<div class="box">
<h2>Liste des groupes hérités</h2>

<table  class="table table-striped">
	<tr>
		<th>Entité</th>
		<th>Nom</th>
		<th>Contact</th>
	</tr>
<?php foreach($groupe_herited as $groupe) : 
	$utilisateur = $annuaireRole->getUtilisateur($groupe['id_r']);
	$nbUtilisateur = count($utilisateur); 
	$r = array();
	foreach($utilisateur as $u){
		$r[] = htmlentities("\"{$u['nom']} {$u['prenom']}\" <{$u['email']}>",ENT_QUOTES);
	}
	$utilisateur = implode(",<br/>",$r);
?>
	<tr>
		<td><?php echo $groupe['denomination']?></td>
		<td>
			<?php echo $groupe['nom']?></td>
		<td><?php if ($nbUtilisateur) : ?>
				<?php echo $utilisateur;?>
			<?php else : ?>
				Ce groupe est vide
			<?php endif;?>	
	
	</tr>
<?php endforeach;?>
	
</table>
</div>

<?php endif;?>

