<div class="box">			
	<form action="entite/import-controler.php" method='post' enctype='multipart/form-data'>
	<input type='hidden' name='id_e' value='<?php hecho($entite_info['id_e'])?>' />
	<table class='table'>
		<?php if ($entite_info['id_e']) : ?>
		<tr>
			<th class='w140'>Collectivité parente</th>
			<td><?php echo $entite_info['denomination'] ?></td>
		</tr>
		<?php endif;?>
		
		<tr>
			<th class='w140'>Fichier CSV</th>
			<td><input type='file' name='csv_col'/></td>
		</tr>
		<tr>
			<th>Centre de gestion</th>
			<td><?php $this->render("CDGSelect"); ?></td>
		</tr>
	</table>
	<input type="submit" value="Importer" class="btn" />
	
	</form>
	</div>
	
	<div class="alert alert-info">
	<p><strong>Format du fichier</strong></p>
	<p>Le fichier CSV doit contenir une collectivité par ligne.</p>
	<p>Les lignes sont formatés de la manière suivante : "libellé collectivité";"siren"</p>
</div>