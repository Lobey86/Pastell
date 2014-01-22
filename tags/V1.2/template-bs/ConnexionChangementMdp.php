<div class="w500">
	<div class="box_contenu clearfix">
		<div class="box_connexion">
			<h2>Réinitialisation du mot de passe</h2>
			
			
			<form action='connexion/changement-mdp-controler.php' method='post' >
			<input type='hidden' name='mail_verif_password' value='<?php echo $mail_verif_password?>'/>
			<table>
				<tr>
				<th><label for="password">Mot de passe</label></th>
				<td><input type="password" name="password" id="password" /></td>
				</tr>
				<tr>
				<th><label for="password2">Mot de passe (confirmer)</label></th>
				<td><input type="password" name="password2" id="password" /></td>
				</tr>
			</table>
			
			<div class="float_left">
			<a href="connexion/connexion.php">Retourner à la connexion</a>
			</div>
			
			<div class="align_right">
			<input type="submit" value="Modifier" class="btn" />
			</div>
			
			</form>
		
		</div>
	</div>
</div>