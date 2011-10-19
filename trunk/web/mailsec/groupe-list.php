<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireGroupe.class.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');

if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:lecture",$id_e)){
	header("Location: ".SITE_BASE."index.php");
	exit;
}


$annuaireGroupe = new AnnuaireGroupe($sqlQuery,$id_e);

$listGroupe = $annuaireGroupe->getGroupe();

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$page= "Carnet d'adresses";
$page_title= $infoEntite['denomination'] . " - Carnet d'adresses";

include( PASTELL_PATH ."/include/haut.php");
include(PASTELL_PATH . "/include/bloc_message.php");

?>
<a href='mailsec/annuaire.php?id_e=<?php echo $id_e ?>'>« Voir la liste des contacts</a>

<br/><br/>
<div class="box_contenu">
<h2>Liste des groupes de contacts de <?php echo $infoEntite['denomination'] ?> </h2>

<form action='mailsec/del-groupe.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />

<table  class="tab_02">
	<tr>
	
		<th>Nom</th>
		<th>Contact</th>
		
	</tr>
<?php foreach($listGroupe as $groupe) : 
	$nbUtilisateur = $annuaireGroupe->getNbUtilisateur($groupe['id_g']); 
	$utilisateur = $annuaireGroupe->getUtilisateur($groupe['id_g']);
	$r = array();
	foreach($utilisateur as $u){
		$r[] = htmlentities( '"' . $u['description'] .'"'. " <".$u['email'].">",ENT_QUOTES);
	}
	$utilisateur = implode(",<br/>",$r);
?>
	<tr>
		<td><input type='checkbox' name='id_g[]' value='<?php echo $groupe['id_g'] ?>'/>
			<a href='mailsec/groupe.php?id_e=<?php echo $id_e?>&id_g=<?php echo $groupe['id_g']?>'><?php echo $groupe['nom']?></a></td>
		<td><?php if ($nbUtilisateur) : ?>
				<?php echo $utilisateur;?>
			<?php else : ?>
				Ce groupe est vide
			<?php endif;?>	
		</td>
	</tr>
<?php endforeach;?>
	
</table>
<input type='submit' value='Supprimer'/>
</form>
</div>

<?php if ( $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) : ?>

<div class="box_contenu">
<h2>Ajouter un groupe</h2>
<form action='mailsec/add-groupe.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	
	<table>
		<tbody>
			<tr>
				<th>Nom</th>
				<td><input type='text' name='nom' value='<?php echo $lastError->getLastInput('nom') ?>' /></td>
			</tr>
		</tbody>
	</table>
	<input type='submit' value='Ajouter'/>
</form>
</div>
<?php endif;?>
<?php include( PASTELL_PATH ."/include/bas.php");
