<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/flux/FluxInscriptionFournisseur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteProperties.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteDetailHTML.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentSQL.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentListHTML.class.php");

require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListeHTML.class.php");

require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');
require_once (PASTELL_PATH . "/lib/action/ActionPossible.class.php");
require_once (PASTELL_PATH . "/lib/action/DocumentActionEntite.class.php");
require_once (PASTELL_PATH . "/lib/document/DocumentEntite.class.php");
require_once (PASTELL_PATH . "/lib/helper/date.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");
require_once( PASTELL_PATH . "/lib/droit/RoleDroit.class.php");


$recuperateur = new Recuperateur($_GET);
$tab_number = $recuperateur->getInt('page',0);
$offset = $recuperateur->getInt('offset',0);
$droit = $recuperateur->get('droit','');
$descendance = $recuperateur->get('descendance','');

$id_e=0;
$entite = new Entite($sqlQuery,$id_e);

$droit_lecture = $roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",$id_e);

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}


$page_title = "Propriétés globales";
include( PASTELL_PATH ."/include/haut.php");
?>
<?php include(PASTELL_PATH . "/include/bloc_message.php");?>


<a href='entite/index.php'>« liste des collectivités</a>
<br/><br/>


	<?php 
	
	$documentType = $documentTypeFactory->getDocumentType('entite0-properties');
	$formulaire = $documentType->getFormulaire();
	
	$donneesFormulaire = $donneesFormulaireFactory->get($id_e,'collectivite-properties');
	
	$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
	
	$afficheurFormulaire->afficheTab($tab_number,"entite/detail0.php?id_e=0");
	?>
	<div class="box_contenu clearfix">
	
<?php 
if ($tab_number == 0){
	$roleDroit = new RoleDroit();
	$all_droit =  $roleDroit->getAllDroit();

	$utilisateurListe = new UtilisateurListe($sqlQuery);
	$utilisateurListeHTML = new UtilisateurListeHTML();
	$utilisateurListeHTML->addDroit($allDroit);
	
	if ($roleUtilisateur->hasDroit($authentification->getId(),"utilisateur:edition",$id_e)){
		$utilisateurListeHTML->addDroitEdition();
	}
	if ($descendance){
		$all_id_e = $entite->getDescendance($id_e);
	} else {
		$all_id_e = array($id_e);
	}
	
	if ($droit){
		$allUtilisateur = $utilisateurListe->getUtilisateurByEntiteAndDroit($all_id_e,$droit);
	} else {
		$allUtilisateur = $utilisateurListe->getUtilisateurByEntite($all_id_e);
	}
	
	$utilisateurListeHTML->display($allUtilisateur,$id_e,$droit,$descendance,"entite/detail0.php",0);
	} elseif ($tab_number == 2) { ?>
	<a href='mailsec/annuaire.php'>Annuaire global »</a>
<?php } else { ?>

	<h2><a href="entite/edition-properties.php?id_e=0&page=<?php echo $tab_number ?>" class='btn_maj'>
			Modifier
		</a></h2>

	<?php 
	
	$afficheurFormulaire->afficheStatic($tab_number,"document/recuperation-fichier.php?id_d=0&id_e=0"); 
	
	$theAction = $documentType->getAction();
	$actionPossible = new ActionPossible($sqlQuery,0,$authentification->getId(),$theAction);
	$actionPossible->setRoleUtilisateur($roleUtilisateur);
	$actionPossible->setDonnesFormulaire($donneesFormulaire);
	$actionPossible->setEntite($entite);

	foreach($actionPossible->getActionPossible(0) as $action_name) :
	if ($formulaire->getTabName($tab_number) != $theAction->getProperties($action_name,"tab") ){
		continue;
	}
	?>
	<form action='entite/action0.php' method='post' >
	<input type='hidden' name='id_e' value='0' />
	<input type='hidden' name='page' value='<?php echo $tab_number ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	<input type='submit' value='<?php hecho($theAction->getActionName($action_name)) ?>'/>
</form>
	<?php 
	
	endforeach; 

	
} ?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
