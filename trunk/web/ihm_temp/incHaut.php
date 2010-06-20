<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
<head>
<title>PASTELL</title>

<meta name="description" content="" />
<meta name="keywords" content="" />
<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />

<link rel="shortcut icon" type="images/x-icon" href="favicon.ico" />
<link rel="stylesheet" type="text/css" href="img/commun.css" media="screen" />

</head>
<body>

<div id="global">

<div id="header">


	<div id="bloc_logo">
		<a href="index.php"><img src="img/commun/logo_pastell.png" alt="Retour à l'accueil" /></a>
	</div>
	
	<?php if ( $page != "connexion" ): ?>
	<div id="bloc_login">
		<img src="img/commun/picto_user.png" alt="" class="absmiddle" /><strong>Eric Le Champion</strong>
		&nbsp;&nbsp;|&nbsp;&nbsp;
		<img src="img/commun/picto_logout.png" alt="" class="absmiddle" /><a href="#">Se déconnecter</a>
	</div><!-- fin bloc_login -->
	<?php endif ?>
	
</div><!-- fin header -->

<?php if ( $page != "connexion" ): ?>
<div id="main_menu">
<a href="index.php" class="picto_accueil">Accueil</a>
<a href="connexion.php" class="picto_collectivites">Collectivités</a>
<a href="formulaire.php" class="picto_fournisseurs">Fournisseurs</a>
<a href="message.php" class="picto_utilisateurs">Utilisateurs</a>
<a href="#" class="picto_flux">Flux</a>
<a href="#" class="picto_journal">Journal transactions</a>
</div><!-- main_menu menu_2 -->
<?php endif; ?>

<div id="breadcrumb">
<?php if ( $page == "connexion" ): ?>
Bienvenue sur l'application PASTELL
<?php else: ?>
<img src="img/commun/puce_geographie.png" alt="" class="absmiddle" />
<a href="#">Départemnt du Nord (59)</a>&gt;
<a href="#">Centre de gestion du nord</a>&gt;
<a href="#">Communuaté d'agglomération de la porte du Hénaut</a>&gt;
<a href="#">Ville de Saint Amand les eaux</a>
<?php endif; ?>
</div><!-- fin breadcrumb -->



<div id="main" class="clearfix">

	<div id="main_gauche">
	
	
	
		<?php if ( $page != "connexion" ): ?>
		<div class="box">
			<div class="haut"><h2>Flux</h2></div>
			<div class="cont">
			
			<h3>Titre H3</h3>
				<ul>
				<li><a href="#">Lien 1</a></li>
				<li><a href="#">Lien 2</a></li>
				</ul>
				
			<h3>Titre H3</h3>
				<ul>
				<li><a href="#">Lien 1</a></li>
				<li class="item_on">element sélectionné</li>
				<li><a href="#">Lien 1</a></li>
				<li><a href="#">Lien 1</a></li>
				</ul>
			</div>

		</div>
		<?php endif; ?>
	
		
		<?php if ( $page == "accueil" ): ?>
		<div class="box_alert">
		<p>
		<strong>Champion :</strong><br/>
		Clic sur "Collectivité" pour la page de connexion<hr/>
		Clic sur "Fournisseurs" pour la page de formulaire<hr/>
		Clic sur "Utilisateur" pour la page de messages pré-formaté<hr/>
		
		
		</p>
		</div>
		<?php endif; ?>
	
	</div><!-- main_gauche  -->
	

<div id="main_droite">
	
<div id="bloc_titre_bouton">
	<div id="bloc_h1">
	<h1><?php echo $page_title; ?></h1>
	</div>
	<?php if ( $page != "connexion" ): ?>
	<div id="bloc_boutons">
	<a href="#"><img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />Nouveau</a>
	</div>
	<?php endif; ?>
</div><!-- fin bloc_titre_bouton -->


<?php if ( $page != "connexion" ): ?>
<div id="bloc_onglet">
	<a href="#" class="onglet_on">Onglet 1</a>
	<a href="#">Onglet 2 blablablba</a>
	<a href="#">Onglet 3 du champion</a>
</div>
<?php endif; ?>


<div class="box_contenu clearfix<?php if ( $page == "connexion") echo " w500"; ?>">