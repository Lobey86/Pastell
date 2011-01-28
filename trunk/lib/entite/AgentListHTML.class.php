<?php


class AgentListHTML {
	
	public function display(array $listAgent){
		?>
		<table class="tab_01">
			<tr>
				<th>Matricule</th>
				<th>Nom </th>
				<th>Prénom </th>
				<th>Statut</th>
				<th>Grade</th>
			</tr>
			<?php foreach ($listAgent as $i => $agent) : ?>
				<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
						
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent["matricule"] ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['nom_patronymique'] ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['prenom'] ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo "unknow" ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['emploi_grade_libelle'] ?></label></td>
					
				</tr>
			<?php endforeach;?>
		</table>		
		<?php 
	}
	
}