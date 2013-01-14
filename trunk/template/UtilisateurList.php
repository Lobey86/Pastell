<h2>Liste des utilisateurs
<?php if ($this->droitEdition) : ?>
	<a href="utilisateur/edition.php?id_e=<?php echo $id_e?>" class='btn_add'>
		Nouveau
	</a>
<?php endif;?>
<?php echo $role_selected?" - $role_selected":""?>
</h2>

<div>
	<form action="entite/detail.php" method='get'>
		<input type='hidden' name='id_e' value='<?php echo $id_e?>'/>
		<input type='hidden' name='page' value='1'/>
	<table class='w500'>
		<tr>
		<td>Afficher les utilisateurs des entités filles</td>
		<td><input type='checkbox' name='descendance' <?php echo $descendance?"checked='checked'":""?>/><br/></td>
		</tr>
		<tr>
		<td>Rôle</td>
		<td><select name='role'>
		<option value=''>N'importe quel rôle</option>
			<?php foreach($all_role as $role ): ?>
				<option value='<?php echo $role['role']?>' <?php echo $role_selected==$role['role']?"selected='selected'":""?>> <?php echo $role['libelle'] ?> </option>
			<?php endforeach ; ?>
			</select>
		</td></tr>
		<tr>
		<td>
		Recherche </td><td><input type='text' name='search' value='<?php echo $search?>'/></td>
		</tr>
		<tr>
		<td></td><td>
		<input type='submit' value='Afficher'/>
		</td></tr>
		</table>
	</form>
	</div>
<br/>
<?php $this->SuivantPrecedent($offset,UtilisateurListe::NB_UTILISATEUR_DISPLAY,$nb_utilisateur,"entite/detail.php?id_e=$id_e&page=1&search=$search&descendance=$descendance&role_selected=$role_selected"); ?>


<table class='tab_02'>
<tr>
	<th>Prénom Nom</th>
	<th>login</th>
	<th>email</th>
	<th>Role</th>
	<?php if ($descendance) : ?>
		<th>Collectivité de base</th>
	<?php endif;?>
</tr>

<?php foreach($liste_utilisateur as $user) : ?>
	<tr>
		<td>
			<a href='utilisateur/detail.php?id_u=<?php echo $user['id_u'] ?>'>
				<?php echo $user['prenom']?> <?php echo $user['nom']?>
			</a>
		</td>
		<td><?php echo $user['login']?></td>
		<td><?php echo $user['email']?></td>
		<td>
			<?php foreach($user['all_role'] as $role): ?>
				<?php echo $role['libelle']?:"Aucun droit"; ?> - 
				<a href='entite/detail.php?id_e=<?php echo $role['id_e']?>'>
				<?php echo $role['denomination']?:"Entité racine"?>
				</a>
				<br/>
			<?php endforeach;?>
		
		</td>
		<?php if ($descendance) : ?>
			<td><a href='entite/detail.php?id_e=<?php echo $user['id_e']?>'><?php echo $user['denomination']?:"Entité racine"?></a></td>
		<?php endif;?>
	</tr>
<?php endforeach; ?>

</table>
<br/><br/>
<a href='utilisateur/export.php?id_e=<?php echo $id_e?>&descendance=<?php echo $descendance?>&role_selected=<?php echo $role_selected?>&search=<?php echo $search ?>'>Exporter (CSV)</a>
