<?php

require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/flux/FluxInscriptionFournisseur.class.php");
require_once( PASTELL_PATH . "/lib/utilisateur/UtilisateurListe.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionFinder.class.php");

$recuperateur = new Recuperateur($_GET);
$siren = $recuperateur->get('siren');

$entite = new Entite($sqlQuery,$siren);
$info = $entite->getInfo();

$utilisateurListe = new UtilisateurListe($sqlQuery);

$lastTransaction = false;
if ($info['type'] == Entite::TYPE_FOURNISSEUR) {
	$transactionFinder = new TransactionFinder($sqlQuery);
	$lastTransaction = $transactionFinder->getLastTransactionBySiren($siren,FluxInscriptionFournisseur::TYPE);
}

$page_title = "Détail " . $info['denomination'];

$infoMere = false;
if ($info['entite_mere']){
	$entiteMere = new Entite($sqlQuery,$info['entite_mere']);
	$infoMere = $entiteMere->getInfo();
}

$filles = $entite->getFille();

include( PASTELL_PATH ."/include/haut.php");
?>
<?php if($info['type'] == Entite::TYPE_COLLECTIVITE) : ?>
<a href='entite/collectivite.php'>« liste des collectivités</a>
<?php elseif ($info['type'] == Entite::TYPE_FOURNISSEUR) : ?>
<a href='entite/fournisseur.php'>« liste des fournisseurs</a>

<?php endif;?>
<br/><br/>


<div class="box_contenu">

<h2>Informations générales</h2>
	<a href="entite/nouveau.php?siren=<?php echo $info['siren']?>" class='btn'>
		<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
		Modifier
	</a>

<table>

<tr>
<th>Type</th>
<td><?php echo $info['type'] ?></td>
</tr>

<tr>
<th>Dénomination</th>
<td><?php echo $info['denomination'] ?></td>
</tr>

<tr>
<th>Siren</th>
<td><?php echo $info['siren'] ?></td>
</tr>

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

<tr>
<th>Date d'inscription</th>
<td><?php echo $info['date_inscription'] ?></td>
</tr>
<?php if ($infoMere) : ?>
<tr>
	<th>Entité mère</th>
	<td>
		<a href='entite/detail.php?siren=<?php echo $infoMere['siren']?>'>
			<?php echo $infoMere['denomination'] ?>
		</a>
	</td>
</tr>
<?php endif;?>
</table>
</div>

<?php if ($info['type'] != Entite::TYPE_FOURNISSEUR ) : ?>
<div class="box_contenu">
<h2>Liste des entités filles</h2>
	<a href="entite/nouveau.php?entite_mere=<?php echo $info['siren']?>">
		<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
		Nouveau
	</a>
	<?php if ($filles) : ?>
		<ul>
			<?php foreach($filles as $fille) : ?>
				<li>
					<a href='entite/detail.php?siren=<?php echo $fille['siren']?>'>
						<?php echo $fille['denomination']?>
					</a>
				</li>
			<?php endforeach;?>
		</ul>
	<?php else : ?>
		<p>Cette entité n'a pas d'entité fille</p>
	<?php endif;?>
</div>
<?php endif;?>

<div class="box_contenu">
<h2>Liste des utilisateurs</h2>
<?php if ($info['type'] != Entite::TYPE_FOURNISSEUR ) :?>
	<a href="utilisateur/nouveau.php?siren=<?php echo $info['siren']?>">
		<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
		Nouveau
	</a>
<?php endif;?>
<table class='<?php echo $info['type'] != Entite::TYPE_FOURNISSEUR?"tab_02":"tab_03" ?>'>
<tr>
	<th>Nom</th>
	<th>Prénom</th>
	<th>login</th>
	<th>email</th>
	<th>Role</th>
	
</tr>
<?php foreach($utilisateurListe->getUtilisateurByEntite($info['siren']) as $user) : ?>
	<tr>
		<td><?php echo $user['nom']?></td>
		<td><?php echo $user['prenom']?></td>
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
