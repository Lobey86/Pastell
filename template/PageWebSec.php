<?php 
$elapsedTime = round($this->Timer->getElapsedTime(),3);
$infoVersionning = $this->Versionning->getAllInfo();

header("Content-type: text/html; charset=iso-8859-15");	 ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo($page_title) . " - Pastell"; ?></title>
		
		<meta name="description" content="Pastell est un logiciel de gestion de flux de documents. Les documents peuvent être crées via un système de formulaires configurables. Chaque document suit alors un workflow prédéfini, également configurable." />
		<meta name="keywords" content="Pastell, collectivité territoriale, flux, document, données, logiciel, logiciel libre, open source" />
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-15" />
		
		<base href='<?php echo WEBSEC_BASE ?>' />
		
		<link rel="shortcut icon" type="images/x-icon" href="favicon.ico" />
		<link rel="stylesheet" type="text/css" href="img/commun.css" media="screen" />
		<!--[if gte IE 6]>
			<link rel="stylesheet" type="text/css" href="img/style_IE6.css" media="screen" />
		<![endif]-->

		
			
	</head>
	<body>
		<div id="global">
			<div id="header">
				<div id="bloc_logo">
					<a href='<?php echo  WEBSEC_BASE ?>'>
						<img src="img/commun/logo_pastell.png" alt="Retour à l'accueil" />
					</a>
				</div>
			
			</div>
	
				
			<div id="breadcrumb">
				<img src="img/commun/puce_geographie.png" alt="" class="absmiddle" />
			
					Bienvenue
			
			</div>
		
		
			<div id="main" class="clearfix">	
			
					
				<div id="main_droite" >
					<div id="bloc_titre_bouton">
						<div id="bloc_h1">
						<h1><?php echo($page_title); ?></h1>
						</div>
				
					</div><!-- fin bloc_titre_bouton -->

					<?php $this->render("LastMessage");?>
					<?php $this->render($template_milieu);?>

				</div>
			</div>
		</div>
			<div id="bottom">
			<div id="bloc_vers_haut">
			Page générée en <?php echo $elapsedTime ?>s
			</div>
			
			<div id="bloc_copyright">
				<div id="bloc_mentions">
					<p>	<a href='https://adullact.net/projects/pastell/'>Pastell</a> <?php echo $infoVersionning['version-complete'] ?> - Copyright <a href='http://www.sigmalis.com'>Sigmalis</a> 2010/2011 
					<br/> Logiciel distribué sous les termes de la licence <a href='http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html'>CeCiLL V2</a> </p>
				</div>
				<div id="bloc_logo_adullact">
					<a href='http://www.adullact.org/'><img src="img/commun/logo_adullact.png" alt="Adullact" /></a>
				</div>
			</div>
		</div>
	</body>
</html>
<?php 
