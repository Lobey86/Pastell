<?php 

$page_title = "Choix d'un agent";
include( PASTELL_PATH ."/include/haut.php");

require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$fileName = PASTELL_PATH . "/data-exemple/agent.csv";
if (! file_exists($fileName)){
	exit;
}

$dataFile = explode("\n",file_get_contents($fileName));
$agent = array();
foreach($dataFile as $ligne){
	if (! $ligne){
		continue;
	}
	$l = explode(",",$ligne);	
	$agent[$l[2]] = $l;	
}


ksort($agent);
$agent = array_values($agent);
$offset = $recuperateur->getInt('offset',0);
$limit = 20;



?>
<a href='document/edition.php?id_d=<?php echo $id_d ?>&id_e=<?php echo $id_e?>&page=<?php echo $page ?>'>« Revenir à l'édition du document <em><?php echo $titre?></em></a>
<br/><br/>


<?php 
suivant_precedent($offset,$limit,count($agent),"document/external-data.php?id_e=$id_e&id_d=$id_d&page=$page&field=$field");
?>


<div class="box_contenu clearfix">
<h2>Agent</h2>

<form action='document/external-data-controler.php' method='post'>
	<input type='hidden' name='id_d' value='<?php echo $id_d?>' />
	<input type='hidden' name='id_e' value='<?php echo $id_e?>' />
	<input type='hidden' name='page' value='<?php echo $page?>' />
	<input type='hidden' name='field' value='<?php echo $field?>' />

<table class="tab_01">
	<tr>
		<th>&nbsp;</th>
		<th>Matricule</th>
		<th>Nom </th>
		<th>Prénom </th>
		<th>Statut</th>
		<th>Grade</th>
	</tr>
	<?php for ($i=$offset; $i<$offset+$limit;$i++) : 
		if (! isset($agent[$i])){
			break;
		}
	?>
		<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
			<td class="w30">
				
			<input type='radio' name='agent[]' id="label_agent_<?php echo $i ?>" value='<?php echo $agent[$i][0]?>'/></td>
			<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent[$i][0] ?></label></td>
			<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent[$i][2] ?></label></td>
			<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent[$i][1] ?></label></td>
			<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent[$i][3] ?></label></td>
			<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent[$i][4] ?></label></td>
			
		</tr>
	     
	<?php endfor;?>
</table>
<div class="align_right">
<input type='submit' value='Choisir' class='submit' />
</div>
</form>
</div>

<?php 
include( PASTELL_PATH ."/include/bas.php");
