<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once (PASTELL_PATH . "/lib/helper/date.php");


$recuperateur = new Recuperateur($_GET);
$id_j = $recuperateur->getInt('id_j',0);

$offset = $recuperateur->getInt('offset',0);
$id_e = $recuperateur->getInt('id_e',0);
$type = $recuperateur->get('type');
$id_d = $recuperateur->get('id_d');


$info = $journal->getAllInfo($id_j);

if  (! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$info['id_e'])){
	header("Location: index.php");
	exit;
}

$page_title="Evenement numéro $id_j";
include( PASTELL_PATH ."/include/haut.php");
?>
<a href='journal/index.php?id_e=<?php echo $id_e?>&id_d=<?php echo $id_d?>&type=<?php echo $type?>&offset=<?php echo $offset ?>'>« Retour au journal </a>
<br/><br/>
<div class="box_contenu clearfix">

<h2>Détail de l'évenement <?php echo $id_j ?></h2>

<table class="tab_04">
<tr>
		<th>Numéro</th>
		<td><?php echo $id_j ?></td>
</tr>
<tr>
		<th>Date</th>
		<td><?php echo time_iso_to_fr($info['date']) ?></td>
</tr>
<tr>		
		<th>Type</th>
		<td><?php echo $journal->getTypeAsString($info['type']) ?></td>
</tr>
<tr>
		<th>Entité</th>
		<td><a href='entite/detail.php?id_e=<?php echo $info['id_e'] ?>'><?php echo  $info['denomination']?></a></td>
		</tr>
<tr>
		<th>Utilisateur</th>
		<td><a href='utilisateur/detail.php?id_u=<?php echo  $info['id_u']?>'><?php echo $info['prenom'] . " " . $info['nom']?></a>
		</td>
		</tr>
<tr>
		<th>Document</th>
		<td>
			<a href='document/detail.php?id_d=<?php echo $info['id_d']?>&id_e=<?php echo $info['id_e']?>'> 
				<?php echo $info['titre']?>
			</a>
		</td>
		</tr>
<tr>
		<th>Action</th>
		<td><?php echo  $info['action']?></td>
</tr>
<tr>
		<th>Message</th>
		<td><?php echo  $info['message']?></td>
		</tr>
<tr>
		<th>Message horodaté: </th>
		<td><?php echo  $info['message_horodate']?></td>
	</tr>
<tr>
		<th>Date et heure de l'horodatage: </th>
		<td><?php echo  $info['date_horodatage']?></td>
</tr>
<tr>
		<th>Preuve </th>
		<td>
			<a href='journal/preuve.php?id_j=<?php echo $id_j?>'>Télécharger</a><br/><br/>
			<pre>
				<?php echo  $opensslTSWrapper->getTimestampReplyString($info['preuve']) ?>
			</pre>		
		</td>
</tr>
<tr>
		<th>Vérification</th>
		<td>
			<?php if ($opensslTSWrapper->verify($info['message_horodate'],$info['preuve'], SIGN_SERVER_CA_CERTIFICATE, SIGN_SERVER_CERTIFICATE)) :?>
				OK
			<?php else : ?>
				<?php echo  $opensslTSWrapper->getLastError() ?>
			<?php endif;?>
		</td>
</tr>
</table>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
