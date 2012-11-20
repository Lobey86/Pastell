<?php
require_once( dirname(__FILE__) . "/../init-authenticated.php");
require_once( PASTELL_PATH . "/lib/base/Recuperateur.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentSQL.class.php");
require_once( PASTELL_PATH . "/lib/entite/AgentListHTML.class.php");
require_once( PASTELL_PATH . "/lib/helper/suivantPrecedent.php");

$recuperateur = new Recuperateur($_GET);
$offset = $recuperateur->getInt('offset',0);
$search = $recuperateur->get('search');

$droit_lecture = $roleUtilisateur->hasOneDroit($authentification->getId(),"entite:lecture");

if ( ! $droit_lecture ){
	header("Location: index.php");
	exit;
}

$agentListHTML = new AgentListHTML(true);
$agentSQL = new AgentSQL($sqlQuery);

$nbAgent = $agentSQL->getNbAllAgent($search);
$listAgent = $agentSQL->getAllAgent($search,$offset);


$page_title = "Liste des agents";
include( PASTELL_PATH ."/include/haut.php");
?>
<div>
<form action='entite/agents.php' method='get' >
	<input type='text' name='search' value='<?php echo $search?>'/>
	<input type='submit' value='Rechercher' />
</form>
</div>
<?php 


suivant_precedent($offset,AgentSQL::NB_MAX,$nbAgent,"entite/agents.php?search=$search");

$agentListHTML->display($listAgent);
include( PASTELL_PATH ."/include/bas.php");

