<?php 

class FormulaireRenderer {
	
	const TABLE_CLASS = 'tab_01';
	
	const BUTTON_CLASS = 'send_button';
	
	const TABLE_MODIF_CLASS = '';
	
	public function showOnglet($tab,$tab_selected,$page_url){ ?>
	<div id="bloc_onglet">
		<?php foreach ($tab as $page_num => $name) : ?>
					<a href='<?php echo $page_url ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_selected)?'class="onglet_on"':'' ?>>
					<?php echo $name?>
					</a>
		<?php endforeach;?>
		</div>
	<?php 
	}
	
	public function showOngletStatic($tab,$page){ ?>
		
		<div id="bloc_onglet">
		<?php foreach ($tab as $page_num => $name) : ?>
					<a <?php echo ($page_num == $page)?'class="onglet_on"':'' ?>>
					<?php echo ($page_num + 1) . ". " . $name?>
					</a>
		<?php endforeach;?>
		</div>
		
		<?php
		
	} 
	
	public function formEntete($i,$libelle){
		?>

			<tr class='<?php echo $i%2?'bg_class_gris':'bg_class_blanc'?>'>
					<td>
						<?php echo $libelle ?>
					</td>
		<?php 
	}
	
	public function alert($message){ ?>
		<div class="box_error">
					<p>
						 <?php  echo $message ?>
					</p>
						
				</div>
	<?php 
	}
	
	
}