
<a class='btn btn-mini' href='role/index.php'><i class="icon-circle-arrow-left"></i>Revenir à la liste des rôles</a>


<div class="box">


    <form class="form-horizontal" action='role/edition-controler.php' method='post'>
		<div class="control-group">
			<label class="control-label" for="role">Rôle<span class="obl">*</span></label>
			<div class="controls">
				<input <?php echo $role_info['role']?"readonly='readonly'":"" ?> type='text' name='role' id='role' value='<?php hecho($role_info['role']) ?>' />
			</div>
		</div>
		
		<div class="control-group">
			<label class="control-label" for="libelle">Libellé<span class="obl">*</span></label>
			<div class="controls">
				<input type='text' name='libelle' id='libelle' value='<?php hecho($role_info['libelle']) ?>' />
			</div>

		</div>
		
		
			<input type='submit' class='btn' value="<?php echo $role_info?"Modifier":"Créer" ?>" />

    </form>




</div>

