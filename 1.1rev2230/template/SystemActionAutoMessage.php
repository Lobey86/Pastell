<a href='system/index.php'>« Revenir à la liste des actions automatiques</a>
<br/><br/>
<div class="box_contenu clearfix">
<h2>Message pour le document </h2>


<table class="tab_01">
<tbody>
<tr>
	<th>Date</th>
	<th>Message</th>
</tr>
<?php foreach($all_message as $i => $message) : ?>
	<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
		<td><?php hecho($message['date'])?></td>
		<td><?php hecho($message['message'])?></td>
	</tr>
<?php endforeach;?>
</tbody>
</table>

</div>