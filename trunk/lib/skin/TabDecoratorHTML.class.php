<?php
class TabDecoratorHTML {
	
	public function display(array $tab,$page_url,$tab_selected){ ?>
				
		<div id="bloc_onglet">
		<?php foreach ($tab as $page_num => $name) : ?>
					<a href='<?php echo $page_url ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_selected)?'class="onglet_on"':'' ?>>
					<?php echo $name?>
					</a>
		<?php endforeach;?>
		</div>
		
	<?php 
		
	}
	
}