<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/mailsec/AnnuaireGroupe.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$id_g = $recuperateur->getInt('id_g');
$offset = $recuperateur->getInt('offset');


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:lecture",$id_e)){
	header("Location: ".SITE_BASE."index.php");
	exit;
}

$can_edit = $roleUtilisateur->hasDroit($authentification->getId(),"annuaire:edition",$id_e);


$annuaireGroupe = new AnnuaireGroupe($sqlQuery,$id_e);

$infoGroupe = $annuaireGroupe->getInfo($id_g);

$listUtilisateur = $annuaireGroupe->getUtilisateur($id_g,$offset);

$nbUtilisateur = $annuaireGroupe->getNbUtilisateur($id_g);

$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();
if ($id_e == 0){
	$infoEntite = array("denomination"=>"Annuaire global");
}


$page= "Carnet d'adresses";
$page_title= $infoEntite['denomination'] . " - Carnet d'adresses";

include( PASTELL_PATH ."/include/haut.php");
include(PASTELL_PATH . "/include/bloc_message.php");

?>
<a href='mailsec/groupe-list.php?id_e=<?php echo $id_e ?>'>« Voir tout les groupes</a>

<br/><br/>
<div class="box_contenu">
<h2>Liste des contacts de «<?php echo $infoGroupe['nom']?>» </h2>

<?php suivant_precedent($offset,AnnuaireGroupe::NB_MAX,$nbUtilisateur,"mailsec/groupe.php?id_e=$id_e&id_g=$id_g"); ?>



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
<?php if ($can_edit) : ?>
<input type='submit' value='Enlever du groupe'/>
<?php endif; ?>

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


<div class="box_contenu">
<h2>Partage</h2>

<?php if ($infoGroupe['partage']) : ?>
<div class='box_info'>
<p>Ce groupe est actuellement partagé avec les entités-filles (services, collectivités) de <?php  echo $infoEntite['denomination'] ?> qui peuvent l'utiliser 
pour leur propre mail.</p>
</div>
<form action='mailsec/partage-groupe.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='id_g' value='<?php echo $id_g ?>' />
	<input type='submit' value='Supprimer le partage'/>
</form>
<?php else:?>
<div class='box_info'>
<p>Cliquer pour partager ce groupe avec les entités filles de <?php  echo $infoEntite['denomination'] ?>.</p>
</div>
<form action='mailsec/partage-groupe.php' method='post' >		
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='id_g' value='<?php echo $id_g ?>' />
	<input type='submit' value='Partager'/>
</form>
<?php endif;?>

</div>

<?php include( PASTELL_PATH ."/include/bas.php");
