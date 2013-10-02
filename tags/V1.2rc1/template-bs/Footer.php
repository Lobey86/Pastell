<?php 
$elapsedTime = round($this->Timer->getElapsedTime(),3);
$infoVersionning = $this->Versionning->getAllInfo();
?>
<div id="bottom">
	<div class="bloc_vers_haut">Page générée en <?php echo $elapsedTime ?>s</div>
	
	<div class="bloc_copyright">
		<div class="bloc_mentions">
			<p>	<a href='https://adullact.net/projects/pastell/'>Pastell</a> <?php echo $infoVersionning['version-complete'] ?> - Copyright <a href='http://www.sigmalis.com'>Sigmalis</a> 2010/2011 
			<br/> Logiciel distribué sous les termes de la licence <a href='http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html'>CeCiLL V2</a> </p>
		</div>
		<div class="bloc_logo_adullact">
			<a href='http://www.adullact.org/'><img src="img/commun/logo_adullact.png" alt="Adullact" /></a>
		</div>
	</div>
</div>
