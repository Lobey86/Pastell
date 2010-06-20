<?php 
require_once( PASTELL_PATH ."/lib/flux/FluxFactory.class.php");
require_once( PASTELL_PATH. "/lib/entite/Entite.class.php");


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
<a href="<?php echo SITE_BASE ?>entite/collectivite.php" class="picto_collectivites">Collectivités</a>
<?php if ($authentification->getTypeEntite() == Entite::TYPE_COLLECTIVITE || $authentification->isAdmin()) : ?>
<a href="<?php echo SITE_BASE ?>entite/centre_de_gestion.php" class="picto_centre_gestion">Centre de gestion</a>
<a href="<?php echo SITE_BASE ?>entite/fournisseur.php" class="picto_fournisseurs">Fournisseurs</a>
<?php if ($authentification->isAdmin()) : ?>
<a href="<?php echo SITE_BASE ?>utilisateur/index.php" class="picto_utilisateurs">Utilisateurs</a>
<?php endif; ?>
<?php elseif($authentification->getTypeEntite() == Entite::TYPE_FOURNISSEUR):  ?>
<a href="<?php echo SITE_BASE ?>inscription-fournisseur/index.php" class="picto_fournisseurs">Mes informations</a>
<?php endif; ?>
<a href="<?php echo SITE_BASE ?>flux/index.php" class="picto_flux">Flux</a>
<a href="<?php echo SITE_BASE ?>journal/index.php" class="picto_journal">Journal transactions</a>

<a href="<?php echo SITE_BASE ?>journal/index.php" class="picto_aide">Aide</a>
</div><!-- main_menu menu_2 -->
<?php endif; ?>

<?php 
$bc = array();
if ( $authentification->getBreadCrumbs()) {
	$bc = $authentification->getBreadCrumbs();
} elseif (isset($siren)){
	$entite = new Entite($sqlQuery,$siren);
	$bc = $entite->getBreadCrumbs() ;
}	
?>
<div id="breadcrumb">
<img src="img/commun/puce_geographie.png" alt="" class="absmiddle" />
<?php foreach( $bc as $infoEntite) : ?>
	<?php if ($authentification->isAdmin()) : ?>
		<a href='entite/detail.php?siren=<?php echo $infoEntite['siren']?>'>
	<?php endif;?>
&gt;&nbsp;<?php echo $infoEntite['denomination']?>&nbsp;
	<?php if ($authentification->isAdmin()) : ?>
</a>
	<?php endif;?>
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
		<ul><li><a href='flux/affiche-flux.php<?php echo isset($siren)?"?siren=$siren":''?>'>Tous</a></li></ul>
	<?php 
	if (! $authentification->isAdmin()){
		$allFlux = FluxFactory::getFluxByEntite($infoEntite);
	} else {
		$allFlux = FluxFactory::getFlux();
	}
	
	foreach($allFlux as $type_flux => $les_flux) : ?>
	
	<h3><?php echo $type_flux  ?></h3>
		<ul>
			<?php foreach($les_flux as $nom => $affichage) : ?>
				<li><a href='<?php echo SITE_BASE ?>flux/affiche-flux.php?flux=<?php echo $nom?><?php echo isset($siren)?"&siren=$siren":''?>'><?php echo $affichage ?></a></li>
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




