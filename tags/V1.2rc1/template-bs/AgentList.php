<table style='width:100%;'>
<tr>
<td>
<h2>Liste des agents</h2>
</td>

<?php if ($droit_edition) : ?>
<td class='align_right'>
<a href="entite/import.php?id_e=<?php echo $id_e?>&page=1&page_retour=2" class='btn'>Importer</a>
</td>
<?php endif;?>

</tr>
</table>


<form action='entite/detail.php' method='get' class="form-inline">
	<input type='hidden' name='id_e' value='<?php echo $id_e ?>' />
	<input type='hidden' name='page' value='<?php echo $page ?>' />
	<input type='text' name='search' value='<?php echo $search?>'/>
	<button type='submit' class='btn'><i class='icon-search'></i>Rechercher</button>
</form>



<?php $this->SuivantPrecedent($offset,AgentSQL::NB_MAX,$nbAgent,"entite/detail.php?id_e=$id_e&page=$page&search=$search"); ?>
<?php if ($id_ancetre != $id_e): ?>
<div class='alert'>
	Informations héritées de <a href='entite/detail.php?id_e=<?php echo $id_ancetre?>'><?php echo $infoAncetre['denomination']?></a>
</div>
<?php endif;?>
<table class="table table-striped">
		<tr>
			<th>Matricule</th>
			<th>Nom </th>
			<th>Prénom </th>
			<th>Grade</th>
				<?php if ($id_e == 0) : ?>
				<th>Collectivité</th>
			<?php endif;?>
		</tr>
		<?php foreach ($listAgent as $i => $agent) : ?>
			<tr>
				
				<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent["matricule"] ?></label></td>
				<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['nom_patronymique'] ?></label></td>
				<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['prenom'] ?></label></td>
				<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['emploi_grade_libelle'] ?></label></td>
				<?php if ($id_e == 0) : ?>
					<td><a href='entite/detail.php?id_e=<?php echo $agent['id_e']?>&page=2'><?php echo $agent['denomination']?></a></td>
				<?php endif;?>
			</tr>
		<?php endforeach;?>
	</table>		