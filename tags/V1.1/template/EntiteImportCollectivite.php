<div class="box_contenu clearfix">			
	<form class="w700" action="entite/import-controler.php" method='post' enctype='multipart/form-data'>
	<input type='hidden' name='id_e' value='<?php hecho($entite_info['id_e'])?>' />
	<table>
		<?php if ($entite_info['id_e']) : ?>
		<tr>
			<th>Collectivité parente</th>
			<td><?php echo $entite_info['denomination'] ?></td>
		</tr>
		<?php endif;?>
		
		<tr>
			<th>Fichier CSV</th>
			<td><input type='file' name='csv_col'/></td>
		</tr>
		<tr>
			<th>Centre de gestion</th>
			<td><?php $this->render("CDGSelect"); ?></td>
		</tr>
	</table>
	<input type="submit" value="Importer" class="submit" />
	
	</form>
	</div>
	
	<div class="box_info">
	<p><strong>Format du fichier</strong></p>
	<p>Le fichier CSV doit contenir une collectivité par ligne.</p>
	<p>Les lignes sont formatés de la manière suivante : "libellé collectivité";"siren"</p>
</div>