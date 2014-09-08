
<a class='btn btn-mini' href='document/list.php?type=<?php echo $info['type']?>&id_e=<?php echo $id_e?>&last_id=<?php echo $id_d ?>'>
<i class="icon-circle-arrow-left"></i>Liste des "<?php echo $documentType->getName() ?>" de <?php echo $infoEntite['denomination']?></a>


<?php if ($donneesFormulaire->getNbOnglet() > 1): ?>
		<ul class="nav nav-pills" style="margin-top:10px;">
			<?php foreach ($donneesFormulaire->getOngletList() as $page_num => $name) : ?>
				<li <?php echo ($page_num == $page)?'class="active"':'' ?>>
					<a href='<?php echo "document/detail.php?id_d=$id_d&id_e=$id_e" ?>&page=<?php echo $page_num?>'>
					<?php echo $name?>
					</a>
				</li>
			<?php endforeach;?>
		</ul>
<?php endif; ?>
	
<div class="box">

<?php 
$this->render("DonneesFormulaireDetail");
?>


<table>
<tr>
<?php foreach($actionPossible->getActionPossible($id_e,$authentification->getId(),$id_d) as $action_name) : ?>
<td>
<form action='document/action.php' method='post' >
	<input type='hidden' name='id_d' value='<?php echo $id_d ?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='<?php echo $page ?>' />
	
	<input type='hidden' name='action' value='<?php echo $action_name ?>' />
	
	<input type='submit' class='btn <?php if ($action_name=="supression")  echo 'btn-danger'; ?>' value='<?php hecho($theAction->getDoActionName($action_name)) ?>'/>&nbsp;&nbsp;
</form>
</td>
<?php endforeach;?>
</tr>
</table>

</div>

<?php if ($next_action_automatique) : ?>
<div class='box'>
<h2>Action automatique</h2>
<table class="table table-striped">
		<tr>
			<th>Action programmée sur le document</th>
			<td><?php hecho($theAction->getActionName($next_action_automatique))?></td>
		</tr>
</table>
<?php if($droit_erreur_fatale) : ?>
<form action='document/action.php' method='post' >
	<input type='hidden' name='id_d' value='<?php echo $id_d ?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='<?php echo $page ?>' />
	<input type='hidden' name='action' value='fatal-error' />
	
	<input type='submit' class='btn btn-danger' value='Déclencher une erreur fatale sur le document'/>&nbsp;&nbsp;
</form>
<?php endif;?>
</div>
<?php endif;?>

<div class="box">
<h2>Entité concernée par le document</h2>

<table class="table table-striped">
		<tr>
			<th class="w200">Entité</th>
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

</table>
</div>

<?php 
$infoDocumentEmail = $documentEmail->getInfo($id_d);
if ($infoDocumentEmail) : 
?>
<div class="box">
<h2>Utilisateurs destinataires du message</h2>

<table class="table table-striped">
		<tr>
			<th class="w200">Email</th>
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
</table>
</div>


<?php endif;?>


<div class="box">
<h2>États du document</h2>

<table class="table table-striped">

		<tr>
			<th class="w200">État</th>
			<th class="w200">Date</th>
			<th class="w200">Entité</th>
			<th class="w200">Utilisateur</th>
			<th>Journal</th>
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
				<td>
					<?php if($action['id_j']) : ?>
					<a href='journal/detail.php?id_j=<?php echo $action['id_j']?>'>voir</a>
					<?php endif;?>
				</td>
			</tr>
		<?php endforeach;?>

</table>
</div>

<a class='btn btn-mini' href='journal/index.php?id_e=<?php echo $id_e?>&id_d=<?php echo $id_d?>'><i class='icon-list'></i>Voir le journal des évènements</a>

