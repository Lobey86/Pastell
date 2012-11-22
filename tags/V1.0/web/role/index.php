<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/droit/RoleSQL.class.php");

$droitChecker->verifDroitOrRedirect("role:lecture",0);


$roleSQL = new RoleSQL($sqlQuery);
$allRole = $roleSQL->getAllRole();

$page_title = "Liste des rôles";

if ($roleUtilisateur->hasDroit($authentification->getId(),"role:edition",0)){
	$nouveau_bouton_url = array("Nouveau" => "role/edition.php");
}

include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">

<h2>Liste des rôles</h2>
	
	<table class="tab_01">
		<tr>
			<th>Rôle</th>
			<th>Libellé</th>
		</tr>
	<?php foreach($allRole as $i => $info) : ?>
		<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
			<td><a href='role/detail.php?role=<?php echo  $info['role'] ?>'><?php hecho($info['role']) ?></a></td>
			<td><?php 
			echo $info['libelle'] ?></td>
		</tr>
	<?php endforeach; ?>
	</table>

</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");

