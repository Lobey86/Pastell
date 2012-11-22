<?php



define("BD_DSN_TEST","mysql:dbname=pastell_test;host=127.0.0.1");
define("BD_USER_TEST",BD_USER);
define("BD_PASS_TEST",BD_PASS);

class DBUnitTestCase extends UnitTestCase {
	
	protected $sqlQuery;
	
	public function __construct(){
		$zLog = new ZLog(LOG_FILE,"zenMail");
		$zLog->setLogLevel(ZLog::DEBUG);
		
		$this->sqlQuery = new SQLQuery(BD_DSN_TEST,BD_USER_TEST,BD_PASS_TEST);
		$this->sqlQuery->setLog($zLog);
		$result = $this->sqlQuery->fetchAll("SHOW TABLES");
		foreach($result as $table){
			$table = array_values($table);
			$this->sqlQuery->query("DROP TABLE ".$table[0]);
		}
	}
	
	
	
	
}
