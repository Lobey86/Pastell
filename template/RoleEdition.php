
<a href='role/index.php'>« Revenir à la liste des rôles</a>
<br/><br/>

<div class="box_contenu clearfix">
	<form class="w700" action='role/edition-controler.php' method='post'>
		<table>
			<tr>
				<th><label for='role'>
				Rôle
				<span>*</span></label> </th>
				 <td> <input <?php echo $role_info['role']?"readonly='readonly'":"" ?>type='text' name='role' value='<?php hecho($role_info['role']) ?>' /></td>
			</tr>
			<tr>
				<th><label for='libelle'>
				Libellé
				<span>*</span></label> </th>
				 <td> <input type='text' name='libelle' value='<?php hecho($role_info['libelle']) ?>' /></td>
			</tr>
		</table>
	
		<div class="align_right">
			<input type='submit' class='submit' value="<?php echo $role_info?"Modifier":"Créer" ?>" />
		</div>
	</form>
</div>



