<?php

//http://stackoverflow.com/questions/1334613/how-to-recursively-zip-a-directory-in-php
//Remplacement des addFromString par des addFile (pb de mémoire)

class MyZipArchive {
	function zipdir($source, $destination){
		
		
	    if (!extension_loaded('zip') || !file_exists($source)) {
	        return false;
	    }
	
	    $zip = new ZipArchive();
	    if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
	        return false;
	    }
	
	    $source = str_replace('\\', '/', realpath($source));
	
	    if (is_dir($source) === true)
	    {
	        $files = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($source), RecursiveIteratorIterator::SELF_FIRST);
	
	        foreach ($files as $file)
	        {
	           	//FIXME: Ca ne marchera pas sous Windows ! 
	        	//$file = str_replace('\\', '/', $file);
	
	            // Ignore "." and ".." folders
	            if( in_array(substr($file, strrpos($file, '/')+1), array('.', '..')) )
	                continue;
	
	            $file = realpath($file);
	
	            if (is_dir($file) === true)
	            {
	                $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
	            }
	            else if (is_file($file) === true)
	            {
                	$zip->addFile($file,str_replace($source . '/', '', $file));
				}
	        }
	    }
	    else if (is_file($source) === true)
	    {
			$zip->addFile($source,basename($source));
	    }
	
	    return $zip->close();
	}
	
}
