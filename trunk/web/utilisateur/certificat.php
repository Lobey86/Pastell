<?php 
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);
$verif_number = $recuperateur->get('verif_number');
$offset = $recuperateur->getInt('offset',0);


$utilisateurListe = new UtilisateurListe($sqlQuery);

$limit = 20;

$count = $utilisateurListe->getNbUtilisateurByCertificat($verif_number);
$liste = $utilisateurListe->getUtilisateurByCertificat($verif_number,$offset,$limit);

if (count($liste) < 1){
	header("Location: index.php");
	exit;
}

$certificat = new Certificat($liste[0]['certificat']);
$certificatInfo = $certificat->getInfo();

$page_title = "Certificat";

include( PASTELL_PATH ."/include/haut.php");
?>



<?php 
suivant_precedent($offset,$limit,$count);
?>


<div class="box_contenu clearfix">

<h2>Utilisateurs utilisant ce certificat </h2>

<table class="tab_01">
	<tr>
		<th>Nom Prénom</th>
		<th>Login</th>
		<th>Email</th>
	</tr>
<?php foreach($liste as $i => $user) : 
	
	
?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='utilisateur/detail.php?id_u=<?php echo $user['id_u']?>'><?php echo $user['nom']?>&nbsp;<?php echo $user['prenom']?></a></td>
		<td><?php echo $user['login']?></td>
		<td><?php echo $user['email']?></td>

	</tr>
<?php endforeach; ?>
</table>
</div>

<div class="box_contenu clearfix">

<h2>Détail du certificat </h2>
<br/><br/>
<table  class="tab_04">
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
<?php 



include( PASTELL_PATH ."/include/bas.php");