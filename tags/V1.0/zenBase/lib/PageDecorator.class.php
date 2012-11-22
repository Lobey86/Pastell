<?php 

class PageDecorator {
	
	private $title;
	private $css;
	private $javascript;
	
	public function __construct($title){
		$this->title = $title;
		$this->css = array();
		$this->javascript = array();
	}
	
	public function addCSS($cssURL){
		$this->css[] = $cssURL;
	}
	
	public function addJavascript($javascriptURL){
		$this->javascript[] = $javascriptURL;
	}
	
	public function haut(){
		if (! headers_sent()) {
			header("Content-type: text/html; charset=UTF-8");
		}		
	?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>
	<head>
		<title><?php echo $this->title; ?></title>
		<?php foreach ($this->css as $css) : ?>
			<link rel="stylesheet" type="text/css" href="<?php echo $css?>" media="screen" />
		<?php endforeach;?>
		<?php foreach ($this->javascript as $javascript) : ?>
			<script type='text/javascript' src='<?php echo $javascript?>'></script>
		<?php endforeach;?>
	</head>
<body>
		<?php 
	}
	
	public function bas($timeInSeconde = null){
		if ($timeInSeconde): ?>
			<div id='bas'>Page générée en <?php echo $timeInSeconde?>s</div>
		<?php endif; ?>
		</body>
		</html>
	<?php 
	}
	
}