<a class='btn btn-mini' href='system/index.php?page_number=4'>
	<i class='icon-circle-arrow-left'></i>Liste des extensions
</a>



<div class="box">


<form action='system/extension-controler.php' method='post' >
<input type='hidden' name='id_e' value='<?php hecho($extension_info['id_e'])?>' />
<table class='table table-striped'>
<tr>
	<th class="w200"><label for='login'>
	Emplacement de l'extension (chemin absolu)
	<span class='obl'>*</span></label> </th>
	<td> <input style='width:500px' type='text' name='path' value='<?php hecho($extension_info['path'])?>' /></td>
</tr>
</table>
<input type='submit' class='btn' value="<?php echo $extension_info['id_e']?"Modifier":"Ajouter" ?>" />

</form>
</div>