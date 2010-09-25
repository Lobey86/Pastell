<?php 
include( "../init-admin.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");



$recuperateur = new Recuperateur($_GET);
$id_u = $recuperateur->get('id_u');

$utilisateur = new Utilisateur($sqlQuery,$id_u);

$info = $utilisateur->getInfo();
if (! $info){
	header("Location: ".SITE_BASE . "index.php");
}


$roleDroit = new RoleDroit();

$page_title = "Utilisateur ".$info['prenom']." " . $info['nom'];

$entiteListe = new EntiteListe($sqlQuery);


$tabEntite = $roleUtilisateur->getEntite($authentification->getId(),'entite:edition');


include( PASTELL_PATH ."/include/haut.php");
?>
<a href='utilisateur/index.php'>« liste des utilisateurs</a>

<br/><br/>


<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>

<div class="box_contenu clearfix">

<h2>Détail de l'utilisateur <?php echo $info['prenom']." " . $info['nom']?></h2>

<table class='tab_04'>

<tr>
<th>Login</th>
<td><?php echo $info['login'] ?></td>
</tr>

<tr>
<th>Prénom</th>
<td><?php echo $info['prenom'] ?></td>
</tr>

<tr>
<th>Nom</th>
<td><?php echo $info['nom'] ?></td>
</tr>

<tr>
<th>Email</th>
<td><?php echo $info['email'] ?></td>
</tr>

<tr>
<th>Date d'inscription</th>
<td><?php echo $info['date_inscription'] ?></td>
</tr>
</table>
</div>

<div class="box_contenu clearfix">
<h2>Rôle de l'utilisateur</h2>

<table class='tab_01'>
<tr>
<th>Rôle</th>
<th>Entité</th>
<th>&nbsp;</th>
</tr>

<?php foreach ($roleUtilisateur->getRole($id_u) as $infoRole) : ?>
<tr>
	<td><?php echo $infoRole['role']?></td>
	<td>
		<?php if ($infoRole['id_e']) : ?>
			<a href='entite/detail.php?id_e=<?php echo $infoRole['id_e']?>'><?php echo $infoRole['denomination']?></a>
		<?php else : ?>
			Toutes les collectivités 
		<?php endif;?>
	</td> 
	<td>
		<a href='utilisateur/supprimer-role.php?id_u=<?php echo $id_u ?>&role=<?php echo $infoRole['role']?>&id_e=<?php echo $infoRole['id_e']?>'>
			enlever ce rôle
		</a>
	</td>
</tr>
<?php endforeach;?>
</table>



<h3>Ajouter un rôle </h3>

<form action='utilisateur/ajouter-role.php' method='post'>
	<input type='hidden' name='id_u' value='<?php echo $id_u ?>' />

	<select name='role'>
		<option value=''>...</option>
		<?php foreach($roleDroit->getAllRole() as $role => $lesDroits): ?>
		<option value='<?php echo $role?>'> <?php echo $role ?> </option>
		<?php endforeach ; ?>
	</select>
	
	<select name='id_e'>
		<option value=''>...</option>
		<?php foreach($entiteListe->getArbreFilleFromArray($tabEntite) as $entiteInfo): ?>
		<option value='<?php echo $entiteInfo['id_e']?>'>
			<?php for($i=0; $i<$entiteInfo['profondeur']; $i++){ echo "&nbsp&nbsp;";}?>
			|_<?php echo $entiteInfo['denomination']?> </option>
		<?php endforeach ; ?>
	</select>
	
	<input type='submit' value='ajouter'/>
</form>

</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");


