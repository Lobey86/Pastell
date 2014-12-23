<?php 

class FormulaireRenderer {
	
	const TABLE_CLASS = 'table table-striped';
	
	const BUTTON_CLASS = 'btn';
	
	const TABLE_MODIF_CLASS = 'table table-striped';
	
	
	public function showOnglet($tab,$tab_selected,$page_url){ ?>
	
		<ul class="nav nav-pills" style="margin-top:10px;">
			<?php foreach ($tab as $page_num => $name) : ?>
				<li <?php echo ($page_num == $tab_selected)?'class="active"':'' ?>>
					<a href='<?php echo $page_url ?>&page=<?php echo $page_num?>'>
					<?php echo $name?>
					</a>
				</li>
			<?php endforeach;?>
		</ul>
	<?php 
	}
	
	public function showOngletStatic($tab,$page){ ?>
		<ul class="nav nav-pills" style="margin-top:10px;">
			<?php foreach ($tab as $page_num => $name) : ?>
				<li <?php echo ($page_num == $page)?'class="active"':'' ?>>
					<a>
					<?php echo ($page_num + 1) . ". " . $name?>
					</a>
				</li>
			<?php endforeach;?>
		

		</ul>
		<?php
	} 

	public function formEntete($i,$libelle){
		?>
					<tr>
					<th class="w300">
						<?php echo $libelle ?>
					</th>
	<?php 
	}
	
	public function alert($message){ ?>
		<div class="alert alert-error">
				<?php  echo $message; ?>
			</div>
	<?php 
	}
	
}
