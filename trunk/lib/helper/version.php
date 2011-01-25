<?php


function get_version(){
	$version = file_get_contents(dirname(__FILE__)."/../../version.txt");
	$revision = file_get_contents(dirname(__FILE__)."/../../revision.txt");
	foreach(explode("\n",$revision) as $line){
		if (preg_match('#^\$Rev: (\d*) \$#',$line,$matches)){
			return "Version $version - Révision " .$matches[1];
		}
	}

}