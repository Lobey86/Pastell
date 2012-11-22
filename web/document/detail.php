<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/helper/date.php");

$recuperateur = new Recuperateur($_GET);
$id_d = $recuperateur->get('id_d');
$id_e = $recuperateur->get('id_e');
$page = $recuperateur->getInt('page',0);


$entite = new Entite($sqlQuery,$id_e);
$infoEntite = $entite->getInfo();

$id_e_col = $entite->getCollectiviteAncetre();
$collectiviteProperties = $donneesFormulaireFactory->get($id_e_col,'collectivite-properties');

$document = new Document($sqlQuery);
$info = $document->getInfo($id_d);


$documentActionEntite = new DocumentActionEntite($sqlQuery);
$documentEmail = new DocumentEmail($sqlQuery);

$donneesFormulaire = $donneesFormulaireFactory->get($id_d,$info['type']);


$documentType = $documentTypeFactory->getDocumentType($info['type']);
$formulaire = $documentType->getFormulaire();
$theAction = $documentType->getAction();

$documentEntite = new DocumentEntite($sqlQuery);


$actionPossible = new ActionPossible($sqlQuery,$id_e,$authentification->getId(),$theAction);
$actionPossible->setDocumentActionEntite($documentActionEntite);
$actionPossible->setDocumentEntite($documentEntite);
$actionPossible->setRoleUtilisateur($roleUtilisateur);
$actionPossible->setDonnesFormulaire($donneesFormulaire);
$actionPossible->setEntite($entite);
$actionPossible->setHeritedProperties($collectiviteProperties);


if ( ! $roleUtilisateur->hasDroit($authentification->getId(),$info['type'].":edition",$id_e)) {
	header("Location: list.php");
	exit;
}

$my_role = $documentEntite->getRole($id_e,$id_d);
if (! $my_role ){
	header("Location: list.php");
	exit;
}

$journal->addConsultation($id_e,$id_d,$authentification->getId());


$page_title =  $info['titre'] . " (".$documentType->getName().")";


include( PASTELL_PATH ."/include/haut.php" );
?>

<?php include(PASTELL_PATH . "/include/bloc_message.php");?>


<a href='document/list.php?type=<?php echo $info['type']?>&id_e=<?php echo $id_e?>&last_id=<?php echo $id_d ?>'>« Liste des "<?php echo $documentType->getName() ?>" de <?php echo $infoEntite['denomination']?></a>
<br/><br/>
<?php
$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);

$afficheurFormulaire->afficheTab($page,"document/detail.php?id_d=$id_d&id_e=$id_e");
?>

<div class="box_contenu">

<?php 
$afficheurFormulaire->afficheStatic($page,"document/recuperation-fichier.php?id_d=$id_d&id_e=$id_e");
?>
<br/>
<?php foreach($actionPossible->getActionPossible($id_d) as $action_name) : ?>
<form action='document/action.php' method='post' >
	<input type='hidden' name='id_d' value='<?php echo $id_d ?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='<?php echo $page ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' value='<?php hecho($theAction->getDoActionName($action_name)) ?>'/>
</form>
<?php endforeach;?>

</div>

<div class="box_contenu clearfix">
<h2>Entité concernée par le document</h2>

<table class="tab_02">
	<tbody>
		<tr>
			<th>Entité</th>
			<th>Rôle</th>
		</tr>
		
<?php foreach($documentEntite->getEntite($id_d) as $docEntite) : 
	if ($my_role == 'editeur' || $docEntite['role'] == 'editeur' || $docEntite['id_e'] == $id_e) : 
?>
	<tr>
			<td><a href='entite/detail.php?id_e=<?php echo $docEntite['id_e'] ?>'><?php echo $docEntite['denomination']?></a></td>
			<td><?php echo $docEntite['role']?></td>
		</tr>
<?php 
	endif;
endforeach;?>
	</tbody>
</table>
</div>

<?php 
$infoDocumentEmail = $documentEmail->getInfo($id_d);
if ($infoDocumentEmail) : 
?>
<div class="box_contenu clearfix">
<h2>Utilisateurs destinataires du message</h2>

<table class="tab_02">
	<tbody>
		<tr>
			<th>Email</th>
			<th>Type</th>
			<th>Date d'envoi</th>
			<th>Lecture</th>
		</tr>
		
<?php foreach($infoDocumentEmail as $infoEmail) : ?>
	<tr>
		<td><?php echo htmlentities($infoEmail['email'],ENT_QUOTES)?></td>
		<td><?php echo DocumentEmail::getChaineTypeDestinataire($infoEmail['type_destinataire']) ?></td>
		<td><?php echo time_iso_to_fr($infoEmail['date_envoie'])?></td>
		<td>
			<?php if ($infoEmail['lu']) : ?>
				<?php echo time_iso_to_fr($infoEmail['date_lecture'])?>
			<?php else : ?>
				Non
			<?php endif;?>
		</td>
	</tr>	
<?php endforeach;?>
	</tbody>
</table>
</div>


<?php endif;?>


<div class="box_contenu clearfix">
<h2>États du document</h2>

<table class="tab_01">
	<tbody>
		<tr>
			<th>État</th>
			<th>Date</th>
			<th>Entité</th>
			<th>Utilisateur</th>
		</tr>
		
		<?php foreach($documentActionEntite->getAction($id_e,$id_d) as $action) : ?>
			<tr>
				<td><?php echo $theAction->getActionName($action['action']) ?></td>
				<td><?php echo time_iso_to_fr($action['date'])?></td>
				<td><a href='entite/detail.php?id_e=<?php echo $action['id_e']?>'><?php echo $action['denomination']?></a></td>
				<td>
					<?php if ($action['id_u'] == 0) : ?>
						Action automatique
					<?php endif;?>
					<?php if ($action['id_e'] == $id_e) :?>
						<a href='utilisateur/detail.php?id_u=<?php echo $action['id_u']?>'><?php echo $action['prenom']?> <?php echo $action['nom']?></a>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>
	</tbody>
</table>
</div>

<?php if ($id_e) : ?>
<a href='journal/index.php?id_e=<?php echo $id_e?>&id_d=<?php echo $id_d?>'>Voir le journal des évènements</a>
<br/><br/>
<?php 
endif;
include( PASTELL_PATH ."/include/bas.php" );
