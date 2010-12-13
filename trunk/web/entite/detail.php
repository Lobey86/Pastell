<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/flux/FluxInscriptionFournisseur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/helper/date.php");

$recuperateur = new Recuperateur($_GET);
$id_e = $recuperateur->getInt('id_e');
$tab_number = $recuperateur->getInt('page',0);


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e)){
	header("Location: index.php");
	exit;
}

$entite = new Entite($sqlQuery,$id_e);
if ($id_e && ! $entite->exists()){
	header("Location: index.php");
	exit;
}


$info = $entite->getInfo();


$utilisateurListe = new UtilisateurListe($sqlQuery);

$lastTransaction = false;
if ($info['type'] == Entite::TYPE_FOURNISSEUR) {
	$transactionFinder = new TransactionFinder($sqlQuery);
	$lastTransaction = $transactionFinder->getLastTransactionBySiren($siren,FluxInscriptionFournisseur::TYPE);
}
if ($id_e){
	$page_title = "Détail " . $info['denomination'];
} else {
	$page_title = "Utilisateurs globaux";
}
$infoMere = false;
if ($info['entite_mere']){
	$entiteMere = new Entite($sqlQuery,$info['entite_mere']);
	$infoMere = $entiteMere->getInfo();
}

$filles = $entite->getFille();

$entiteProperties = new EntiteProperties($sqlQuery,$id_e);





include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<?php if ($info['type'] == Entite::TYPE_FOURNISSEUR) : ?>
<a href='entite/fournisseur.php'>« liste des fournisseurs</a>
<?php else :?>
<a href='entite/index.php'>« liste des collectivités</a>
<?php endif;?>
<br/><br/>

<?php 
if ($info['type'] != Entite::TYPE_FOURNISSEUR && $id_e != 0) {

	$documentType = $documentTypeFactory->getDocumentType('collectivite-properties');
	$formulaire = $documentType->getFormulaire();
	
	$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
	
	
	$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
	$afficheurFormulaire->injectHiddenField("id_e",$id_e);
	
	$afficheurFormulaire->afficheTab($tab_number,"entite/detail.php?id_e=$id_e");
}	
?>
<?php if ($id_e) : ?>
<div class="box_contenu clearfix">

<?php if ($tab_number == 0) : ?>

<h2>Informations générales
<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) : ?>
<a href="entite/edition.php?id_e=<?php echo $id_e?>" class='btn_maj'>
		Modifier
	</a>
<?php endif;?>
</h2>
	

<table class='tab_04'>

<tr>
<th>Type</th>
<td><?php echo Entite::getNom($info['type']) ?></td>
</tr>

<tr>
<th>Dénomination</th>
<td><?php echo $info['denomination'] ?></td>
</tr>
<?php if ($info['siren']) : ?>
<tr>
<th>Siren</th>
<td><?php echo $info['siren'] ?></td>
</tr>
<?php endif;?>

<?php if ($info['type'] == Entite::TYPE_FOURNISSEUR ) : ?>
<tr>
<th>Etat</th>

<td>
<?php if($lastTransaction) : ?>
<a href='<?php echo SITE_BASE ?>flux/detail-transaction.php?id_t=<?php echo $lastTransaction; ?>'>
<?php endif;?>
<?php echo Entite::getChaineEtat($info['etat']) ?> 
<?php if($lastTransaction) : ?>
</a>
<?php endif;?>

</td>
</tr>
<?php endif;?>
<tr>
<th>Date d'inscription</th>
<td><?php echo time_iso_to_fr($info['date_inscription']) ?></td>
</tr>
<?php if ($infoMere) : ?>
<tr>
	<th>Entité mère</th>
	<td>
		<a href='entite/detail.php?id_e=<?php echo $infoMere['id_e']?>'>
			<?php echo $infoMere['denomination'] ?>
		</a>
	</td>
