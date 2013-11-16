<?php 
require_once 'vfsStream/vfsStream.php';
require_once "PHPUnit/Extensions/Database/TestCase.php";

abstract class PastellTestCase extends PHPUnit_Extensions_Database_TestCase {
	
	private $databaseConnection;
	private $objectInstancier;
	
	public function __construct(){
		parent::__construct();
		
		$this->objectInstancier = new ObjectInstancier();
		
		$testStream = vfsStream::setup('test');
		$testStreamUrl = vfsStream::url('test');
		$revision_path = $testStreamUrl."/revision.txt";
		$version_path  = $testStreamUrl."/version.txt";
		file_put_contents($revision_path ,'$Rev: 666 $');
		file_put_contents($version_path, '1.12');
		
		$this->objectInstancier->versionFile = $version_path;
		$this->objectInstancier->revisionFile = $revision_path;
		
		$this->objectInstancier->SQLQuery = new SQLQuery(BD_DSN_TEST,BD_USER_TEST,BD_PASS_TEST);
		
		$this->objectInstancier->template_path = TEMPLATE_PATH;
		
		$this->databaseConnection = $this->createDefaultDBConnection($this->objectInstancier->SQLQuery->getPdo(), BD_DBNAME_TEST);
	}
	
	public function getObjectInstancier(){
		return $this->objectInstancier;
	}
	
	/**
	 * @return PHPUnit_Extensions_Database_DB_IDatabaseConnection
	 */
	public function getConnection() {
		return $this->databaseConnection;
	}
	
	/**
	 * @return PHPUnit_Extensions_Database_DataSet_IDataSet
	 */
	public function getDataSet() {
		return new PHPUnit_Extensions_Database_DataSet_YamlDataSet( __DIR__."/database_data.yml");
	}
	
	public function setUpWithDBReinit(){
		parent::setUp();
		$this->getConnection()->createDataSet();
	}
}