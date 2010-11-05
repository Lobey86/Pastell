<?php

require_once(dirname(__FILE__)."/../init-authenticated.php");

require_once( PASTELL_PATH ."/lib/journal/Journal.class.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");


$recuperateur = new Recuperateur($_GET);
$id_j = $recuperateur->getInt('id_j',0);


$info = $journal->getAllInfo($id_j);

if  (! $roleUtilisateur->hasDroit($authentification->getId(),"journal:lecture",$info['id_e'])){
	header("Location: index.php");
	exit;
}

$page_title="Evenement num�ro $id_j";
include( PASTELL_PATH ."/include/haut.php");
?>

<div class="box_contenu clearfix">

<h2>D�tail de l'�venement <?php echo $id_j ?></h2>

<table class="tab_04">
<tr>
		<th>Num�ro</th>
		<td><?php echo $id_j ?></td>
</tr>
<tr>
		<th>Date</th>
		<td><?php echo $info['date'] ?></td>
</tr>
<tr>		
		<th>Type</th>
		<td><?php echo $journal->getTypeAsString($info['type']) ?></td>
</tr>
<tr>
		<th>Entit�</th>
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
		<th>Message horodat�: </th>
		<td><?php echo  $info['message_horodate']?></td>
	</tr>
<tr>
		<th>Date et heure de l'horodatage: </th>
		<td><?php echo  $info['date_horodatage']?></td>
</tr>
<tr>
		<th>Preuve </th>
		<td>
			<a href='journal/preuve.php?id_j=<?php echo $id_j?>'>T�l�charger</a><br/><br/>
			<pre>
				<?php echo  $opensslTSWrapper->getTimestampReplyString($info['preuve']) ?>
			</pre>		
		</td>
</tr>
</table>
</div>
<?php 
include( PASTELL_PATH ."/include/bas.php");
