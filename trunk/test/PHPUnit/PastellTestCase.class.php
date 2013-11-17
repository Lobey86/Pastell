<?php 
require_once 'vfsStream/vfsStream.php';
require_once "PHPUnit/Extensions/Database/TestCase.php";

abstract class PastellTestCase extends PHPUnit_Extensions_Database_TestCase {
	
	const ID_E_COL = 1;
	const ID_E_SERVICE = 2;
	const ID_U_ADMIN = 1;
	
	
	private $databaseConnection;
	private $objectInstancier;
	
	public function __construct(){
		parent::__construct();
		$this->objectInstancier = new ObjectInstancier();
		$this->objectInstancier->SQLQuery = new SQLQuery(BD_DSN_TEST,BD_USER_TEST,BD_PASS_TEST);
		$this->objectInstancier->template_path = TEMPLATE_PATH;
		$this->databaseConnection = $this->createDefaultDBConnection($this->objectInstancier->SQLQuery->getPdo(), BD_DBNAME_TEST);
	}
	
	public function reinitDatabaseOnSetup(){
		return false;
	}
	
	public function reinitFileSystemOnSetup(){
		return false;
	}
	
	
	public function getObjectInstancier(){
		return $this->objectInstancier;
	}
	
	public function reinitFileSystem(){
		$structure = array(
				'revision.txt' => '$Rev: 666 $',
				'version.txt' => '1.12',
				'workspace' => array()
		);		
		$testStream = vfsStream::setup('test',null,$structure);
		$testStreamUrl = vfsStream::url('test');
		$this->objectInstancier->versionFile = $testStreamUrl."/version.txt";
		$this->objectInstancier->revisionFile = $testStreamUrl."/revision.txt";
		$this->objectInstancier->workspacePath = $testStreamUrl."/workspace/";
	}
	
	public function reinitDatabase(){
		$this->getConnection()->createDataSet();
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
	
	protected function setUp(){
		parent::setUp();
		if ($this->reinitDatabaseOnSetup()) {
			$this->reinitDatabase();
		}
		if ($this->reinitFileSystemOnSetup()){
			$this->reinitFileSystem();
		}
	}
}