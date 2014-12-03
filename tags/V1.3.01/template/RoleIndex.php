
<div class='box'>
	<table class="table table-striped">
		<tr>
			<th class='w200'>Rôle</th>
			<th>Libellé</th>
		</tr>
	<?php foreach($allRole as $i => $info) : ?>
		<tr>
			<td><a href='role/detail.php?role=<?php echo  $info['role'] ?>'><?php hecho($info['role']) ?></a></td>
			<td><?php 
			echo $info['libelle'] ?></td>
		</tr>
	<?php endforeach; ?>
	</table>
</div>
	


