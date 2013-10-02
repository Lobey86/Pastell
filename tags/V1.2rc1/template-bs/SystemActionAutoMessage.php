<a class='btn btn-mini' href='system/index.php'><i class='icon-circle-arrow-left'></i>Revenir à la liste des actions automatiques</a>

<div class="box">
<h2>Message pour le document </h2>

<table class="table table-striped">
<tbody>
<tr>
	<th class='w200'>Date</th>
	<th>Message</th>
</tr>
<?php foreach($all_message as $i => $message) : ?>
	<tr>
		<td><?php hecho($message['date'])?></td>
		<td><?php hecho($message['message'])?></td>
	</tr>
<?php endforeach;?>
</tbody>
</table>

</div>