<?php 


if (! isset($nouveau_bouton_url)){
	$nouveau_bouton_url = array();
}
if (! is_array($nouveau_bouton_url)){
	$nb['Nouveau'] = $nouveau_bouton_url ;
	$nouveau_bouton_url = $nb;
}

if (! headers_sent()) {
	header("Content-type: text/html; charset=iso-8859-15");	
}
?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php echo($page_title) . " - Pastell"; ?></title>
		
		<meta name="description" content="Pastell est un logiciel de gestion de flux de documents. Les documents peuvent être crées via un système de formulaires configurables. Chaque document suit alors un workflow prédéfini, également configurable." />
		<meta name="keywords" content="Pastell, collectivité territoriale, flux, document, données, logiciel, logiciel libre, open source" />
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-15" />
		<meta http-equiv="X-UA-Compatible" content="chrome=1">
		<base href='<?php echo SITE_BASE ?>' />
		
		<link rel="shortcut icon" type="images/x-icon" href="favicon.ico" />
		
		<!-- bootstrap et modif LBI -->
		<link rel="stylesheet" type="text/css" href="img/commun.css" media="screen" />
		<link type="text/css" href="img/bs_css/bootstrap.css" rel="stylesheet" />
		<link type="text/css" href="img/bs_surcharge.css" rel="stylesheet" />
	
		
		<!--[if gte IE 6]>
			<link rel="stylesheet" type="text/css" href="img/style_IE6.css" media="screen" />
		<![endif]-->
		<link rel="stylesheet" href="img/jquery.autocomplete.css" type="text/css" />
		<link type="text/css" href="img/jquery-ui-1.8.10.custom.css" rel="stylesheet" />
		<link type="text/css" href="img/jquery.treeview.css" rel="stylesheet" />
		
		
		
		
		<script type="text/javascript" src="js/jquery-1.7.1.min.js"></script>
		<script type="text/javascript" src='js/jquery-ui-1.8.10.custom.min.js'></script> 
		<script type="text/javascript" src="js/jquery.autocomplete.min.js"></script>     
		<script type="text/javascript" src="js/htmlentities.js"></script>   
		<script type="text/javascript" src="js/jquery.treeview.js"></script>  
		<script type="text/javascript" src="js/pastell.js"></script>   
		


		
			
	</head>
	<body>
		<div id="global">
			<div id="header">
				<div id="bloc_logo">
					<a href='<?php echo  SITE_BASE ?>'>
						<img src="img/commun/logo_pastell.png" alt="Retour à l'accueil" />
					</a>
				</div>
				<?php if ($authentification->isConnected() ) : ?> 
					<div id="bloc_login">
						<?php if ($roleUtilisateur->hasDroit($authentification->getId(),'system:lecture',0) && $this->LastUpstart->hasWarning()): ?>
						<b style='color:red'>Le script action-automatique ne fonctionne pas</b>
						&nbsp;&nbsp;
						<?php endif;?>
						<img src="img/commun/picto_user.png" alt="" class="absmiddle" />
						<strong><a href='utilisateur/moi.php'><?php hecho($authentification->getLogin()) ?></a></strong>
						&nbsp;&nbsp;&nbsp;&nbsp;
						<img src="img/commun/picto_logout.png" alt="" class="absmiddle" />
						<a href="<?php echo  SITE_BASE ?>connexion/logout.php">Se déconnecter</a>
					</div>
				<?php endif; ?> 
			</div>
			<?php if ($authentification->isConnected() ) : ?>
				<div id="main_menu">				
					<a href="document/index.php" class="picto_flux">Accueil</a>
					<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"entite:lecture",0)) : ?>
					<a href="entite/detail.php" class="picto_utilisateurs">Administration</a>
					<?php endif;?>					
					<a href="journal/index.php" class="picto_journal">Journal des évènements</a>
					<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"role:lecture",0)) : ?>
						<a href="role/index.php" class="picto_collectivites">Rôles</a>
					<?php endif;?>
					<?php if ($roleUtilisateur->hasDroit($authentification->getId(),"system:lecture",0)) : ?>
						<a href="system/index.php" class="picto_collectivites">Environnement système</a>
					<?php endif;?>
					<a href="<?php hecho(AIDE_URL) ?>" class="picto_aide">Aide</a>
				</div>
			<?php endif; ?> 
				

			
			<ul class="breadcrumb">
				<?php if (! $breadcrumbs) : ?>
					<li class="active">Bienvenue</li>
				<?php else:?>
					<?php foreach( $breadcrumbs as $libelle) : ?>
						<li><?php echo $libelle?> <span class="divider">/</span></li>
					<?php endforeach;?>
				<?php endif;?>
			</ul>
		
		
			<div id="main">	
				<?php if ($authentification->isConnected() ) : ?>
					<div id="main_gauche">
						
						
						<h2>Documents</h2>
						<div class="menu">
							<ul>
								<li>
									<a class="dernier" href='document/index.php?id_e=<?php echo $id_e_menu ?>'>Tous</a>
								</li>
							</ul>
						</div>
						
						
							<?php 
							foreach($all_module as $type_flux => $les_flux) : ?>
								
								

								
								<h3><?php echo $type_flux  ?></h3>
								<div class="menu">
								<ul>
								<?php foreach($les_flux as $nom => $affichage) : ?>
								
								
								<?php 
								$array_keys = array_keys($les_flux);
								$last_key = end($array_keys);
								?>
								<?php
								$a_class = "";
								if($nom === $last_key) $a_class = "dernier";
								if ( $type_e_menu == $nom ) $a_class = "actif";
								
								if( ($nom === $last_key) && ($type_e_menu == $nom) ) $a_class = "actif dernier";
								?>
								
								
									
									<li>
										<a class="<?php echo $a_class ?>" href='document/list.php?type=<?php echo $nom?>&id_e=<?php echo $id_e_menu ?>'>
											<?php echo $affichage ?>
										</a>
										
									</li>
								<?php endforeach;?>
								</ul>
								</div>
							<?php endforeach;?>

				
					</div><!-- main_gauche  -->
				<?php endif;?>
					
					
					
				<div id="main_droite" >
					<div id="bloc_titre_bouton">
						<div id="bloc_h1">
						<h1><?php echo($page_title); ?></h1>
						</div>
						<?php if ($nouveau_bouton_url): ?>
							<div id="bloc_boutons">
								<?php foreach ($nouveau_bouton_url as $label => $url) : ?>
									<a class="btn " href="<?php echo $url ?>">
										<i class="icon-chevron-right"></i>
										<?php echo $label?>
									</a>
								<?php endforeach;?>
							</div>
						<?php endif;?>
					</div><!-- fin bloc_titre_bouton -->

					<?php $this->render("LastMessage");?>
					<?php $this->render($template_milieu);?>

				</div>
			</div>
		</div>
		
		<?php $this->render('Footer')?>

	</body>
</html>
<?php 
