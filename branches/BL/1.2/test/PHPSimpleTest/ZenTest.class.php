<?php
require_once('simpletest/unit_tester.php');
require_once ("spikephpcoverage/CoverageRecorder.php");
require_once ("spikephpcoverage/reporter/HtmlCoverageReporter.php");

class ZenTest extends TestSuite {
	
	private $directory;
	private $reporter;
	private $includePath;
	private $excludePath;
	
    public function __construct($directory) {
		$this->directory = $directory;
    	$this->setReporter(new DefaultReporter());
    	$this->includePath = array();
    	$this->excludePath = array();
    }
    
    public function setReporter($reporter){
    	$this->reporter = $reporter;
    }
    
    public function run(){
    	$this->TestSuite('Test');
        $dirHandle = opendir($this->directory);
		while ($file = readdir($dirHandle)) {
			$file = basename($file);
         	if (substr($file,-14) == "Test.class.php") {
        		$this->addFile($this->directory."/".$file);
			}
		}
    	parent::run($this->reporter);
    }
    
    public function addToIncludePath($path){
    	$this->includePath[] = $path;
    }
    
    public function addToExludePath($path){
    	$this->excludePath[] = $path;
    }
    
    public function coverage(){

		$reporter = new HtmlCoverageReporter("Couverture de code", "", "report");

		$cov = new CoverageRecorder($this->includePath, $this->excludePath, $reporter);
		$cov->startInstrumentation();
		$this->setReporter(new SimpleReporter());
		$this->run();
		$cov->stopInstrumentation();	
		$cov->generateReport();
    }
}