<?php 

require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);
$offset = $recuperateur->getInt('offset',0);


$utilisateurListe = new UtilisateurListe($sqlQuery);

$limit = 20;
$count = $utilisateurListe->getNbUtilisateur();

$page_title = "Liste des utilisateurs";

include( PASTELL_PATH ."/include/haut.php");

suivant_precedent($offset,$limit,$count);

?>

<div class="box_contenu clearfix">

<h2>Utilisateurs<?php if ($roleUtilisateur->hasOneDroit($authentification->getId(),"entite:edition")) : ?>
	<a href="utilisateur/edition.php?" class='btn_add'>
		Nouveau
	</a>
<?php endif;?></h2>

<table class="tab_01">
	<tr>
		<th>Nom Prénom</th>
		<th>Login</th>
		<th>Email</th>
		<th>Vérifié</th>
		<th>Entité</th>
	</tr>
<?php foreach($utilisateurListe->getAll($offset,$limit) as $i => $user) : 
	
	//TODO 
	$infoEntite = $roleUtilisateur->getRole($user['id_u']);
	
?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><a href='utilisateur/detail.php?id_u=<?php echo $user['id_u']?>'><?php echo $user['nom']?>&nbsp;<?php echo $user['prenom']?></a></td>
		<td><?php echo $user['login']?></td>
		<td><?php echo $user['email']?></td>
		<td>
			<?php echo $user['mail_verifie']?"OUI":"NON" ?>
			<?php if ( ! $user['mail_verifie']) : ?>
				<a href='utilisateur/set-mail-verifier.php?id_u=<?php echo $user['id_u']?>'>Marquer comme vérifié</a>
			<?php endif;?>
		</td>
		<td>

			 <?php foreach($infoEntite as $entite) : ?>
				<a href='entite/detail.php?id_e=<?php echo $entite['id_e']?>'>
			 		<?php echo $entite['denomination']?>
			 	</a>
			 	 (<?php echo $entite['role']?>) &nbsp;
			 <?php endforeach;?>
		
		</td>
	</tr>
<?php endforeach; ?>
</table>


</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
