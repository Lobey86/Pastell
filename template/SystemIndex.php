<div id="bloc_onglet">
	<?php foreach ($onglet_tab as $onglet_number => $onglet_name) : ?>
		<a href='system/index.php?page_number=<?php echo $onglet_number?>' <?php echo ($onglet_number == $page_number)?'class="onglet_on"':'' ?>>
			<?php echo $onglet_name?>
		</a>
	<?php endforeach;?>
</div>
<?php $this->render($onglet_content);?>
