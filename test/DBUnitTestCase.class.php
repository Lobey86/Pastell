<?php


require_once( PASTELL_PATH . "lib/base/ZLog.class.php");
require_once( PASTELL_PATH . "lib/base/SQLQuery.class.php");

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
