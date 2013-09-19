<div class="box_contenu clearfix">

<h2>Filtre du journal </h2>

<form class="w700" action='journal/export-controler.php' method='post'>
	<input type='hidden' name='id_e' value='<?php hecho($id_e)?>'>
	<input type='hidden' name='id_d' value='<?php hecho($id_d)?>'>
	<input type='hidden' name='id_u' value='<?php hecho($id_u)?>'>
	<input type='hidden' name='type' value='<?php hecho($type)?>'>
	<table>
		<tr>
			<th>Entité</th>
			<td><?php hecho($id_e?$entite_info['denomination']:"Toutes")?></td>
		</tr>
		<tr>
			<th>Utilisateur</th>
			<td><?php hecho($id_u?$utilisateur_info['login']:"Tous")?></td>
		</tr>
		<tr>
			<th>Document</th>
			<td><?php hecho($id_d?$document_info['titre']:"Tous")?></td>
		</tr>
		<tr>
			<th><label for='recherche'>Recherche</label> </th>
			 <td> <input type='text' name='recherche' value='<?php hecho($recherche) ?>' /></td>
		</tr>
		<tr>
			<th><label for='debut'>
			Date de début
			</label> </th>
			 <td>
			 	<input type='text' id='date_debut' name='date_debut' value='<?php hecho(date_iso_to_fr($date_debut))?>' size='40'/>
			 </td>
		</tr>
		<tr>
			<th><label for='debut'>
			Date de fin
			</label> </th>
			 <td> 
			 	<input type='text' id='date_fin' name='date_fin' value='<?php hecho(date_iso_to_fr($date_fin))?>' />
			 </td>
		</tr>
	</table>
	
	
	
	<input type='submit' value='Récupérer le journal'/>
	
	
</form>
</div>

<script type="text/javascript">
jQuery.datepicker.setDefaults(jQuery.datepicker.regional['fr']);
$(function() {
	$("#date_debut").datepicker( { dateFormat: 'dd/mm/yy' });
	$("#date_fin").datepicker( { dateFormat: 'dd/mm/yy' });
});
</script>