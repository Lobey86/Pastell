<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");

$droitChecker->verifDroitOrRedirect("role:lecture",0);

$role_edition = $droitChecker->hasDroit("role:edition",0);


$recuperateur = new Recuperateur($_GET);
$role = $recuperateur->get('role');

$roleSQL = new RoleSQL($sqlQuery);

$all_droit = $objectInstancier->RoleDroit->getAllDroit();
$all_droit_utilisateur = $roleSQL->getDroit($all_droit,$role);

$page_title = "Droits associés au rôle <em>$role</em>";

$i= 0;


include( PASTELL_PATH ."/include/haut.php");
?>
<a href='role/index.php'>« Revenir à la liste des rôles</a>
<br/><br/>
<div class="box_contenu clearfix">

<h2>Liste des droits</h2>

<form action='role/detail-controler.php' method='post'>
	<table class="tab_01">
		<tr>
			<th>Droits</th>
		</tr>
		<?php foreach($all_droit_utilisateur as $droit => $ok) : ?>
			<tr class='<?php echo $i++%2?'bg_class_gris':'bg_class_blanc'?>'>
				<td>
					<?php if ($role_edition) : ?>
						<input type='checkbox' name='droit[]' value='<?php echo $droit ?>' <?php echo $ok?"checked='checked'":"" ?>/>
					<?php endif;?>
					<?php echo $droit ?>
				</td>
			</tr>
		<?php endforeach; ?>
	</table>
	<?php if ($role_edition) : ?>
		<input type='hidden' name='role' value='<?php echo $role?>'/>
		<input type='submit' value='Modifier' />
	<?php endif;?>
</form>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
