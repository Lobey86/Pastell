<?php

class PageDecorator {
	
	const DEFAULT_TITLE = "Pastell";
	
	private $title;
	private $siteBase;
	private $id_u;
	private $login;
	
	private $mainMenu;
	private $breadcrumbs;
	private $menuGaucheElement;
	private $id_e;
	private $type;
	private $nouveauBouton;
	private $version;
	
	public function setTitle($title){
		$this->title = $title;
	}
	
	public function setVersion($version){
		$this->version = $version;
	}
	
	public function setSiteBase($siteBase){
		$this->siteBase = $siteBase;
	}
	
	public function setUtilisateur($id_u,$login){
		$this->id_u = $id_u;
		$this->login = $login;
	}
	
	public function addToMainMenu($name,$link,$pictogramme){
		$this->mainMenu[$name] = array('link'=>$link,'picto' => $pictogramme);
	}
	
	public function addToBreadCrumbs($libelle){
		$this->breadcrumbs[] = $libelle;
	}
	
	public function setMenuGauche(array $element,$id_e,$type){
		$this->menuGaucheElement = $element;
		$this->id_e = $id_e;
		$this->type = $type;
	}
	
	public function addNouveauBouton($nouveauBouton){
		if (! is_array($nouveauBouton)){
			$this->nouveauBouton['Nouveau'] = $nouveauBouton;
			return;
		}
		$this->nouveauBouton = $nouveauBouton ;
	}
	
	public function displayHaut(){
		header("Content-type: text/html; charset=ISO-8859-15");	 ?>
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="fr" lang="fr">
			<?php $this->displayHead();?>	
			<body>
				<div id="global">
					<?php $this->displayHeader(); ?>
					<div id="main" class="clearfix">	
						<?php if ($this->id_u) $this->displayMainGauche();?>	
						<div id="main_droite" >
							<?php $this->displayBlocTitre() ?>
		<?php 
	}

	public function displayBas($elapsedTime){?>
						</div>
					</div>
				</div>
				<?php $this->displayBottom($elapsedTime);?>		
			</body>
		</html>
	<?php 
	}
	
	private function displayHead(){ ?>
		<head>
			<title><?php echo $this->getTitle() ?></title>
			
			<meta name="description" content="Pastell est un logiciel de gestion de flux de documents. Les documents peuvent être crées via un système de formulaires configurables. Chaque document suit alors un workflow prédéfini, également configurable." />
			<meta name="keywords" content="Pastell, collectivité territoriale, flux, document, données, logiciel, logiciel libre, open source" />
			<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
			
			<base href='<?php echo $this->siteBase ?>' />
			
			<link rel="shortcut icon" type="images/x-icon" href="favicon.ico" />
			<link rel="stylesheet" type="text/css" href="img/commun.css" media="screen" />
			<!--[if gte IE 6]>
				<link rel="stylesheet" type="text/css" href="img/style_IE6.css" media="screen" />
			<![endif]-->
			<link rel="stylesheet" href="img/jquery.autocomplete.css" type="text/css" />
			<link type="text/css" href="img/jquery-ui-1.8.10.custom.css" rel="stylesheet" />
			
			<script src="javascript/jquery-1.4.4.min.js"></script>
			<script src='javascript/jquery-ui-1.8.10.custom.min.js'></script> 
			<script src="javascript/jquery.autocomplete.min.js"></script>    
			<script src="javascript/htmlentities.js"></script>   
				
		</head>
		<?php 
	}
	
	private function getTitle(){
		if ($this->title){
			return strip_tags($this->title) . " - " . self::DEFAULT_TITLE;
		}
		return self::DEFAULT_TITLE;		
	}
	
	
	private function displayHeader(){ ?>
		<div id="header">
			<div id="bloc_logo">
				<a href='<?php echo  $this->siteBase ?>'>
					<img src="img/commun/logo_pastell.png" alt="Retour à l'accueil" />
				</a>
			</div>
			<?php if ($this->id_u ) $this->displayBlocLogin(); ?>
		</div>
				
		<?php 
		if ( $this->id_u ) $this->displayMainMenu();
		$this->displayBreadCrumbs();		
	}
	
