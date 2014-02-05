<div class="box_contenu clearfix">
	<form class="w700" action="entite/import-agent-controler.php" method='post' enctype='multipart/form-data'>
		<input type='hidden' name='id_e' value='<?php hecho($entite_info['id_e'])?>' />
		
		<table>
		<?php if ($entite_info['id_e']) : ?>
		<tr>
			<th>Collectivité (écrasera le SIREN du fichier) :</th>
			<td><?php echo $entite_info['denomination'] ?></td>
		</tr>
		<?php endif;?>
		<tr>
			<th>Fichier CSV</th>
			<td><input type='file' name='csv_agent'/></td>
		</tr>
		</table>
		<input type="submit" value="Importer" class="submit" />
	</form>
</div>

<div class="box_info">
	<p><strong>Format du fichier</strong></p>
	<p>Le fichier CSV doit contenir un agent par ligne.</p>
	<p>Les lignes sont formatés de la manière suivante : 
	"Matricule (5)";"Titre";"Nom d'usage";"Nom patronymique";"Prénom";"Emploi / Grade (C)";"Emploi / Grade (L)";"Collectivité (C)";"Collectivité (L)";"SIREN";"Type de dossier";"Type de dossier (L)";"Train de traitement (C)";"Train de traitement (L)"</p>
	<p>Note: si le fichier est trop gros (&gt;  <?php echo ini_get("upload_max_filesize") ?>) 
	vous pouvez le compresser avec gzip.
	</p>
</div>