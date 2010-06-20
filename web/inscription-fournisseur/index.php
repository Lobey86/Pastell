<?php 
require_once("init-information.php");

require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once( PASTELL_PATH . '/lib/transaction/TransactionSQL.class.php');
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');


$recuperateur = new Recuperateur($_GET);
$page = $recuperateur->get('page');

$formulaire->setTabNumber($page);

$page_title = "Inscription fournisseur";

include( PASTELL_PATH . "/include/haut.php");
?>
<div class="box_contenu">

<table class='tab_02'>
<tr>
	<td>Raison sociale : </td><td><?php echo $infoEntite['denomination']?></td>
</tr>
<tr>
	<td>Numéro SIREN : </td><td><?php echo $infoEntite['siren']?></td>
</tr>
<tr>
	<td>Mail : </td><td><?php echo $infoUtilisateur['email']?></td>
</tr>
</table>

</div>

<?php include(PASTELL_PATH . "/include/bloc_message.php");?>

<?php if ($infoEntite['etat'] == Entite::ETAT_EN_COURS_VALIDATION) : ?>
<div class="box_info"><p>Votre formulaire est en cours de soumission à la collectivité.</p></div>
<?php elseif ($infoEntite['etat'] == Entite::ETAT_VALIDE) : ?>
<div class="box_confirm"><p>Votre inscription à été accepté par la collectivité.</p></div>
<?php elseif ($donneesFormulaire->isValidable()) : ?>
<p><a href='inscription-fournisseur/valider.php'>Valider le formulaire</a></p>
<?php else: ?>
<div class="box_alert"><p>Vous devez remplir le formulaire avant de le valider</p></div>
<?php endif;?>

<?php 
$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);
$afficheurFormulaire->afficheTab($page,"inscription-fournisseur/index.php?");
?>

<div class="box_contenu clearfix">

<?php 
if ($infoEntite['etat'] == Entite::ETAT_EN_COURS_VALIDATION || $infoEntite['etat'] == Entite::ETAT_VALIDE) {
	 $afficheurFormulaire->afficheStatic($page,"recuperation-fichier.php?");
} else {
	$afficheurFormulaire->affiche($page,"inscription-fournisseur/valider-information.php");
}
?>
</div>
<?php 
include( PASTELL_PATH . "/include/bas.php");
