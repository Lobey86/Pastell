<?php $i = 0; ?>

<a class='btn btn-mini' href='role/index.php'><i class='icon-circle-arrow-left'></i>Revenir à la liste des rôles</a>


<div class="box">

<h2>Liste des droits - <?php  hecho($role_info['libelle']) ?></h2>
<a class='btn btn-mini' href='role/edition.php?role=<?php hecho($role) ?>'><i class='icon-edit'></i>Modifier le libellé</a>

<br/><br/>

<form action='role/detail-controler.php' method='post'>
	<table class="table table-striped table-hover">
		<tr>
			<th>Droits</th>
		</tr>
		<?php foreach($all_droit_utilisateur as $droit => $ok) : ?>
			<tr>
				<td>
					<?php if ($role_edition) : ?>
						<input type='checkbox' name='droit[]' value='<?php echo $droit ?>' <?php echo $ok?"checked='checked'":"" ?>/>&nbsp;
					<?php endif;?>
					<?php echo $droit ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php if ($role_edition) : ?>
		<input type='hidden' name='role' value='<?php echo $role?>'/>
		<input type='submit' value='Modifier' class='btn' />
	<?php endif;?>
</form>



</div>

<div class="box">
<h2>Supprimer le rôle</h2>

<form action='role/delete-controler.php' method='post'>
	<input type='hidden' name='role' value='<?php hecho($role) ?>' />
	<input type='submit' class='btn btn-danger' value='Supprimer' />
</form>
</div>