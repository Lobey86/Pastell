
<div class="box">

<h2>Information de version</h2>
<table class='table table-striped'>

<tr>
	<th class="w140">R�vision</th>
	<td><?php echo nl2br(utf8_decode(file_get_contents( PASTELL_PATH."/revision.txt"))) ?></td>
</tr>
</table>

</div>

<div class="box">
<h2>Extensions PHP</h2>

<table class='table table-striped'>
	<?php foreach($checkExtension as $extension => $is_ok) : ?>
		<tr>
			<th class="w140"><?php echo $extension ?></th>
			<td><?php echo $is_ok?"ok":"<b style='color:red'>CETTE EXTENSION N'EST PAS INSTALLEE</b>"; ?></td>
		</tr>
	<?php endforeach;?>
</table>
</div>

<div class="box">
<h2>Modules PHP</h2>

<table class='table table-striped'>
	<?php foreach($checkModule as $module => $is_ok) : ?>
		<tr>
			<th class="w140"><?php echo $module ?></th>
			<td><?php echo $is_ok?"ok":"<b style='color:red'>CE MODULE N'EST PAS ACCESSIBLE</b>"; ?></td>
		</tr>
	<?php endforeach;?>
</table>
</div>




<div class="box">
<h2>Valeur minimum</h2>

<table class='table table-striped'>
	<tr>
		<th class="w140">Element</th>
		<th>Attendu</th>
		<th>Trouv�</th>
	</tr>
	<?php foreach($valeurMinimum as $name => $value) : ?>
	<tr>
		<th><?php echo $name?></th>
		<td><?php echo $value ?></td>
		<td><?php echo $valeurReel[$name] ?></td>
	</tr>
	<?php endforeach;?>
</table>
</div>
<div class="box">
<h2>Constante</h2>
<table class='table table-striped'>
	<tr>
		<th class="w140">Element</th>
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
</div>
<div class="box">
<h2>Auto test</h2>
<table class='table table-striped'>
	<tr>
		<td class='w400'><?php echo WORKSPACE_PATH ?> accessible en lecture/�criture ?</td>
		<td><?php echo $checkWorkspace?"ok":"<b style='color:red'>NON</b>"?></td>
	</tr>
</table>

</div>
