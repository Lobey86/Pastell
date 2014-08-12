<a class='btn btn-mini' href='entite/detail.php?id_e=<?php echo $id_e ?>&page=5'><i class='icon-circle-arrow-left'></i>Administration de <?php echo $infoEntite['denomination']?></a>

<div class='box'>

<a class='btn btn-mini' href='mailsec/groupe-list.php?id_e=<?php echo $id_e ?>'><i class='icon-chevron-right'></i>Voir les groupes</a>
&nbsp;&nbsp;&nbsp;&nbsp;
<a class='btn btn-mini' href='mailsec/groupe-role-list.php?id_e=<?php echo $id_e ?>'><i class='icon-chevron-right'></i>Voir les groupes basés sur les rôles</a>

</div>

<div class="box">
<h2>liste des contacts de <?php echo $infoEntite['denomination'] ?> </h2>

<form action='mailsec/del-contact.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />

<table  class="table table-striped">
	<tr>
	
		<th>Description</th>
		<th>Email</th>
		
	</tr>
<?php foreach($listUtilisateur as $utilisateur) : ?>
	<tr>
		<td>
		<?php if ($can_edit) : ?>
		<input type='checkbox' name='email_list[]' value='<?php hecho($utilisateur['email']) ?>'/>
		<?php endif; ?>
		<?php echo $utilisateur['description']?></td>
		<td><?php echo $utilisateur['email']?></td>
	</tr>
<?php endforeach;?>
	
</table>

<?php if ($can_edit) : ?>
<input type='submit' class='btn btn-danger' value='Supprimer'/>
<?php endif; ?>
</form>

</div>

<?php if ( $this->RoleUtilisateur->hasDroit($this->Authentification->getId(),"annuaire:edition",$id_e)) : ?>

<div class="box">
<h2>Ajouter un contact</h2>
<form action='mailsec/add-contact.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	
	<table class="table table-striped">

			<tr>
				<th>Description</th>
				<td><input type='text' name='description' value='<?php echo $this->LastError->getLastInput('description') ?>' /></td>
			</tr>
			<tr>
				<th>Email</th>
				<td><input type='text' name='email' value='<?php echo $this->LastError->getLastInput('email') ?>'/></td>
			</tr>

	</table>
	<button type='submit' class='btn'><i class='icon-plus'></i>Ajouter</button>
</form>
</div>
<?php endif;?>
