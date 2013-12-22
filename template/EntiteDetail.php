<?php
$id_e = $entiteExtendedInfo['id_e'];
?>
<h2>Informations générales
	<?php if ($droit_edition) : ?>
	<a class='btn btn-mini' href="entite/edition.php?id_e=<?php echo $id_e?>" class='btn'>
			Modifier
		</a>
	<?php endif;?>
</h2>
<table class='table table-striped'>		
	<tr>
		<th class='w200'>Type</th>
		<td><?php echo Entite::getNom($entiteExtendedInfo['type']) ?></td>
	</tr>
	
	<tr>
		<th>Dénomination</th>
		<td><?php echo $entiteExtendedInfo['denomination'] ?></td>
	</tr>
	<?php if ($entiteExtendedInfo['siren']) : ?>
		<tr>
			<th>Siren</th>
			<td><?php echo $entiteExtendedInfo['siren'] ?></td>
		</tr>
	<?php endif;?>
	<tr>
		<th>Date d'inscription</th>
		<td><?php echo time_iso_to_fr($entiteExtendedInfo['date_inscription']) ?></td>
	</tr>
	<?php if ($entiteExtendedInfo['entite_mere']) : ?>
	<tr>
		<th>Entité mère</th>
		<td>
			<a class='btn btn-mini' href='entite/detail.php?id_e=<?php echo $entiteExtendedInfo['entite_mere']['id_e']?>'>
				<?php echo $entiteExtendedInfo['entite_mere']['denomination'] ?>
			</a>
		</td>
	</tr>
	<?php endif;?>
	<?php if ($entiteExtendedInfo['type'] != Entite::TYPE_FOURNISSEUR ) : ?>
		<tr>
		<th>Entité fille</th>
		<td>
			<?php if ( ! $entiteExtendedInfo['filles']) : ?>
				<?php echo "Cette entité n'a pas d'entité fille"?>
			<?php endif;?>
			<ul>
			<?php foreach($entiteExtendedInfo['filles'] as $fille) : ?>
				<li><a href='entite/detail.php?id_e=<?php echo $fille['id_e']?>'>
					<?php echo $fille['denomination']?>
				</a></li>
			<?php endforeach;?>
			</ul>
			<?php if ($droit_edition) : ?>
				<a class='btn btn-mini' href="entite/edition.php?entite_mere=<?php echo $id_e?>" >
					<i class='icon-plus'></i>Ajouter une entité fille
				</a>
				&nbsp;&nbsp;
				<a class='btn btn-mini' href="entite/import.php?id_e=<?php echo $id_e?>" >
					<i class='icon-file'></i>Importer des entités filles
				</a>
			<?php endif;?>
		</td>
		</tr>
	<?php endif;?>
	<?php if ($entiteExtendedInfo['cdg']) : 
		$infoCDG = $entiteExtendedInfo['cdg']; ?>
		<tr>
			<th>Centre de gestion</th>
			<td>
				<?php if ($droit_lecture_cdg ) : ?>			
					<a href='entite/detail.php?id_e=<?php echo $infoCDG['id_e']?>'>
						<?php echo $infoCDG['denomination']?>
					</a>
				<?php else : ?>
					<?php echo $infoCDG['denomination']?>
				<?php endif; ?>
				
				</td>
		</tr>
	<?php endif;?>
		
	<tr>
		<th>GED</th>
		<td><?php echo $has_ged ?></td>
	</tr>
		
	<tr>
		<th>SAE</th>
		<td><?php echo $has_sae ?></td>
	</tr>
		
</table>

