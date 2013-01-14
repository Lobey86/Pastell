<?php 
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php"); 

$recuperateur = new Recuperateur($_GET);
$id_e_menu = $recuperateur->getInt('id_e',0);
$type_e_menu = $recuperateur->get('type',"");


$allType = array();

$allDocType = $this->FluxDefinitionFiles->getAllType();
$allDroit = $roleUtilisateur->getAllDroit($authentification->getId());

foreach($allDocType as $type_flux => $les_flux){
	foreach($les_flux as $nom => $affichage) {
		if ($roleUtilisateur->hasOneDroit($authentification->getId(),$nom.":lecture")){
			$allType[$type_flux][$nom]  = $affichage;
		}
	}
}

$breadcrumbs = array();
$entiteBC = new Entite($sqlQuery,$id_e_menu);
foreach( $entiteBC->getAncetre() as $infoEntiteBR){
	$breadcrumbs[] = $infoEntiteBR['denomination'];
}

if (! isset($nouveau_bouton_url)){
	$nouveau_bouton_url = array();
}
if (! is_array($nouveau_bouton_url)){
	$nb['Nouveau'] = $nouveau_bouton_url ;
	$nouveau_bouton_url = $nb;
}

$elapsedTime = round($timer->getElapsedTime(),3);
$infoVersionning = $versionning->getAllInfo();


header("Content-type: text/html; charset=iso-8859-15");	 ?>
<!DOCTYPE html>
<html>
	<head>
		<title><?php hecho($page_title) . " - Pastell"; ?></title>
		
		<meta name="description" content="Pastell est un logiciel de gestion de flux de documents. Les documents peuvent être crées via un système de formulaires configurables. Chaque document suit alors un workflow prédéfini, également configurable." />
		<meta name="keywords" content="Pastell, collectivité territoriale, flux, document, données, logiciel, logiciel libre, open source" />
		<meta http-equiv="content-type" content="text/html; charset=iso-8859-15" />
		
		<base href='<?php echo SITE_BASE ?>' />
		
		<link rel="shortcut icon" type="images/x-icon" href="favicon.ico" />
		<link rel="stylesheet" type="text/css" href="img/commun.css" media="screen" />
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
						<img src="img/commun/picto_user.png" alt="" class="absmiddle" />
						<strong><a href='utilisateur/moi.php'><?php hecho($authentification->getLogin()) ?></a></strong>
						&nbsp;&nbsp;|&nbsp;&nbsp;
						<img src="img/commun/picto_logout.png" alt="" class="absmiddle" />
						<a href="<?php echo  SITE_BASE ?>connexion/logout.php">Se déconnecter</a>
					</div>
				<?php endif; ?> 
			</div>
			<?php if ($authentification->isConnected() ) : ?>
				<div id="main_menu">
					<a href="document/index.php" class="picto_flux">Accueil</a>
					<a href="entite/detail.php" class="picto_utilisateurs">Administration</a>
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
				
			<div id="breadcrumb">
				<img src="img/commun/puce_geographie.png" alt="" class="absmiddle" />
				<?php if (! $breadcrumbs) : ?>
					Bienvenue
				<?php else:?>
					<?php foreach( $breadcrumbs as $libelle) : ?>
						&gt;&nbsp;<?php echo $libelle?>&nbsp;
					<?php endforeach;?>
				<?php endif;?>
			</div>
		
		
			<div id="main" class="clearfix">	
				<?php if ($authentification->isConnected() ) : ?>
					<div id="main_gauche">		
						<div class="box">
							<div class="haut"><h2>Documents</h2></div>
							<div class="cont">
							<ul>
								<li>
									<a href='document/index.php?id_e=<?php echo $id_e_menu ?>'>Tous</a>
								</li>
							</ul>
							<?php 
							foreach($allType as $type_flux => $les_flux) : ?>
								<h3><?php echo $type_flux  ?></h3>
								<ul>
								<?php foreach($les_flux as $nom => $affichage) : ?>
									<li><a href='document/list.php?type=<?php echo $nom?>&id_e=<?php echo $id_e_menu ?>'>
										<?php if ($type_e_menu == $nom) : ?>
											<b><?php echo $affichage ?></b>
										<?php else : ?>
											<?php echo $affichage ?>
										<?php endif;?>
										</a>
									</li>
								<?php endforeach;?>
								</ul>
							<?php endforeach;?>
							</div>
						</div>
						<?php 
						
						?>
					</div><!-- main_gauche  -->
				<?php endif;?>
					
				<div id="main_droite" >
					<div id="bloc_titre_bouton">
						<div id="bloc_h1">
						<h1><?php hecho($page_title); ?></h1>
						</div>
						<?php if ($nouveau_bouton_url): ?>
							<div id="bloc_boutons">
								<?php foreach ($nouveau_bouton_url as $label => $url) : ?>
									<a href="<?php echo $url ?>">
										<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
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
