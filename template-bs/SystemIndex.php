<ul class="nav nav-pills">
	<?php foreach ($onglet_tab as $onglet_number => $onglet_name) : ?>
	<li <?php echo ($onglet_number == $page_number)?'class="active"':'' ?>>
		<a href='system/index.php?page_number=<?php echo $onglet_number?>'>
			<?php echo $onglet_name?>
		</a>
	</li>
	<?php endforeach;?>
</ul>

	
<?php $this->render($onglet_content);?>
