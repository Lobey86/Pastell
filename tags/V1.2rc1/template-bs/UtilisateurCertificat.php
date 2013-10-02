<?php 
$this->SuivantPrecedent($offset,$limit,$count);
?>

<div class="box">

<h2>Utilisateurs utilisant ce certificat </h2>

<table class="table table-striped">
	<tr>
		<th>Nom Prénom</th>
		<th>Login</th>
		<th>Email</th>
	</tr>
<?php foreach($liste as $i => $user) : 
	
	
?>
	<tr>
		<td><a href='utilisateur/detail.php?id_u=<?php echo $user['id_u']?>'><?php echo $user['nom']?>&nbsp;<?php echo $user['prenom']?></a></td>
		<td><?php echo $user['login']?></td>
		<td><?php echo $user['email']?></td>

	</tr>
<?php endforeach; ?>
</table>
</div>

<div class="box">

<h2>Détail du certificat</h2>

<table  class="table table-striped">
	<tr>
		<th>Numéro de série</th>
		<td>
			<?php echo $certificat->getSerialNumber() ?>
		</td>
	</tr>
		<tr>
		<th>Nom</th>
		<td>
			<?php echo $certificatInfo['name'] ?>
		</td>
	</tr>
	<tr>
		<th>Emis pour </th>
		<td>
		<ul>
		<?php foreach($certificatInfo['subject'] as $col => $value) : ?>
		<li><?php echo "$col : $value" ;?></li>		
		<?php endforeach;?>
		</ul>
	</td>
	</tr>

	<tr>
		<th>Emis par </th>
		<td>
		<ul>
		<?php foreach($certificatInfo['issuer'] as $col => $value) : ?>
		<li><?php echo "$col : $value" ;?></li>		
		<?php endforeach;?>
		</ul>
	</td>
	</tr>
	<tr>
		<th>Validité </th>
		<td>
		<ul>
		
			<li><?php echo "Emis le ". date(Date::DATE_FR,$certificatInfo['validFrom_time_t']) ;?></li>	
			<li><?php echo "Expire le ". date(Date::DATE_FR,$certificatInfo['validTo_time_t']) ;?></li>	
		</ul>
	</td>
	</tr>
		<tr>
		<th>&nbsp; </th>
		<td>
			<a href='utilisateur/getCertificat.php?verif_number=<?php echo $verif_number?>'>Télécharger le certificat</a>
		</td>
	</tr>
</table>

</div>
