<?php


class AgentListHTML {
	
	public function __construct($displaySIREN = false){
		$this->displaySIREN = $displaySIREN;
	}
	

	
	public function display(array $listAgent){
		?>
		<table class="tab_01">
			<tr>
			
				<th>Matricule</th>
				<th>Nom </th>
				<th>Prénom </th>
				<th>Statut</th>
				<th>Grade</th>
					<?php if ($this->displaySIREN) : ?>
					<th>Collectivité</th>
				<?php endif;?>
			</tr>
			<?php foreach ($listAgent as $i => $agent) : ?>
				<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
					
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent["matricule"] ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['nom_patronymique'] ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['prenom'] ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo "unknow" ?></label></td>
					<td><label for="label_agent_<?php echo $i ?>"><?php echo $agent['emploi_grade_libelle'] ?></label></td>
					<?php if ($this->displaySIREN) : ?>
						<td><a href='entite/detail.php?id_e=<?php echo $agent['id_e']?>&page=2'><?php echo $agent['denomination']?></a></td>
					<?php endif;?>
				</tr>
			<?php endforeach;?>
		</table>		
		<?php 
	}
	
}