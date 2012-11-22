<?php 

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/droit/RoleSQL.class.php");


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"role:edition",0)){
	header("Location: index.php");
	exit;
}

$recuperateur = new Recuperateur($_GET);
$role = $recuperateur->get('role');

$page_title = "Ajout d'un rôle";

if ($role){
	$page_title = "Modification du rôle $role ";
}

include( PASTELL_PATH ."/include/haut.php");
?>

<a href='role/index.php'>« Revenir à la liste des rôles</a>
<br/><br/>

<?php include (PASTELL_PATH."/include/bloc_message.php"); ?>

<div class="box_contenu clearfix">
	<form class="w700" action='role/edition-controler.php' method='post'>
	
		<table>
			<tr>
				<th><label for='role'>
				Rôle
				<span>*</span></label> </th>
				 <td> <input type='text' name='role' value='' /></td>
			</tr>
			<tr>
				<th><label for='libelle'>
				Libellé
				<span>*</span></label> </th>
				 <td> <input type='text' name='libelle' value='' /></td>
			</tr>
		</table>
	
		<div class="align_right">
			<input type='submit' class='submit' value="<?php echo $role?"Modifier":"Créer" ?>" />
		</div>
	</form>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");


