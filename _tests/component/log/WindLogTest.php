<?php
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 * tags
 */
define ('LOG_PATH', COMPILE_PATH.'/log/' );
/**
 * the last known user to change this file in the repository  <$LastChangedBy$>
 * @author Qian Su <aoxue.1988.su.qian@163.com>
 * @version $Id$ 
 * @package 
 */
class WindLogTest extends BaseTestCase {
	
	public function init() {
		require_once ('component/Log/WindLog.php');
	}
	
	public function setUp() {
		parent::setUp();
		date_default_timezone_set('UTC');
		$this->init();
	}
	
	public function tearDown() {
		parent::tearDown();
	}
	
	public function testFlushWithLog(){
		WindLog::add("test log",WindLog::TRACE);
		WindLog::add("test log again",WindLog::INFO);
		$this->assertTrue(WindLog::flush());
	}
	
	public function testFlushWithHtml(){
		WindLog::setLogDisplay(WindLog::HTML);
		WindLog::add("test log",WindLog::TRACE);
		WindLog::add("test log again",WindLog::INFO);
		$this->assertTrue(WindLog::flush());
	}
	
	public function testLog(){
		WindLog::log("test record log by log",WindLog::ERROR);
	}
	
	public function testClearFiles(){
		$this->assertTrue(WindLog::clearFiles());
	}
}