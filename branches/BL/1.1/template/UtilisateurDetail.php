<?php if ($this->RoleUtilisateur->hasDroit($info['id_u'],"entite:lecture",$info['id_e']) && $info['id_e']) : ?>
<a href='entite/detail.php?id_e=<?php echo $info['id_e'] ?>&page=1'>« Revenir à <?php echo $infoEntiteDeBase['denomination'] ?></a>
<?php endif; ?>

<br/><br/>

<div class="box_contenu clearfix">

<h2>Détail de l'utilisateur <?php echo $info['prenom']." " . $info['nom']?>
<?php if ($utilisateur_edition) : ?>
<a href="utilisateur/edition.php?id_u=<?php echo $id_u?>" class='btn_maj'>
		Modifier
	</a>
<?php endif;?>
</h2>

<table class='tab_04'>

<tr>
<th>Login</th>
<td><?php echo $info['login'] ?></td>
</tr>

<tr>
<th>Prénom</th>
<td><?php echo $info['prenom'] ?></td>
</tr>

<tr>
<th>Nom</th>
<td><?php echo $info['nom'] ?></td>
</tr>

<tr>
<th>Email</th>
<td><?php echo $info['email'] ?></td>
</tr>

<tr>
<th>Date d'inscription</th>
<td><?php echo time_iso_to_fr($info['date_inscription']) ?></td>
</tr>


<tr>
<th>Entité de base</th>
<td>
	<a href='entite/detail.php?id_e=<?php echo $info['id_e']?>'>
		<?php if ($info['id_e']) : ?>
			<?php echo $denominationEntiteDeBase ?>
		<?php else : ?>
			Utilisateur global
		<?php endif;?>
	</a> 
</td>
</tr>

<?php if ($certificat->isValid()) : ?>
<tr>
<th>Certificat</th>
<td><a href='utilisateur/certificat.php?verif_number=<?php echo $certificat->getVerifNumber() ?>'><?php echo $certificat->getFancy() ?></a></td>
</tr>
<?php endif;?>

<?php if ( $this->RoleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$info['id_e'])) : ?>
	<tr>
		<th>Dernières actions</th>
		<td>
		<a href='journal/index.php?id_u=<?php echo $id_u?>'>Dernières actions de <?php echo $info['prenom']." " . $info['nom']?> »</a>
		</td>
	</tr>
<?php endif;?>

</table>
</div>

<div class="box_contenu clearfix">
<h2>Rôle de l'utilisateur</h2>

<table class='tab_01'>
<tr>
<th>Rôle</th>
<th>Entité</th>
<th>&nbsp;</th>
</tr>

<?php foreach ($this->RoleUtilisateur->getRole($id_u) as $infoRole) : ?>
<tr>
	<td><?php echo $infoRole['role']?></td>
	<td>
		<?php if ($infoRole['id_e']) : ?>
			<a href='entite/detail.php?id_e=<?php echo $infoRole['id_e']?>'><?php echo $infoRole['denomination']?></a>
		<?php else : ?>
			Toutes les collectivités 
		<?php endif;?>
	</td> 
	<td>
		<?php if ($utilisateur_edition) : ?>
		<a href='utilisateur/supprimer-role.php?id_u=<?php echo $id_u ?>&role=<?php echo $infoRole['role']?>&id_e=<?php echo $infoRole['id_e']?>'>
			enlever ce rôle
		</a>
		<?php endif; ?>
	</td>
</tr>
<?php endforeach;?>
</table>


<?php if ($utilisateur_edition) :

$roleSQL = new RoleSQL($sqlQuery);
$allRole = $roleSQL->getAllRole();

?>
	<h3>Ajouter un rôle </h3>
	
	<form action='utilisateur/ajouter-role.php' method='post'>
		<input type='hidden' name='id_u' value='<?php echo $id_u ?>' />
	
		<select name='role'>
			<option value=''>...</option>
			<?php foreach($allRole as $role ): ?>
			<option value='<?php echo $role['role']?>'> <?php echo $role['role'] ?> </option>
			<?php endforeach ; ?>
		</select>
		
		<select name='id_e'>
			<option value=''>...</option>
			<?php foreach($arbre as $entiteInfo): ?>
			<option value='<?php echo $entiteInfo['id_e']?>'>
				<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
				|_<?php echo $entiteInfo['denomination']?> </option>
			<?php endforeach ; ?>
		</select>
		
		<input type='submit' value='ajouter'/>
	</form>
<?php endif; ?>
</div>

<div class="box_contenu clearfix">
<h2>Notification de l'utilisateur</h2>
<table class='tab_02'>
<tr>
<th>Entité</th>
<th>Type de document</th>
<th>Action</th>
<th>&nbsp;</th>
</tr>

<?php foreach ($notification->getAll($id_u) as $infoNotification) : ?>
<tr>
	<td>
		<?php if ($infoNotification['id_e']) : ?>
			<a href='entite/detail.php?id_e=<?php echo $infoNotification['id_e']?>'><?php echo $infoNotification['denomination']?></a>
		<?php else : ?>
			Toutes les collectivités 
		<?php endif;?>
	</td> 
	<td>
		<?php if($infoNotification['type']): ?>
			<?php 
			echo $this->DocumentTypeFactory->getFluxDocumentType($infoNotification['type'])->getName() ?>
		<?php else : ?>
			Tous
		<?php endif; ?>
	</td>
	<td>
		<?php if ($infoNotification['action']) : ?>
			<?php echo $infoNotification['action'] ?>
		<?php else : ?>
			Toutes
		<?php endif;?>
	</td>
	
	<td>
		<?php if ($utilisateur_edition) : ?>
			<a href='utilisateur/supprimer-notification.php?id_n=<?php echo $infoNotification['id_n'] ?>'>
				enlever cette notification
			</a>
		<?php endif;?>
	</td>
</tr>
<?php endforeach;?>
</table>
<?php if ($utilisateur_edition) : ?>
	<form action='utilisateur/ajouter-notification.php' method='post'>
		<input type='hidden' name='id_u' value='<?php echo $id_u ?>' />
		
		<select name='id_e'>
			<option value=''>...</option>
			<?php foreach($arbre as $entiteInfo): ?>
			<option value='<?php echo $entiteInfo['id_e']?>'>
				<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
				|_<?php echo $entiteInfo['denomination']?> </option>
			<?php endforeach ; ?>
		</select>
		<?php  $documentTypeHTML->displaySelectWithCollectivite(); ?>
			
		<input type='submit' value='ajouter'/>
	</form>
<?php endif;?>

</div>
