
<div class="box_contenu clearfix">

<h2>Information de version</h2>
<table class='tab_04'>

<tr>
	<th>Révision</th>
	<td><?php echo nl2br(utf8_decode(file_get_contents( PASTELL_PATH."/revision.txt"))) ?></td>
</tr>
</table>
<h2>Extensions PHP</h2>

<table class='tab_04'>
	<?php foreach($checkExtension as $extension => $is_ok) : ?>
		<tr>
			<th><?php echo $extension ?></th>
			<td><?php echo $is_ok?"ok":"<b style='color:red'>CETTE EXTENSION N'EST PAS INSTALLEE</b>"; ?></td>
		</tr>
	<?php endforeach;?>
</table>

<h2>Valeur minimum</h2>

<table class='tab_04'>
	<tr>
		<th>Element</th>
		<th>Attendu</th>
		<th>Trouvé</th>
	</tr>
	<?php foreach($valeurMinimum as $name => $value) : ?>
	<tr>
		<th><?php echo $name?></th>
		<td><?php echo $value ?></td>
		<td><?php echo $valeurReel[$name] ?></td>
	</tr>
	<?php endforeach;?>
</table>

<h2>Constante</h2>
<table class='tab_04'>
	<tr>
		<th>Element</th>
		<th>Valeur</th>
	</tr>
	<tr>
		<th>OPENSSL_PATH</th>
		<td><?php echo OPENSSL_PATH ?></td>
	</tr>
	<tr>
		<th>WORKSPACE_PATH</th>
		<td><?php echo WORKSPACE_PATH ?></td>
	</tr>
</table>

<h2>Auto test</h2>
<table class='tab_04'>
	<tr>
		<td><?php echo WORKSPACE_PATH ?> accessible en lecture/écriture ?</td>
		<td><?php echo $checkWorkspace?"ok":"<b style='color:red'>NON</b>"?></td>
	</tr>
</table>

</div>
