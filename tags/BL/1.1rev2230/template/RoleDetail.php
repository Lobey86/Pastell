<?php $i = 0; ?>
<a href='role/index.php'>« Revenir à la liste des rôles</a>
<br/><br/>
<div class="box_contenu clearfix">

<h2>Liste des droits - <?php  hecho($role_info['libelle']) ?></h2>

<a href='role/edition.php?role=<?php hecho($role) ?>'>Modifier le libellé</a>
<br/><br/>
<form action='role/detail-controler.php' method='post'>
	<table class="tab_01">
		<tr>
			<th>Droits</th>
		</tr>
		<?php foreach($all_droit_utilisateur as $droit => $ok) : ?>
			<tr class='<?php echo $i++%2?'bg_class_gris':'bg_class_blanc'?>'>
				<td>
					<?php if ($role_edition) : ?>
						<input type='checkbox' name='droit[]' value='<?php echo $droit ?>' <?php echo $ok?"checked='checked'":"" ?>/>
					<?php endif;?>
					<?php echo $droit ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php if ($role_edition) : ?>
		<input type='hidden' name='role' value='<?php echo $role?>'/>
		<input type='submit' value='Modifier' />
	<?php endif;?>
</form>



</div>

<div class="box_contenu clearfix">
<h2>Supprimer le rôle</h2>



<form action='role/delete-controler.php' method='post'>
	<input type='hidden' name='role' value='<?php hecho($role) ?>' />
	<input type='submit' value='Supprimer' />
</form>
</div>