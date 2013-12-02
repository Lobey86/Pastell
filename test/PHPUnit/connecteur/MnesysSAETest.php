<?php 
require_once __DIR__.'/../init.php';

require_once PASTELL_PATH."/connecteur/MnesysSAE/MnesysSAE.class.php";

class MnesysSAETest extends PastellTestCase  {

	//VfsStream ne fonctionne pas avec ZipArchiver ...
	public function testGenerateArchive(){
		$tmpFolder = new TmpFolder();
		$test_folder = $tmpFolder->create();
		file_put_contents($test_folder."/ar-actes.xml", "<test>");
		
		$tmpFile = new TmpFile();
		$mnesysSAE = new MnesysSAE($tmpFile);
		$archive_path = $mnesysSAE->generateArchive(false, $test_folder);
		
		$this->assertTrue(file_exists($archive_path));
		
		$zipArchive = new ZipArchive();
		$zipArchive->open($archive_path);
		
		$this->assertEquals(1, $zipArchive->numFiles);
		$info = $zipArchive->statIndex(0);
		$this->assertEquals("ar-actes.xml", basename($info['name']));
		
		
		$tmpFolder->delete($test_folder);
		$tmpFile->delete($archive_path);
		
	}
}