<?php

$cache = apc_cache_info("user");
foreach($cache['cache_list'] as $entry){
	apc_delete($entry['info']);
}

require_once(dirname(__FILE__)."/../init-authenticated.php");
$lastMessage->setLastMessage("Cache détruit et récréé");

header("Location: index.php");