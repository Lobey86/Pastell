
<?php if ($id_e) : ?>
<a href='entite/detail.php'>« Administration</a>
<?php endif; ?>
<br/><br/>

<div id="bloc_onglet">
	<?php foreach ($formulaire_tab as $page_num => $name) : ?>
		<a href='entite/detail.php?id_e=<?php echo $id_e ?>&page=<?php echo $page_num?>' <?php echo ($page_num == $tab_number)?'class="onglet_on"':'' ?>>
			<?php echo $name?>
		</a>
	<?php endforeach;?>
</div>
<div class="box_contenu clearfix">

<?php 
if ($tab_number == 0) :
	if ($id_e){ 
		$this->EntiteControler->detailEntite();
	} else {
		$this->EntiteControler->listEntite();
	}
elseif($tab_number == 1) : 
	$this->EntiteControler->listUtilisateur();
elseif($tab_number == 2) :
	$this->AgentControler->listAgent();
elseif($tab_number==3) :
	$this->ConnecteurControler->listConnecteur();
elseif($tab_number == 4 ) :
	$this->FluxControler->listFlux();
elseif($tab_number == 5 ): 
		?><a href='mailsec/annuaire.php'>Annuaire global »</a><?php 
	
 endif;
 
 
 ?>

</div>

