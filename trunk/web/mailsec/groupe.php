<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireGroupe.class.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$id_g = $recuperateur->getInt('id_g');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:lecture",$id_e)){
	header("Location: ".SITE_BASE."index.php");
	exit;
}

$annuaireGroupe = new AnnuaireGroupe($sqlQuery,$id_e);

$infoGroupe = $annuaireGroupe->getInfo($id_g);

$listUtilisateur = $annuaireGroupe->getUtilisateur($id_g);


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$page= "Carnet d'adresse";
$page_title= $infoEntite['denomination'] . " - Carnet d'adresse";

include( PASTELL_PATH ."/include/haut.php");
include(PASTELL_PATH . "/include/bloc_message.php");

?>
<a href='mailsec/groupe-list.php?id_e=<?php echo $id_e ?>'>« Voir tout les groupes</a>

<br/><br/>
<div class="box_contenu">
<h2>Liste des contacts de «<?php echo $infoGroupe['nom']?>» </h2>

<form action='mailsec/del-contact-from-groupe.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='id_g' value='<?php echo $id_g ?>' />

<table  class="tab_02">
	<tr>
	
		<th>Description</th>
		<th>Email</th>
		
	</tr>
<?php foreach($listUtilisateur as $utilisateur) : ?>
	<tr>
		<td><input type='checkbox' name='id_a[]' value='<?php echo $utilisateur['id_a'] ?>'/><?php echo $utilisateur['description']?></td>
		<td><?php echo $utilisateur['email']?></td>
	</tr>
<?php endforeach;?>
	
</table>
<input type='submit' value='Enlever du groupe'/>
</form>
</div>

<?php if ( $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e)) : ?>

<div class="box_contenu">
<h2>Ajouter un contact à «<?php echo $infoGroupe['nom']?>» </h2>
<form action='mailsec/add-contact-to-groupe.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='id_g' value='<?php echo $id_g ?>' />
	
	<table>
		<tbody>
			<tr>
				<th>Contact : </th>
				<td><input type='text' id='name' name='name' value='' /></td>
			</tr>	
		</tbody>
	</table>
	<script>
		 var format_item = function (item, position, length){ 
		    return htmlentities("" + item,"ENT_QUOTES");
		  } 
	 
 		 $(document).ready(function(){
				$("#name").autocomplete("mailsec/get-contact-ajax.php",  
						{
						cacheLength:0, 
						max: 20, 
						extraParams: { id_e: <?php echo $id_e?>, "mail-only": "true"},
						formatItem : format_item,

				});
 		 });
	</script>
	<input type='submit' value='Ajouter'/>
</form>
</div>
<?php endif;?>
<?php include( PASTELL_PATH ."/include/bas.php");
