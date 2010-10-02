<?php 
require_once( PASTELL_PATH ."/lib/flux/FluxFactory.class.php");
require_once( PASTELL_PATH. "/lib/entite/Entite.class.php");
require_once( PASTELL_PATH. "/lib/base/Recuperateur.class.php");

header("Content-type: text/html");

$recuperateur = new Recuperateur($_GET);
$id_e_menu = $recuperateur->get('id_e');

?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>PASTELL</title>

<meta name="description" content="" />
<meta name="keywords" content="" />
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<base href='<?php echo SITE_BASE ?>' />

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
		<a href='<?php echo SITE_BASE ?>'><img src="img/commun/logo_pastell.png" alt="Retour à l'accueil" /></a>
	</div>
	
	<?php if ($authentification->isConnected()) : ?>
	<div id="bloc_login">
		<img src="img/commun/picto_user.png" alt="" class="absmiddle" /><strong><?php echo $authentification->getLogin() ?></strong>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		<img src="img/commun/picto_logout.png" alt="" class="absmiddle" /><a href="<?php echo SITE_BASE ?>connexion/logout.php">Se déconnecter</a>
	</div><!-- fin bloc_login -->
	<?php endif ?>
	
</div><!-- fin header -->

<?php if ( $authentification->isConnected() ): 

?>
<div id="main_menu">
<a href="<?php echo SITE_BASE ?>index.php" class="picto_accueil">Accueil</a>
<a href="<?php echo SITE_BASE ?>entite/" class="picto_collectivites">Collectivités</a>
<a href="<?php echo SITE_BASE ?>entite/fournisseur.php" class="picto_fournisseurs">Fournisseurs</a>
<a href="<?php echo SITE_BASE ?>utilisateur/index.php" class="picto_utilisateurs">Utilisateurs</a>
<a href="<?php echo SITE_BASE ?>inscription-fournisseur/index.php" class="picto_fournisseurs">Mes informations</a>
<a href="<?php echo SITE_BASE ?>flux/index.php" class="picto_flux">Flux</a>
<a href="<?php echo SITE_BASE ?>document/index.php" class="picto_journal">Document</a>
<a href="<?php echo SITE_BASE ?>journal/index.php" class="picto_journal">Journal transactions</a>

<a href="<?php echo SITE_BASE ?>journal/index.php" class="picto_aide">Aide</a>
</div><!-- main_menu menu_2 -->
<?php endif; ?>

<?php 
$entiteBC = new Entite($sqlQuery,$id_e_menu);
$bc = $entiteBC->getBreadCrumbs() ;
?>
<div id="breadcrumb">
<img src="img/commun/puce_geographie.png" alt="" class="absmiddle" />
<?php foreach( $bc as $infoEntiteBC) : ?>
&gt;&nbsp;<?php echo $infoEntiteBC['denomination']?>&nbsp;
<?php endforeach;?>
<?php if (! $bc) : ?>
	Bienvenue
<?php endif;?>
</div><!-- fin breadcrumb -->



<div id="main" class="clearfix">

	<div id="main_gauche">
	
	
	
		<?php if ( $authentification->isConnected() ): ?>
		<div class="box">
		<div class="haut"><h2>Flux</h2></div>
		<div class="cont">
		<ul><li><a href='flux/affiche-flux.php<?php echo isset($id_e_menu)?"?id_e=$id_e_menu":''?>'>Tous</a></li></ul>
	<?php 

		$allFlux = FluxFactory::getFlux();
	
	foreach($allFlux as $type_flux => $les_flux) : ?>
	
	<h3><?php echo $type_flux  ?></h3>
		<ul>
			<?php foreach($les_flux as $nom => $affichage) : ?>
				<li><a href='<?php echo SITE_BASE ?>document/list.php?type=<?php echo $nom?><?php echo isset($id_e_menu)?"&id_e=$id_e_menu":''?>'><?php echo $affichage ?></a></li>
			<?php endforeach;?>
		</ul>
	<?php endforeach;?>
	</div>

		</div>

	
		<?php endif; ?>
	
	</div><!-- main_gauche  -->
	

<div id="main_droite" >
	

<div id="bloc_titre_bouton">
	<div id="bloc_h1">
	<h1><?php echo $page_title; ?></h1>
	</div>
	<?php if (isset($nouveau_bouton_url)): ?>
	<div id="bloc_boutons">
		<a href="<?php echo $nouveau_bouton_url ?>">
			<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />Nouveau</a>
	</div>
	<?php endif;?>
</div><!-- fin bloc_titre_bouton -->