</tr>
<?php endif;?>
<?php if ($info['type'] != Entite::TYPE_FOURNISSEUR ) : ?>
	<tr>
	<th>Entité fille</th>
	<td>
		<?php if ( ! $filles) : ?>
			<?php echo "Cette entité n'a pas d'entité fille"?>
		<?php endif;?>
		<ul>
		<?php foreach($filles as $fille) : ?>
			<li><a href='entite/detail.php?id_e=<?php echo $fille['id_e']?>'>
				<?php echo $fille['denomination']?>
			</a></li>
		<?php endforeach;?>
		</ul>
		<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) : ?>
		<a href="entite/edition.php?entite_mere=<?php echo $id_e?>" >
			Ajouter une entité fille
		</a>
		<?php endif;?>
	</td>
	</tr>
<?php endif;?>
<?php if ($info['centre_de_gestion']) : 
	$cdg = new Entite($sqlQuery,$info['centre_de_gestion']);
	$infoCDG = $cdg->getInfo();

?>
	<tr>
		<th>Centre de gestion</th>
		<td>
		<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$infoCDG['id_e'])) : ?>			
			<a href='entite/detail.php?id_e=<?php echo $infoCDG['id_e']?>'>
		<?php endif; ?>
			<?php echo $infoCDG['denomination']?>
			<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$infoCDG['id_e'])) : ?>			
			</a>
			<?php endif; ?>
			
			</td>
	</tr>
<?php endif;?>

<tr>
<th>Système de GED</th>
<td><?php echo $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_ged') ?></td>
</tr>

<tr>
<th>Système de d'archivage légal</th>
<td><?php echo $entiteProperties->getProperties(EntiteProperties::ALL_FLUX,'has_archivage') ?></td>
</tr>

</table>
<?php else: 


$theAction = $documentType->getAction();

$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
//$actionPossible->setDocumentActionEntite($documentActionEntite);
//$actionPossible->setDocumentEntite($documentEntite);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);


?>
	<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:edition",$id_e)) : ?>
	<h2><a href="entite/edition-properties.php?id_e=<?php echo $id_e?>&page=<?php echo $tab_number ?>" class='btn_maj'>
			Modifier
		</a></h2>
	<?php endif;?>

	<?php $afficheurFormulaire->afficheStatic($tab_number,"document/recuperation-fichier.php?id_d=$id_e"); ?>
	
<br/>
<?php 



foreach($actionPossible->getActionPossible($id_e) as $action_name) : 

if ($formulaire->getTabName($tab_number) != $theAction->getProperties($action_name,"tab") ){
	continue;
}
?>
<form action='entite/action.php' method='post' >
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='<?php echo $tab_number ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' value='<?php hecho($theAction->getActionName($action_name)) ?>'/>
</form>
<?php endforeach;?>
<?php endif;?>

</div>
<?php endif;?>


<div class="box_contenu">
<h2>Liste des utilisateurs
<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:edition",$id_e)) : ?>
<a href="utilisateur/edition.php?id_e=<?php echo $id_e?>" class='btn_add'>
		Nouveau
	</a>
<?php endif;?>
</h2>

<table class='<?php echo $info['type'] != Entite::TYPE_FOURNISSEUR?"tab_02":"tab_03" ?>'>
<tr>
	<th>Prénom Nom</th>
	<th>login</th>
	<th>email</th>
	<th>Role</th>
	
</tr>
<?php foreach($utilisateurListe->getUtilisateurByEntite($id_e) as $user) : ?>
	<tr>
		<td>
			<a href='utilisateur/detail.php?id_u=<?php echo $user['id_u'] ?>'>
				<?php echo $user['prenom']?> <?php echo $user['nom']?>
			</a>
		</td>
		<td><?php echo $user['login']?></td>
		<td><?php echo $user['email']?></td>
		<td><?php echo $user['role']?></td>
	</tr>
<?php endforeach; ?>

</table>


</div>
<?php if($info['type'] == Entite::TYPE_FOURNISSEUR): ?>
<a href='supprimer.php'>Redemander les informations</a>
<?php endif; ?>

<?php 
include( PASTELL_PATH ."/include/bas.php");
