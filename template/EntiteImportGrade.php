<div class="box_contenu clearfix">
	<form class="w700" action="entite/import-grade-controler.php" method='post' enctype='multipart/form-data'>
		
		<table>
		
		<tr>
			<th>Fichier CSV</th>
			<td><input type='file' name='csv_grade'/></td>
		</tr>
		</table>
		<input type="submit" value="Importer" class="submit" />
	</form>
</div>

<div class="box_info">
	<p><strong>Format du fichier</strong></p>
	<p>Le fichier CSV doit contenir un grade par ligne.</p>
	<p>Les lignes sont formatés de la manière suivante : 
	Filière (C);Filière (L);Cadre d'emplois (C);Cadre d'emplois (L);Grade (C);Grade (L)
	</p>
	<p>Note: si le fichier est trop gros (&gt;  <?php echo ini_get("upload_max_filesize") ?>) 
	vous pouvez le compresser avec gzip.
	</p>
</div>