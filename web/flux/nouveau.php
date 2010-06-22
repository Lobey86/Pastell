<?php
require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( ZEN_PATH . "/lib/Recuperateur.class.php");
require_once( ZEN_PATH . "/lib/PasswordGenerator.class.php");

require_once( PASTELL_PATH . "/lib/transaction/message/MessageSQL.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionSQL.class.php");
require_once( PASTELL_PATH . "/lib/entite/EntiteListe.class.php");
require_once( PASTELL_PATH ."/lib/flux/FluxFactory.class.php");
require_once( PASTELL_PATH ."/lib/flux/message/MessageFactory.class.php");
require_once( PASTELL_PATH . "/lib/transaction/TransactionCreator.class.php");

require_once (PASTELL_PATH . "/lib/formulaire/Formulaire.class.php");
require_once( PASTELL_PATH . "/lib/formulaire/DonneesFormulaire.class.php");
require_once( PASTELL_PATH . '/lib/formulaire/AfficheurFormulaire.class.php');


$recuperateur = new Recuperateur($_REQUEST);

$flux = $recuperateur->get('flux');
$id_m = $recuperateur->get('id_m');
$message_type = $recuperateur->get('message_type');
$destinataire = $recuperateur->get('destinataire');

$id_t = "";

if ($flux){
	$theFlux = FluxFactory::getInstance($flux);
	$message = $theFlux->getMessageInit();
} else {
	
	$messageSQL = new MessageSQL($sqlQuery);
	$infoMessage = $messageSQL->getInfo($id_m);
	$id_t = $infoMessage['id_t'];
	$transactionSQL = new TransactionSQL($sqlQuery,$infoMessage['id_t']);
	$transactionInfo= $transactionSQL->getInfo();
	$theFlux = FluxFactory::getInstance($transactionInfo['type']);
	
	$message = MessageFactory::getInstance($message_type);	
	
	if ($infoMessage['emetteur'] == $infoEntite['siren']){
		$destinataire = $messageSQL->getDestinataire($id_m);
		foreach($destinataire as $r){
			//TODO UGLY
			$result[] = $r['siren'];
		}
		$destinataire = $result;
	} else {
		$destinataire = array($infoMessage['emetteur']);
	}
}

$entiteListe = new EntiteListe($sqlQuery);
if ($destinataire){
	$infoEntite2 = $entiteListe->getInfoFromArray($destinataire);
} 

if ($id_m){
	$page_title = "Réponse au message «" . $transactionInfo['objet'] . "»"; 
} else {
	$page_title="Création d'une nouvelle transaction « " . $theFlux->getFluxTitre() . " »";
}
include( PASTELL_PATH ."/include/haut.php");
?>

<?php if ($message->getDescription()) : ?>
<div class='box_info'>
	<p><?php echo $message->getDescription();?></p>
</div>
<?php endif;?>

<?php if ($id_m) : ?>
<a href='flux/detail-transaction.php?id_t=<?php echo $transactionInfo['id_t']?>'>Revenir au message</a>
<br/><br/>
<?php endif;?>

<div class="box_contenu clearfix">

<?php if ($message->getFormulaire()) : 
$formulaire_file = $message->getFormulaire();
$formulaire = new Formulaire( PASTELL_PATH ."/form/".$formulaire_file);

if (!$id_t){
	$transactionCreator = new TransactionCreator($sqlQuery,new PasswordGenerator());
	$id_t = $transactionCreator->getNewTransactionNum();
}

$donneesFormulaire = new DonneesFormulaire( WORKSPACE_PATH . "/$id_t.yml");
$donneesFormulaire->setFormulaire($formulaire);

$afficheurFormulaire = new AfficheurFormulaire($formulaire,$donneesFormulaire);

$afficheurFormulaire->injectHiddenField('flux',$flux);
$afficheurFormulaire->injectHiddenField('message_type', $message->getType() );
$afficheurFormulaire->injectHiddenField('id_t',$id_t);


$afficheurFormulaire->affiche(0,"flux/nouveau-controler.php");
?>
<?php else : ?>

<form action='flux/nouveau-controler.php' method='post' enctype="multipart/form-data">
<input type='hidden' name='flux' value='<?php echo $flux ?>' />
<input type='hidden' name='message_type' value='<?php echo $message->getType() ?>' />
<input type='hidden' name='id_m' value='<?php echo $id_m ?>' />
<input type='hidden' name='id_t' value='<?php echo $id_t ?>' />

<table>
<tr>
<th><?php echo  Entite::getNom($message->getTypeDestinataire()); ?>
<?php if ($message->hasMultipleDestinataire()) : ?>
	<br/>
	<a href='entite/<?php echo $message->getTypeDestinataire()?>.php'>Choisir plusieurs <?php echo $message->getTypeDestinataire()?></a>
<?php endif;?>
</th>
<td>
<?php if ($destinataire) : ?>
	<?php foreach($infoEntite2 as $info) : ?>
		
		<input type='hidden' name='destinataire[]' value='<?php echo $info['siren']?>' />
		<a href='entite/detail.php?siren=<?php echo $info['siren']?>'>
			<?php echo $info['denomination'] ?>
		</a>
		 (<?php echo $info['siren'] ?>) <br/>
	<?php endforeach;?>
<?php else :?>
<select name='destinataire[]'>
<option >...</option>
<?php foreach($entiteListe->getAll($message->getTypeDestinataire()) as $entiteDestinataire) : ?>
	<option value='<?php echo $entiteDestinataire['siren']?>'>
		<?php echo $entiteDestinataire['denomination']?>(<?php echo $entiteDestinataire['siren']?>)
		</option>
<?php endforeach;?>
</select>
<?php endif;?>
</td>
</tr>
<tr>
	<th>Objet</th>
	<td>
	<?php if (isset($transactionInfo['objet'])) : ?>
		<?php echo $transactionInfo['objet']?>
	<?php else : ?>
		<input type='text' name='objet'/>
	<?php endif;?>
	</td>
</tr>
<tr>
	<th>Message</th>
	<td><textarea name='message'/></textarea>
</tr>
<tr>
	<th>Pièce jointe</th>
	<td><input type='file' name='devis'/></td>
</tr>

</table>
<div class="align_right">
<input type='submit' value='Créer' class='submit' />
</div>

</form>
<?php endif; ?>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
