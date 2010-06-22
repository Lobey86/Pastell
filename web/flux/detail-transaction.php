<?php
require_once("../init-authenticated.php");
require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionSQL.class.php");
require_once( PASTELL_PATH ."/lib/flux/Flux.class.php");
require_once( PASTELL_PATH ."/lib/transaction/message/MessageSQL.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");

require_once( PASTELL_PATH . "/include/TransactionEtatBouton.class.php");
require_once( PASTELL_PATH . "/lib/flux/message/MessageFactory.class.php");

$flux = new Flux();


$recuperateur = new Recuperateur($_GET);

$id_t = $recuperateur->get('id_t');


$transactionSQL = new TransactionSQL($sqlQuery,$id_t);
if ($infoEntite && $infoEntite['type'] == Entite::TYPE_FOURNISSEUR){
	$transactionSQL->restrictInformation($infoEntite['siren']);
}

$info = $transactionSQL->getInfo();	
$page_title = "[".FluxFactory::getTitre($info['type'])."] " . $info['objet'];

/*
$nouveau_bouton_url = "flux/affiche-flux.php?flux=".$theFlux;
$nouveau_bouton_lib = "« Retour liste des messages";
*/


$messageSQL = new MessageSQL($sqlQuery);
$lesMessages = $messageSQL->getMessageFromTransaction($id_t);

include( PASTELL_PATH ."/include/haut.php");
?>
<p><a href='flux/affiche-flux.php?flux=<?php echo $info['type']?>'>« Liste des messages </a></p>


<?php foreach($lesMessages as $message) : ?>
<div class="box_contenu clearfix">

	<div class="box_message">
	
	<?php echo $message['type']?> | Le <?php echo $message['date_envoie']?>
	<hr />

	<table class="align_left">
	<tr>
	<th class="w30">De :</th>
	<td>
		<a href='entite/detail.php?siren=<?php echo $message['siren']?>'>
		<?php  echo $message['denomination'] ?>
		</a>
	</td>
	</tr><tr>
	<th>A :</th>
	<td>
		<?php foreach($messageSQL->getDestinataire($message['id_m']) as $i => $destinataire):?>
			 <?php if ($i!=0) echo ","; ?>
			 <a href='entite/detail.php?siren=<?php echo $destinataire['siren']?>'>
			 	<?php echo $destinataire['denomination']?>
			 </a>
		<?php endforeach;?>
	</td>
	</tr>
	</table>


	
	
	<div class="bloc_message">
	<?php echo $message['message']?>
	</div>

		<div class="box_piecejointe">
			<strong>Pièces jointes :</strong><br/>
			<?php foreach ($messageSQL->getRessource($message['id_m']) as $etat) :
				if ($etat['type'] == 'file'): ?>
					<a href='recuperation-fichier.php?id=<?php echo $etat['id_r']?>'><?php echo $etat['original_name'] ?></a>
					
					(<?php echo$etat['type'] ?>)
				
				<?php elseif ($etat['type'] != DonneesFormulaire::TYPE_RESSOURCE_FORMULAIRE_ATTACHEMENT):
		
					$donneesFormulaire = new DonneesFormulaire($etat['ressource']);
					$fileForm = basename($donneesFormulaire->get("formulaire_definition"));
					?>
					<a href='flux/detail-formulaire.php?id=<?php echo $etat['id_r']?>'><?php echo $fileForm ?></a>
					(<?php echo $etat['type'] ?>)
				<?php endif; ?>
			<?php endforeach;  ?>
		</div>
	</div>
	
</div>


<?php 
$ok0 = false;
$messageType = MessageFactory::getInstance($message['type']);
		foreach ($messageType->getMessageReponse() as $reponsePossible){
			$messageReponse = MessageFactory::getInstance($reponsePossible);
			if ($messageReponse->canCreate($infoEntite['type'])) :?>
				
				<a href='flux/nouveau.php?id_m=<?php echo $message['id_m']?>&message_type=<?php echo $messageReponse->getType()?>' 
				
				
						class='<?php echo $messageReponse->getType() != 'inscription_accepter' ?'btn_pas_ok':'btn_ok' ?>'
						
						>
					<?php echo $messageReponse->getLienResponse(); ?>
				</a>&nbsp;
				
			<?php 
			$ok0 = true;
			endif;
		}
?>	
		<?php endforeach;  ?>
		<?php 
		//TODO
		if($ok0) : ?>
		<br/><br/>
		<?php endif;?>
		
<div class="box_contenu clearfix">

<h2>Historique de la transaction</h2>
<table class='tab_01'>
	<tr>
		<th>Date</th>
		<th>Etat</th>
		<th>Preuve</th>
	</tr>
<?php
$cpt = 1;
foreach ($transactionSQL->getEtat() as $etat) : 
	$cpt++;
	?>
	<tr class='<?php echo $cpt%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php echo $etat['date'] ?></td>
		<td><?php echo $etat['etat'] ?></td>
		<td><a href='journal/preuve.php'>preuve</a></td>
	</tr>
<?php endforeach;?>
</table>
</div>



<div class="box_contenu clearfix">

<h2> Rôles </h2>
<table class='tab_02'>
	<tr>
		<th>Rôle</th>
		<th>Entité</th>
	</tr>
<?php 
$cpt = 1;
foreach ($transactionSQL->getAllRole() as $etat) : 
	$cpt++;
	?>
	<tr class='<?php echo $cpt%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php echo $etat['role'] ?></td>
		<td><a href='entite/detail.php?siren=<?php echo $etat['siren'] ?>'><?php echo $etat['denomination'] ?></a> (<?php echo $etat['siren'] ?>)</td>
	</tr>
<?php endforeach;?>
</table>
</div>


<?php 
include( PASTELL_PATH ."/include/bas.php");