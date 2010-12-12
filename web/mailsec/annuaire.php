<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mail/Annuaire.class.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:lecture",$id_e)){
	header("Location: ".SITE_BASE."index.php");
	exit;
}

$annuaire = new Annuaire($sqlQuery,$id_e);

$listUtilisateur = $annuaire->getUtilisateur();


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$page= "Carnet d'adresse";
$page_title= $infoEntite['denomination'] . " - Carnet d'adresse";

include( PASTELL_PATH ."/include/haut.php");?>
<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=3'>« Administration de <?php echo $infoEntite['denomination']?></a>

<table>
	<tr>
		<th>email</th>
	</tr>
<?php foreach($listUtilisateur as $utilisateur) : ?>
	<tr>Naiyei7w
		<td><?php echo $utilisateur['description']?></td>
		<td><?php echo $utilisateur['email']?></td>
	</tr>
<?php endforeach;?>
</table>


<?php include( PASTELL_PATH ."/include/bas.php");