	private function displayBlocLogin(){ ?>
		<div id="bloc_login">
			<img src="img/commun/picto_user.png" alt="" class="absmiddle" />
			<strong><a href='utilisateur/moi.php'><?php echo $this->login ?></a></strong>
			&nbsp;&nbsp;|&nbsp;&nbsp;
			<img src="img/commun/picto_logout.png" alt="" class="absmiddle" />
			<a href="<?php echo  $this->siteBase ?>connexion/logout.php">Se déconnecter</a>
		</div><!-- fin bloc_login -->
		<?php 
	}
	
	private function displayMainMenu(){ ?>
		<div id="main_menu">
			<?php foreach($this->mainMenu as $name => $info) : ?>
				<a href="<?php echo $info['link']?>" class="<?php echo $info['picto']?>">
					<?php echo $name ?>
				</a>
			<?php endforeach;?>
		</div>
		<?php 
	}
	
	private function displayBreadCrumbs(){ ?>
		<div id="breadcrumb">
			<img src="img/commun/puce_geographie.png" alt="" class="absmiddle" />
			<?php if (! $this->breadcrumbs) : ?>
				Bienvenue
			<?php else:?>
				<?php foreach( $this->breadcrumbs as $libelle) : ?>
					&gt;&nbsp;<?php echo $libelle?>&nbsp;
				<?php endforeach;?>
			<?php endif;?>
		</div><!-- fin breadcrumb -->
		<?php 
	}
	
	private function displayMainGauche(){ ?>
		<div id="main_gauche">		
			<div class="box">
				<div class="haut"><h2>Documents</h2></div>
				<div class="cont">
				<ul>
					<li>
						<a href='document/index.php?id_e=<?php echo $this->id_e ?>'>Tous</a>
					</li>
				</ul>
				<?php 
				foreach($this->menuGaucheElement as $type_flux => $les_flux) : ?>
					<h3><?php echo $type_flux  ?></h3>
					<ul>
					<?php foreach($les_flux as $nom => $affichage) : ?>
						<li><a href='<?php echo  $this->siteBase ?>document/list.php?type=<?php echo $nom?>&id_e=<?php echo$this->id_e ?>'>
							<?php if ($this->type == $nom) : ?>
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
		<?php 
	}
	
	private function displayBlocTitre(){ ?>
		<div id="bloc_titre_bouton">
			<div id="bloc_h1">
			<h1><?php echo $this->title; ?></h1>
			</div>
			<?php if ($this->nouveauBouton): ?>
				<div id="bloc_boutons">
					<?php foreach ($this->nouveauBouton as $label => $url) : ?>
						<a href="<?php echo $url ?>">
							<img src="img/commun/picto_nouveau.png" alt="" class="absmiddle" />
							<?php echo $label?>
						</a>
					<?php endforeach;?>
				</div>
			<?php endif;?>
		</div><!-- fin bloc_titre_bouton -->
		<?php 
	}	

	private function displayBottom($elapsedTime){ ?>
		<div id="bottom">
			<div id="bloc_vers_haut">
			Page générée en <?php echo $elapsedTime ?>s
			</div>
			
			<div id="bloc_copyright">
				<div id="bloc_mentions">
					<p>	<a href='https://adullact.net/projects/pastell/'>Pastell</a> <?php echo $this->version ?> - Copyright <a href='http://www.sigmalis.com'>Sigmalis</a> 2010/2011 
					<br/> Logiciel distribué sous les termes de la licence <a href='http://www.cecill.info/licences/Licence_CeCILL_V2-fr.html'>CeCiLL V2</a> </p>
				</div>
				<div id="bloc_logo_adullact">
					<a href='http://www.adullact.org/'><img src="img/commun/logo_adullact.png" alt="Adullact" /></a>
				</div>
			</div>
		</div>
	<?php 
	}
}