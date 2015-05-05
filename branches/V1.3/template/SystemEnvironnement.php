
<div class="box">

<h2>Information de version</h2>
<table class='table table-striped'>

<tr>
	<th class="w140">Version</th>
	<td><?php echo $manifest_info['version']; ?></td>
</tr>
<tr>
	<th class="w140">Révision</th>
	<td><?php echo $manifest_info['revision']; ?></td>
</tr>
<tr>
	<th class="w140">Date du commit</th>
	<td><?php echo $manifest_info['last_changed_date']; ?></td>
</tr>
<tr>
	<th class="w140">Versions compatibles pour les modules</th>
	<td>
		<ul>
		<?php foreach($manifest_info['extensions_versions_accepted'] as $version_module): ?>
			<li><?php hecho($version_module)?></li>
		<?php endforeach;?>
		</ul>
	</td>
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
</div>

<div class="box">
<h2>Commande présente</h2>

<table class='table table-striped'>
	<tr>
		<th class="w140">Commande</th>
		<th>Résultat</th>
	</tr>
	<?php foreach($commandeTest as $commande => $emplacement) : ?>
	<tr>
		<th><?php echo $commande?></th>
		<td><?php echo $emplacement?:"<b style='color:red'>La commande n'est pas disponible</b>"; ?></td>
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
		<td class='w400'><?php echo WORKSPACE_PATH ?> accessible en lecture/écriture ?</td>
		<td><?php echo $checkWorkspace?"ok":"<b style='color:red'>NON</b>"?></td>
	</tr>
</table>
</div>

<div class='box'>
<h2>Envoi de mail</h2>
<div class='alert alert-info'>Permet d'envoyer un <b>email de test</b></div>
<form action='system/mail-test-controler.php' method='post' >
<table class='table table-striped'>
<tr>
	<th class="w200"><label for='email'>
	Email</label></th>
	<td> <input style='width:200px' type='text' name='email' value='' /></td>
</tr>
</table>
<input type='submit' class='btn' value="Envoyer" />

</form>
</div>
